<?php

/**
 * LICENSE
 *
 * This source file is subject to the GNU General Public License, Version 3
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @package    BitsyBay Engine
 * @copyright  Copyright (c) 2015 The BitsyBay Project (http://bitsybay.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License, Version 3
 */

final class Auth {

    private $_user_id    = 0;
    private $_file_quota = 0;
    private $_approved   = false;
    private $_username   = false;
    private $_email      = false;
    private $_status     = false;
    private $_verified   = false;
    private $_date_added = false;

    private $_db;
    private $_url;
    private $_mail;
    private $_load;
    private $_request;
    private $_session;

    public function __construct(Registry $registry) {

        // Registry
        $this->_db      = $registry->get('db');
        $this->_url     = $registry->get('url');
        $this->_mail    = $registry->get('mail');
        $this->_load    = $registry->get('load');
        $this->_request = $registry->get('request');
        $this->_session = $registry->get('session');

        // If user has id
        if ($this->_session->getUserId()) {

            // Find Customer in Database
            try {
                $statement = $this->_db->prepare('SELECT    `user_id`,
                                                            `file_quota`,
                                                            `status`,
                                                            `verified`,
                                                            `username`,
                                                            `date_added`,
                                                            `email`,
                                                            `approved`

                                                    FROM  `user`
                                                    WHERE `user_id` = ?
                                                    AND   `status` = 1
                                                    LIMIT 1');

                $statement->execute(array($this->_session->getUserId()));

            } catch (PDOException $e) {

                if ($this->_db->inTransaction()) {
                    $this->_db->rollBack();
                }

                trigger_error($e->getMessage());

            }

            if ($statement->rowCount()) {

                $user = $statement->fetch();

                $this->_user_id     = $user->user_id;
                $this->_file_quota  = $user->file_quota;
                $this->_approved    = $user->approved;
                $this->_username    = $user->username;
                $this->_email       = $user->email;
                $this->_status      = $user->status;
                $this->_verified    = $user->verified;
                $this->_date_added  = $user->date_added;

                // Set last visit date
                $this->_setVisitDate($user->user_id);

                // Set active language
                $this->_saveLanguage($user->user_id);

            } else {
                $this->logout();
            }
        }
    }

    /**
    * Register last visit
    *
    * @param int $user_id
    * @return int|bool Return affected int row, or bool true if row already exists or false if throw exception
    */
    private function _setVisitDate($user_id) {

        try {

            $statement = $this->_db->prepare('UPDATE `user` SET `date_visit` = NOW() WHERE `user_id` = ? LIMIT 1');
            $statement->execute(array($user_id));

            return $statement->rowCount();

        } catch (PDOException $e) {

            if ($this->_db->inTransaction()) {
                $this->_db->rollBack();
            }

            trigger_error($e->getMessage());

            return false;
        }
    }

    /**
    * Register active language
    *
    * @param int $user_id
    * @return int|bool Return affected int row, or bool true if row already exists or false if throw exception
    */
    private function _saveLanguage($user_id) {

        // Get current language
        if (isset($this->_request->get['language_id'])) {
            $language_id = (int) $this->_request->get['language_id'];
        } else if (isset($this->_request->cookie['language_id'])) {
            $language_id = (int) $this->_request->cookie['language_id'];
        } else {
            $language_id = (int) DEFAULT_LANGUAGE_ID;
        }

        $statement = $this->_db->prepare('SELECT * FROM `language` WHERE `language_id` = ? LIMIT 1');
        $statement->execute(array($language_id));

        if ($statement->rowCount()) {
            $statement = $this->_db->prepare('UPDATE `user` SET `language_id` = ? WHERE `user_id` = ? LIMIT 1');
            $statement->execute(array($language_id, $user_id));
        }
    }

    /**
    * Login user
    *
    * @param string $login Username or Email
    * @param string $password Raw password string
    * @param bool $login_is_email FALSE if login is username or TRUE if login is email (by default)
    * @return bool Returns TRUE if success or FALSE if something wrong
    */
    public function login($login, $password, $login_is_email = true) {

        try {
            // Login by email
            if ($login_is_email) {
                $statement = $this->_db->prepare('SELECT *  FROM  `user`
                                                            WHERE `email`    = LOWER(:email) AND
                                                                  `password` = SHA1(CONCAT(`salt`, SHA1(CONCAT(`salt`, SHA1(:password))))) AND
                                                                  `status`   = 1
                                                            LIMIT 1');

                $statement->execute(array(
                    ':password' => $password,
                    ':email'    => $login));

            // Login by username
            } else {
                $statement = $this->_db->prepare('SELECT *  FROM  `user`
                                                            WHERE `username` = :username AND
                                                                  `password` = SHA1(CONCAT(`salt`, SHA1(CONCAT(`salt`, SHA1(:password))))) AND
                                                                  `status`   = 1
                                                            LIMIT 1');

                $statement->execute(array(
                    ':password' => $password,
                    ':username' => $login));
            }

        } catch (PDOException $e) {

            if ($this->_db->inTransaction()) {
                $this->_db->rollBack();
            }

            trigger_error($e->getMessage());

            return false;
        }


        // Update Items
        if ($statement->rowCount()) {

            $user = $statement->fetch();

            $this->_session->setUserId($user->user_id);

            // Update Global Variables
            $this->_user_id     = $user->user_id;
            $this->_approved    = $user->approved;
            $this->_file_quota  = $user->file_quota;
            $this->_username    = $user->username;
            $this->_email       = $user->email;
            $this->_status      = $user->status;
            $this->_verified    = $user->verified;
            $this->_date_added  = $user->date_added;

            return true;

        } else {

            return false;
        }
    }

    /**
    * Logout user
    *
    * @return bool Returns TRUE if success or FALSE if throw exception
    */
    public function logout() {

        // Remove Session
        $this->_session->setUserId();

        // Update Variables
        $this->_user_id    = 0;
        $this->_file_quota = 0;
        $this->_approved   = false;
        $this->_username   = false;
        $this->_email      = false;
        $this->_status     = false;
        $this->_verified   = false;
        $this->_date_added = false;

        return true;

    }

    /**
    * Check if user is already logged
    *
    * @return bool TRUE if logged or FALSE if guest
    */
    public function isLogged() {
        return (bool) $this->_user_id;
    }

    /**
    * Check if user is approved
    *
    * @return bool TRUE if approved or FALSE if unapproved
    */
    public function isApproved() {
        return (bool) $this->_approved;
    }

    /**
    * Check if user is active
    *
    * @return bool TRUE if active or FALSE if unapproved
    */
    public function isActive() {
        return (bool) $this->_status;
    }

    /**
    * Check if user is verified
    *
    * @return bool TRUE if verified or FALSE if unapproved
    */
    public function isVerified() {
        return (bool) $this->_verified;
    }

    /**
    * Get user id
    *
    * @return int
    */
    public function getId() {
        return (int) $this->_user_id;
    }

    /**
    * Get file quota
    *
    * @return int file_quota
    */
    public function getFileQuota() {
        return (int) $this->_file_quota;
    }

    /**
    * Get username
    *
    * @return string
    */
    public function getUsername() {
        return (string) $this->_username;
    }

    /**
    * Get email
    *
    * @return string email
    */
    public function getEmail() {
        return (string) $this->_email;
    }

    /**
    * Get date added
    *
    * @return string mysql datetime
    */
    public function getDateAdded() {
        return $this->_date_added;
    }
}
