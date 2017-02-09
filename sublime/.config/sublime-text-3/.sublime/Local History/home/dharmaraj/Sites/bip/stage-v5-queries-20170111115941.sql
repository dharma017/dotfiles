UPDATE `mysql`.`proc` p SET definer = 'bip_dbuser@localhost' WHERE definer='bip_dbuser@%' AND db='bip'
UPDATE `mysql`.`proc` p SET definer = 'bip_dbuser@localhost' WHERE definer='wpuser@%' AND db='bip'

ALTER TABLE `bip_user`
ADD `created_by` int(12) NOT NULL AFTER `user_role`;

ALTER TABLE `bip_user`
CHANGE `difficulty_id` `difficulty_id` text NULL AFTER `last_name`,
CHANGE `group_id` `group_id` text NULL AFTER `num_login`;

DELIMITER ;;
CREATE PROCEDURE `addUser_adminer_57f21bafe5734` (OUT `result` int, IN `username` varchar(255), IN `contact_number` varchar(255), IN `contact_number_1` varchar(255), IN `sms_notify` int, IN `email` varchar(250), IN `password1` varchar(250), IN `firstName` varchar(250), IN `lastName` varchar(250), IN `difficulty` int, IN `address` varchar(250), IN `city` varchar(250), IN `group_id` varchar(12), IN `email_notify` int, IN `psychologist_id` varchar(50), IN `user_role` int(3), IN `created_by` int(12), IN `communication` int(3), IN `permission` text, IN `activefrom` date, IN `activeto` date, IN `published` int, IN `lang_id` int, IN `app_status` tinyint, IN `app_web_version` tinyint, IN `patient_access` tinyint, IN `patient_access_create` tinyint, IN `visitor_user_id` int, IN `visitor_user_role` int, IN `visitor_user_name` varchar(255), IN `visitor_full_name` varchar(255), IN `visitor_ip_address` varchar(255), IN `visitor_comment` varchar(255))
BEGIN
	INSERT INTO bip_user SET username=username, contact_number=contact_number,contact_number_1=contact_number_1, sms_notify=sms_notify, email=email, PASSWORD=password1, first_name=firstName, last_name=lastName,  difficulty_id=difficulty,  address=address, city=city, group_id=group_id,email_notify = email_notify,psychologist_id=psychologist_id, user_role=user_role,created_by=created_by,communication=communication,  permission = permission, STATUS=published, join_date=NOW(),active_from=activefrom,active_to=activeto,lang_id=lang_id,app_status=app_status,app_web_version=app_web_version,patient_access=patient_access,patient_access_create=patient_access_create,visitor_user_id=visitor_user_id,visitor_user_role=visitor_user_role,visitor_user_name=visitor_user_name,visitor_full_name=visitor_full_name,visitor_ip_address=visitor_ip_address,visitor_comment=visitor_comment;
	SELECT LAST_INSERT_ID() INTO result;


		 END;;
DELIMITER ;
DROP PROCEDURE `addUser_adminer_57f21bafe5734`;
DROP PROCEDURE `addUser`;
DELIMITER ;;
CREATE PROCEDURE `addUser` (OUT `result` int, IN `username` varchar(255), IN `contact_number` varchar(255), IN `contact_number_1` varchar(255), IN `sms_notify` int, IN `email` varchar(250), IN `password1` varchar(250), IN `firstName` varchar(250), IN `lastName` varchar(250), IN `difficulty` int, IN `address` varchar(250), IN `city` varchar(250), IN `group_id` varchar(12), IN `email_notify` int, IN `psychologist_id` varchar(50), IN `user_role` int(3), IN `created_by` int(12), IN `communication` int(3), IN `permission` text, IN `activefrom` date, IN `activeto` date, IN `published` int, IN `lang_id` int, IN `app_status` tinyint, IN `app_web_version` tinyint, IN `patient_access` tinyint, IN `patient_access_create` tinyint, IN `visitor_user_id` int, IN `visitor_user_role` int, IN `visitor_user_name` varchar(255), IN `visitor_full_name` varchar(255), IN `visitor_ip_address` varchar(255), IN `visitor_comment` varchar(255))
BEGIN
	INSERT INTO bip_user SET username=username, contact_number=contact_number,contact_number_1=contact_number_1, sms_notify=sms_notify, email=email, PASSWORD=password1, first_name=firstName, last_name=lastName,  difficulty_id=difficulty,  address=address, city=city, group_id=group_id,email_notify = email_notify,psychologist_id=psychologist_id, user_role=user_role,created_by=created_by,communication=communication,  permission = permission, STATUS=published, join_date=NOW(),active_from=activefrom,active_to=activeto,lang_id=lang_id,app_status=app_status,app_web_version=app_web_version,patient_access=patient_access,patient_access_create=patient_access_create,visitor_user_id=visitor_user_id,visitor_user_role=visitor_user_role,visitor_user_name=visitor_user_name,visitor_full_name=visitor_full_name,visitor_ip_address=visitor_ip_address,visitor_comment=visitor_comment;
	SELECT LAST_INSERT_ID() INTO result;


		 END;;
DELIMITER ;


DELIMITER ;;
CREATE PROCEDURE `updateUser_adminer_57f21c0d9175d` (IN `userId` int, IN `user_name` varchar(255), IN `contact_number` varchar(255), IN `contact_number_1` varchar(255), IN `sms_notify` bool, IN `eMail` varchar(250), IN `password1` varchar(250), IN `firstName` varchar(250), IN `lastName` varchar(250), IN `difficulty` int, IN `address` varchar(250), IN `city` varchar(250), IN `group_id` varchar(12), IN `email_notify` int, IN `psychologist_id` varchar(50), IN `user_role` int(3), IN `created_by` int(12), IN `communication` int(3), IN `permission` text, IN `activefrom` date, IN `activeto` date, IN `published` int, IN `lang_id` int, IN `app_status` tinyint, IN `app_web_version` tinyint, IN `patient_access` tinyint, IN `patient_access_create` tinyint, IN `visitor_user_id` int, IN `visitor_user_role` int, IN `visitor_user_name` varchar(255), IN `visitor_full_name` varchar(255), IN `visitor_ip_address` varchar(255), IN `visitor_comment` varchar(255))
BEGIN
	UPDATE bip_user SET username=user_name, contact_number=contact_number,contact_number_1=contact_number_1, sms_notify=sms_notify, email=eMail, PASSWORD=password1, first_name=firstName, last_name=lastName,difficulty_id=difficulty, address=address, city=city,group_id=group_id, email_notify = email_notify,psychologist_id=psychologist_id, user_role=user_role, created_by=created_by,communication=communication, permission=permission,active_from=activefrom, active_to=activeto, STATUS=published,lang_id=lang_id,app_status=app_status,app_web_version=app_web_version,patient_access=patient_access,patient_access_create=patient_access_create,visitor_user_id=visitor_user_id,visitor_user_role=visitor_user_role,visitor_user_name=visitor_user_name,visitor_full_name=visitor_full_name,visitor_ip_address=visitor_ip_address,visitor_comment=visitor_comment WHERE id=userId;

		END;;
DELIMITER ;
DROP PROCEDURE `updateUser_adminer_57f21c0d9175d`;
DROP PROCEDURE `updateUser`;
DELIMITER ;;
CREATE PROCEDURE `updateUser` (IN `userId` int, IN `user_name` varchar(255), IN `contact_number` varchar(255), IN `contact_number_1` varchar(255), IN `sms_notify` bool, IN `eMail` varchar(250), IN `password1` varchar(250), IN `firstName` varchar(250), IN `lastName` varchar(250), IN `difficulty` int, IN `address` varchar(250), IN `city` varchar(250), IN `group_id` varchar(12), IN `email_notify` int, IN `psychologist_id` varchar(50), IN `user_role` int(3), IN `created_by` int(12), IN `communication` int(3), IN `permission` text, IN `activefrom` date, IN `activeto` date, IN `published` int, IN `lang_id` int, IN `app_status` tinyint, IN `app_web_version` tinyint, IN `patient_access` tinyint, IN `patient_access_create` tinyint, IN `visitor_user_id` int, IN `visitor_user_role` int, IN `visitor_user_name` varchar(255), IN `visitor_full_name` varchar(255), IN `visitor_ip_address` varchar(255), IN `visitor_comment` varchar(255))
BEGIN
	UPDATE bip_user SET username=user_name, contact_number=contact_number,contact_number_1=contact_number_1, sms_notify=sms_notify, email=eMail, PASSWORD=password1, first_name=firstName, last_name=lastName,difficulty_id=difficulty, address=address, city=city,group_id=group_id, email_notify = email_notify,psychologist_id=psychologist_id, user_role=user_role, created_by=created_by,communication=communication, permission=permission,active_from=activefrom, active_to=activeto, STATUS=published,lang_id=lang_id,app_status=app_status,app_web_version=app_web_version,patient_access=patient_access,patient_access_create=patient_access_create,visitor_user_id=visitor_user_id,visitor_user_role=visitor_user_role,visitor_user_name=visitor_user_name,visitor_full_name=visitor_full_name,visitor_ip_address=visitor_ip_address,visitor_comment=visitor_comment WHERE id=userId;

		END;;
DELIMITER ;

UPDATE `bip_step` SET `description` = replace(description, 'https://barninternetprojektet.se', 'https://bip.zapto.org');
UPDATE `bip_user_tracking` SET `exit_page` = replace(exit_page, 'https://barninternetprojektet.se', 'https://bip.zapto.org');

DROP TABLE bip_access_activity;

# Indexing tables
ALTER TABLE `bip_app_comments` ADD INDEX(`user_id`);
ALTER TABLE `bip_app_comments` ADD INDEX(`psychologist_id`);
ALTER TABLE `bip_app_comments` ADD INDEX(`task_id`);
ALTER TABLE `bip_app_comments` ADD INDEX(`message_id`);
ALTER TABLE `bip_app_comments` ADD INDEX(`status_new`);
ALTER TABLE `bip_app_comments` ADD INDEX(`usertype`);

ALTER TABLE `bip_download` ADD INDEX(`sub_step_id`);

ALTER TABLE `bip_faq` ADD INDEX(`lang_id`);
ALTER TABLE `bip_faq` ADD INDEX(`published`);

ALTER TABLE `bip_form` ADD INDEX(`status`);
ALTER TABLE `bip_form` ADD INDEX(`fld_name`);

ALTER TABLE `bip_form_data` ADD INDEX(`step_id`);
ALTER TABLE `bip_form_data` ADD INDEX(`user_id`);

ALTER TABLE `bip_login_activity` ADD INDEX(`user_id`);
ALTER TABLE `bip_login_activity` ADD INDEX(`user_role`);
ALTER TABLE `bip_login_activity` ADD INDEX(`login_at`);

ALTER TABLE `bip_message` ADD INDEX(`sender_id`);
ALTER TABLE `bip_message` ADD INDEX(`receiver_id`);
ALTER TABLE `bip_message` ADD INDEX(`status_receiver`);
ALTER TABLE `bip_message` ADD INDEX(`status_sender`);
ALTER TABLE `bip_message` ADD INDEX(`message_type`);
ALTER TABLE `bip_message` ADD INDEX(`is_app`);
ALTER TABLE `bip_message` ADD INDEX(`task_id`);
ALTER TABLE `bip_message` ADD INDEX(`sms_notify`);
ALTER TABLE `bip_message` ADD INDEX(`email_notify`);
ALTER TABLE `bip_message` ADD INDEX(`notify_now`);
ALTER TABLE `bip_message` ADD INDEX(`patient_inbox`);
ALTER TABLE `bip_message` ADD INDEX(`lang_id`);

ALTER TABLE `bip_message_pending` ADD INDEX(`sms_notify`);
ALTER TABLE `bip_message_pending` ADD INDEX(`email_notify`);
ALTER TABLE `bip_message_pending` ADD INDEX(`lang_id`);

ALTER TABLE `bip_page_views` ADD INDEX(`psychologist_id`);
ALTER TABLE `bip_page_views` ADD INDEX(`group_id`);
ALTER TABLE `bip_page_views` ADD INDEX(`user_id`);
ALTER TABLE `bip_page_views` ADD INDEX(`stage_id`);
ALTER TABLE `bip_page_views` ADD INDEX(`step_id`);
ALTER TABLE `bip_page_views` ADD INDEX(`lang_id`);

ALTER TABLE `bip_psychologist_activity` ADD INDEX(`psychologist_id`);
ALTER TABLE `bip_psychologist_activity` ADD INDEX(`group_id`);
ALTER TABLE `bip_psychologist_activity` ADD INDEX(`user_id`);
ALTER TABLE `bip_psychologist_activity` ADD INDEX(`lang_id`);

ALTER TABLE `bip_psychologist_log` ADD INDEX(`psychologist_id`);
ALTER TABLE `bip_psychologist_log` ADD INDEX(`user_id`);
ALTER TABLE `bip_psychologist_log` ADD INDEX(`lang_id`);

ALTER TABLE `bip_registration_assignments_details` ADD INDEX(`assignment_id`);
ALTER TABLE `bip_registration_assignments_details` ADD INDEX(`registration_id`);
ALTER TABLE `bip_registration_assignments_details` ADD INDEX(`flow_id`);
ALTER TABLE `bip_registration_assignments_details` ADD INDEX(`step_id`);
ALTER TABLE `bip_registration_assignments_details` ADD INDEX(`answer_id`);

ALTER TABLE `bip_step` ADD INDEX(`published`);

ALTER TABLE `bip_tasks` ADD INDEX(`difficulty_id`);
ALTER TABLE `bip_tasks` ADD INDEX(`problem_id`);
ALTER TABLE `bip_tasks` ADD INDEX(`type`);
ALTER TABLE `bip_tasks` ADD INDEX(`tag`);
ALTER TABLE `bip_tasks` ADD INDEX(`is_deleted`);

ALTER TABLE `bip_tics_v1_ratings` ADD INDEX(`tic_id`);
ALTER TABLE `bip_tics_v1_ratings` ADD INDEX(`level_id`);
ALTER TABLE `bip_tics_v1_ratings` ADD INDEX(`user_id`);
ALTER TABLE `bip_tics_v1_ratings` ADD INDEX(`is_stop_rating`);

ALTER TABLE `bip_training_app` ADD INDEX(`user_id`);
ALTER TABLE `bip_training_app` ADD INDEX(`task_id`);
ALTER TABLE `bip_training_app` ADD INDEX(`status`);
ALTER TABLE `bip_training_app` ADD INDEX(`practice`);
ALTER TABLE `bip_training_app` ADD INDEX(`type`);
ALTER TABLE `bip_training_app` ADD INDEX(`edited`);

ALTER TABLE `bip_user` ADD INDEX(`sms_notify`);
ALTER TABLE `bip_user` ADD INDEX(`communication`);
ALTER TABLE `bip_user` ADD INDEX(`email_notify`);
ALTER TABLE `bip_user` ADD INDEX(`psychologist_id`);
ALTER TABLE `bip_user` ADD INDEX(`user_role`);
ALTER TABLE `bip_user` ADD INDEX(`status`);
ALTER TABLE `bip_user` ADD INDEX(`app_status`);
ALTER TABLE `bip_user` ADD INDEX(`is_deleted`);

ALTER TABLE `bip_user_activity` ADD INDEX(`user_id`);
ALTER TABLE `bip_user_activity` ADD INDEX(`stage_id`);
ALTER TABLE `bip_user_activity` ADD INDEX(`status`);
ALTER TABLE `bip_user_activity` ADD INDEX(`lang_id`);

ALTER TABLE `bip_user_sms` ADD INDEX(`user_id`);
ALTER TABLE `bip_user_sms` ADD INDEX(`user_role`);
ALTER TABLE `bip_user_sms` ADD INDEX(`activate`);
ALTER TABLE `bip_user_sms` ADD INDEX(`lang_id`);

ALTER TABLE `bip_user_tracking` ADD INDEX(`user_id`);

ALTER TABLE `bip_worksheet_comments` ADD INDEX(`worksheet_id`);
ALTER TABLE `bip_worksheet_comments` ADD INDEX(`user_id`);
ALTER TABLE `bip_worksheet_comments` ADD INDEX(`wc_status`);
ALTER TABLE `bip_worksheet_comments` ADD INDEX(`lang_id`);

ALTER TABLE `bip_worksheet_notification` ADD INDEX(`worksheet_id`);
ALTER TABLE `bip_worksheet_notification` ADD INDEX(`user_id`);
ALTER TABLE `bip_worksheet_notification` ADD INDEX(`ws_status`);
ALTER TABLE `bip_worksheet_notification` ADD INDEX(`lang_id`);

ALTER TABLE `_revision_bip_user` ADD INDEX(`user_role`);

# Remove duplicate index using pt-duplicate-key-checker
ALTER TABLE `bip`.`bip_user_sms` DROP INDEX `user_id`;
ALTER TABLE `bip`.`bip_user_tracking` DROP INDEX `user_id`;

# Convert all Tables of one or more Database(s) from MyISAM to InnoDB and vice-versa

-- SELECT CONCAT('ALTER TABLE ', TABLE_SCHEMA, '.', TABLE_NAME,' ENGINE=InnoDB;') 
-- FROM Information_schema.TABLES WHERE TABLE_SCHEMA = 'bip' AND ENGINE = 'MyISAM' AND TABLE_TYPE = 'BASE TABLE';

ALTER TABLE bip.bip_app_comments ENGINE=InnoDB;
ALTER TABLE bip.bip_auto_message ENGINE=InnoDB;
ALTER TABLE bip.bip_bass ENGINE=InnoDB;
ALTER TABLE bip.bip_bass_query ENGINE=InnoDB;
ALTER TABLE bip.bip_faq ENGINE=InnoDB;
ALTER TABLE bip.bip_form_data ENGINE=InnoDB;
ALTER TABLE bip.bip_language ENGINE=InnoDB;
ALTER TABLE bip.bip_menu ENGINE=InnoDB;
ALTER TABLE bip.bip_notify_app ENGINE=InnoDB;
ALTER TABLE bip.bip_pages ENGINE=InnoDB;
ALTER TABLE bip.bip_problem_assign ENGINE=InnoDB;
ALTER TABLE bip.bip_push_reminder ENGINE=InnoDB;
ALTER TABLE bip.bip_registration_answers ENGINE=InnoDB;
ALTER TABLE bip.bip_registration_custom_answer_category ENGINE=InnoDB;
ALTER TABLE bip.bip_registration_custom_answers ENGINE=InnoDB;
ALTER TABLE bip.bip_registration_flow_page ENGINE=InnoDB;
ALTER TABLE bip.bip_registration_task ENGINE=InnoDB;
ALTER TABLE bip.bip_training_app ENGINE=InnoDB;
ALTER TABLE bip.bip_training_comments ENGINE=InnoDB;
ALTER TABLE bip.bip_user_activity ENGINE=InnoDB;Step 4: On the Port Forwarding page enter in a name for your device like, “Camera”. Then enter the port you are forwarding in the port field. Select “TCP/UDP” or “Both” under Protocol if you are unsure which protocol you are using. Next, enter in the internal IP address of the device you are port forwarding to and click “Apply” or “Save” to store the changes.

ALTER TABLE bip.bip_user_tracking ENGINE=InnoDB;
ALTER TABLE bip.bip_v2_sk_exposure_master ENGINE=InnoDB;
ALTER TABLE bip.bip_v2_skill_exposure_answers ENGINE=InnoDB;
ALTER TABLE bip.bip_weekly_training ENGINE=InnoDB;
ALTER TABLE bip.bip_worksheet_comments ENGINE=InnoDB;
ALTER TABLE bip.ci_sessions ENGINE=InnoDB;

# Configure foreign keys with reference integrity to avoid faulty data
ALTER TABLE `bip_stage`
ADD FOREIGN KEY (`difficulty_id`) REFERENCES `bip_difficulty` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `bip_download`
DROP FOREIGN KEY `FK_bip_download`,
ADD FOREIGN KEY (`step_id`) REFERENCES `bip_step` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

DELETE
  FROM bip_link
 WHERE NOT EXISTS (
                   SELECT *
                     FROM bip_step AS T1
                    WHERE T1.id = bip_link.step_id
                  );

ALTER TABLE `bip_link`
ADD FOREIGN KEY (`step_id`) REFERENCES `bip_step` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

# New feature
ALTER TABLE `bip_admin_user`
ADD `error_notify` tinyint(4) NULL DEFAULT '0' AFTER `status`;

ALTER TABLE `_revision_bip_admin_user`
ADD `error_notify` tinyint(4) NULL DEFAULT '0' AFTER `status`;

ALTER TABLE `bip_login_activity`
CHANGE `sms_code_in` `sms_code_in` varchar(255) COLLATE 'utf8_unicode_ci' NULL AFTER `sms_code`;


ALTER TABLE `bip_login_activity`
CHANGE `sms_code` `sms_code` varchar(255) COLLATE 'utf8_unicode_ci' NULL AFTER `sms_attempt`;

DELETE FROM `ci_sessions`;

