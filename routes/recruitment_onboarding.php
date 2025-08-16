<?php

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// ลงประกาศและจัดการตำแหน่งงาน
$router->group(['prefix' => 'hrm/api/v1', 'middleware' => 'auth'], function () use ($router) {
    $router->post('search_jobs', 'RecruitmentOnboarding\Controller_Onboarding@searchJobs');
    $router->get('view_jobs/{id}', 'RecruitmentOnboarding\Controller_Onboarding@view_jobs');
    $router->get('list_position', 'RecruitmentOnboarding\Controller_Onboarding@list_position');
    $router->post('create_job', 'RecruitmentOnboarding\Controller_Onboarding@create_job');
    $router->post('upload_jobfile', 'RecruitmentOnboarding\Controller_Onboarding@upload_jobfile');
    $router->post('create_position', 'RecruitmentOnboarding\Controller_Onboarding@create_position');
    $router->put('update_status_jobs/{id}', 'RecruitmentOnboarding\Controller_Onboarding@update_status_jobs');
// บันทึกและติดตามสถานะใบสมัคร
    $router->post('list_the_application', 'RecruitmentOnboarding\Controller_application_tracking@list_the_application');
    $router->post('list_of_job_applicants', 'RecruitmentOnboarding\Controller_application_tracking@list_of_job_applicants');
    $router->post('status_applications', 'RecruitmentOnboarding\Controller_application_tracking@status_applications');
    $router->get('view_of_job_applicants/{id}', 'RecruitmentOnboarding\Controller_application_tracking@view_of_job_applicants');
    //$router->get('find_an_application-n/{id}', 'RecruitmentOnboarding\Controller_application_tracking@find_an_application');
    // $router->put('update_application_status/{id}', 'RecruitmentOnboarding\Controller_application_tracking@update_application_status');
// ส่วนของระบบจัดการสอบ/สัมภาษณ์
    $router->post('list_of_eligible_candidates', 'RecruitmentOnboarding\Controller_interview_evaluation@list_of_eligible_candidates');
    $router->post('enter_your_score', 'RecruitmentOnboarding\Controller_interview_evaluation@enter_your_score');
    $router->post('create_room', 'RecruitmentOnboarding\Controller_interview_evaluation@create_room');
// ส่วนของระบบลงทะเบียนพนักงานใหม่
    $router->post('register_new_employee', 'RecruitmentOnboarding\Controller_new_employee_info@register_new_employee');
    $router->get('getNewEmployee', 'RecruitmentOnboarding\Controller_new_employee_info@getNewEmployee');
    $router->get('getNewEmployeeInfo/{id}', 'RecruitmentOnboarding\Controller_new_employee_info@getNewEmployeeInfo');

});


/// ไม่ใช้ token 
$router->group(['prefix' => 'hrm/api/v1'], function () use ($router) {
/// ส่วนของ การสมัครงานและค้นหางาน
 $router->post('create_application', 'RecruitmentOnboarding\Controller_application_tracking@create_application');
    $router->post('search_jobs', 'RecruitmentOnboarding\Controller_Onboarding@searchJobs');
    $router->post('education_history', 'RecruitmentOnboarding\Controller_application_tracking@education_history');
    $router->post('training', 'RecruitmentOnboarding\Controller_application_tracking@training');
    $router->post('foreign_language_skills', 'RecruitmentOnboarding\Controller_application_tracking@foreign_language_skills');
    $router->post('work_experience', 'RecruitmentOnboarding\Controller_application_tracking@work_experience');
    $router->post('typing_skills', 'RecruitmentOnboarding\Controller_application_tracking@typing_skills');
    $router->post('driving_skills', 'RecruitmentOnboarding\Controller_application_tracking@driving_skills');
    $router->post('health_conditions', 'RecruitmentOnboarding\Controller_application_tracking@health_conditions');
    $router->post('uploadDocument', 'RecruitmentOnboarding\Controller_application_tracking@uploadDocument');
    $router->post('family_info', 'RecruitmentOnboarding\Controller_application_tracking@family_info');
    $router->get('view_jobs/{id}', 'RecruitmentOnboarding\Controller_Onboarding@view_jobs');
    $router->get('list_position', 'RecruitmentOnboarding\Controller_Onboarding@list_position');
    $router->get('list_agency', 'Resource\Controller_Resource@getAll_agency');
    $router->post('list_of_eligible_candidates_users', 'RecruitmentOnboarding\Controller_interview_evaluation@list_of_eligible_candidates_users');
    $router->post('vidw_of_eligible_candidates_users', 'RecruitmentOnboarding\Controller_interview_evaluation@vidw_of_eligible_candidates_users');
    $router->post('the_names_have_been_selected', 'RecruitmentOnboarding\Controller_new_employee_info@the_names_have_been_selected');
    $router->post('viewthe_names_have_been_selected', 'RecruitmentOnboarding\Controller_new_employee_info@viewthe_names_have_been_selected');
    $router->get('find_an_application/{id}', 'RecruitmentOnboarding\Controller_application_tracking@find_an_application');
});