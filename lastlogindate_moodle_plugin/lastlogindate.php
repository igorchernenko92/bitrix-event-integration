<?php
echo 'tett';
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->dirroot.'/lib.php');

//$inputJSON = file_get_contents('php://input');

//try {
//$data=json_decode($inputJSON);
//} catch (Exception $e) {
//    echo e.message();
//}
//
//
//if ($data->status!=='paid') {
//    echo 'not paid';
//    exit;
//}
//
//$email=$data->contact->contact_profile->email;
//$email=strtolower($email);
//
//$phone=$data->contact->contact_profile->phone;
//
//$courseid=$data->contact->additional_info->courseid;
//
//
//
//$usertoenrol=null;
//
//if (!$userexists=$DB->get_record('user',array('email'=>$email))) {
//
//    $usernew = new stdClass();
//    $usernew->id = -1;
//    $usernew->auth = 'manual';
//    $usernew->confirmed = 1;
//    $usernew->deleted = 0;
//    $usernew->timezone = '99';
//
//    $authplugin = get_auth_plugin($usernew->auth);
//
//    $usernew->timemodified = time();
//    $createpassword = false;
//
//    $usernew->username=$email;
//    $usernew->newpassword=$phone;
//    $usernew->password = hash_internal_user_password($usernew->newpassword);
//
//    $usernew->firstname=$data->contact->contact_profile->first_name;
//    $usernew->lastname=$data->contact->contact_profile->last_name;
//    $usernew->email=$email;
//    $usernew->phone2=$phone;
//
//    $usernew->mnethostid = $CFG->mnet_localhost_id; // Always local user.
//    $usernew->confirmed  = 1;
//    $usernew->timecreated = time();
//
//    $usernew->id = user_create_user($usernew, false, false);
//
//    if (!$authplugin->is_internal() and $authplugin->can_change_password() and !empty($usernew->newpassword)) {
//        if (!$authplugin->user_update_password($usernew, $usernew->newpassword)) {
//            // Do not stop here, we need to finish user creation.
//            debugging(get_string('cannotupdatepasswordonextauth', '', '', $usernew->auth), DEBUG_NONE);
//        }
//    }
//
//
//
//    $usertoenrol=$usernew;
//} else {
//
//    $usertoenrol=$userexists;
//}
//
//if (!$enrol_manual = enrol_get_plugin('manual')) {
//    throw new coding_exception('Can not instantiate enrol_manual');
//}
//
//$enrolinstance = $DB->get_record('enrol', array('courseid'=>$courseid, 'enrol'=>'manual'), '*', MUST_EXIST);
//$role=$DB->get_record('role',array('shortname'=>'student'));
//
//
//
//$enrol_manual->enrol_user($enrolinstance, $usertoenrol->id, $role->id);
//
//
//echo "ok";
//
