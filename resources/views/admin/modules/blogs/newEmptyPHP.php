<?php
$ci = & get_instance();
$ci->load->model('notifications_model');
$ci->load->model('user_model');

$user_id = $this->session->userdata("userId");
$role = $this->session->userdata("role");
$hr_modules = array();
$hrmodulefilter = array();
if ($role == 2) {
    if ($this->session->userdata("hr_modules") != '') {
        $hr_modules = explode(',', $this->session->userdata("hr_modules"));
    }
    if ($this->session->userdata("hrmodulefilter") != '') {
        $hrmodulefilter = explode(',', $this->session->userdata("hrmodulefilter"));
    }
}
//   print_r($_SESSION);
//   print_r( $hr_modules);exit;
$company_id = $this->session->userdata("company_id");

$token = $this->session->userdata("token");

$user_details = $ci->user_model->select_where_row('tbl_users', array('userId' => $user_id));
$notifications = $ci->notifications_model->get_header_notifications(array('user_id' => $user_id, 'company_id' => $company_id, 'role' => $role));

if ($token != $user_details->token) {
    session_destroy();
    header("Location: " . base_url() . "login");
}



$company_details = $ci->user_model->select_where_row('tbl_company', array('company_id' => $company_id));
$company_details = $ci->user_model->select_where_row('tbl_company', array('company_id' => $company_id));

$assign_module = !empty($company_details->assign_module) ? $company_details->assign_module : "";
if (empty($assign_module)) {
    $assign_module = array();
} else {
    $assign_module = json_decode($assign_module);
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
        <title><?php echo $pageTitle; ?></title>
        <link rel="icon" type="image/x-icon" href="<?php echo base_url() ?>assets/backend/assets/img/favicon.ico"/>
        <link href="<?php echo base_url() ?>assets/backend/assets/css/loader.css" rel="stylesheet" type="text/css" />
        <script src="<?php echo base_url() ?>assets/backend/assets/js/loader.js"></script>
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
        <link href="<?php echo base_url() ?>assets/backend/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url() ?>assets/backend/assets/css/plugins.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL STYLES -->
        <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/backend/plugins/table/datatable/datatables.css">
        <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/backend/plugins/table/datatable/custom_dt_miscellaneous.css">
        <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/backend/assets/css/forms/theme-checkbox-radio.css">
        <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/backend/plugins/table/datatable/dt-global_style.css">
        <!-- END PAGE LEVEL STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM STYLES -->
        <link href="<?php echo base_url() ?>assets/backend/plugins/apex/apexcharts.css" rel="stylesheet" type="text/css">
        <link href="<?php echo base_url() ?>assets/backend/assets/css/dashboard/dash_1.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->
        <!--  BEGIN CUSTOM STYLE FILE  -->
        <link href="<?php echo base_url() ?>assets/backend/assets/css/scrollspyNav.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url() ?>assets/backend/assets/css/components/tabs-accordian/custom-tabs.css" rel="stylesheet" type="text/css" />
        <!-- BEGIN PAGE LEVEL STYLES -->
        <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/backend/plugins/table/datatable/datatables.css">
        <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/backend/plugins/table/datatable/dt-global_style.css">
        <!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
        <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/backend/plugins/table/datatable/custom_dt_html5.css">

        <!--  BEGIN CUSTOM STYLE FILE  -->
        <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/backend/plugins/editors/quill/quill.snow.css">
        <!--<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/backend/assets/css/forms/theme-checkbox-radio.css">-->
        <link href="<?php echo base_url() ?>assets/backend/assets/css/apps/todolist.css" rel="stylesheet" type="text/css" />

        <!-- BEGIN PAGE LEVEL STYLE -->
        <link href="<?php echo base_url() ?>assets/backend/assets/css/scrollspyNav.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/backend/plugins/editors/markdown/simplemde.min.css">
        <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/backend/assets/css/widgets/modules-widgets.css">

        <link href="<?php echo base_url() ?>assets/backend/plugins/flatpickr/flatpickr.css" rel="stylesheet" type="text/css">


        <link href="<?php echo base_url() ?>assets/backend/plugins/flatpickr/custom-flatpickr.css" rel="stylesheet" type="text/css">

        <!-- BEGIN PAGE LEVEL STYLES -->
        <link href="<?php echo base_url() ?>assets/backend/assets/css/apps/scrumboard.css" rel="stylesheet" type="text/css" />
        <!--<link href="<?php echo base_url() ?>assets/backend/assets/css/forms/theme-checkbox-radio.css" rel="stylesheet" type="text/css">-->
        <!-- END PAGE LEVEL STYLES -->


        <!-- jQuery 2.1.4 -->
        <script src="<?php echo base_url(); ?>assets/js/jQuery-2.1.4.min.js"></script>



        <script type="text/javascript">
            var baseURL = "<?php echo base_url(); ?>";
        </script>
        <style>
            #email-error {
                color: #e7515a !important;
            }
            .error {
                color: #e7515a !important;
            }

        </style>
        <style type="text/css">
            .rounded-pills-icon .nav-pills li a {
                -webkit-border-radius: 0.625rem !important;
                -moz-border-radius: 0.625rem !important;
                -ms-border-radius: 0.625rem !important;
                -o-border-radius: 0.625rem !important;
                border-radius: 0.625rem !important;
                background-color: #f1f2f3;
                width: 100%;
                padding: 8px;
            }
        </style>

        <style type="text/css">
            #attendance-export_wrapper .dt-button span{
                font-family: 'Nunito', sans-serif !important;
                font-size: 16px !important;
                background:#164B74 !important;
            }
            #attendance-export_wrapper .dt-button{
                font-family: 'Nunito', sans-serif !important;
                font-size: 16px !important;
                background:#164B74 !important;
            }
            #attendance-export_wrapper .dt--bottom-section{
                display:none !important;
            }
            #attendance-export_wrapper .dt--top-section {
                margin: 0px 5px;
            }
            .new-custom-table{
                max-height: calc(100vh - 230px);
                overflow-y: auto;
                display: block;
                width: 100%;
                padding: 0;
            }

            .new-custom-table thead th{
                position: sticky;
                top: 0;
                background: white;
            }

            .new-custom-table .table > thead > tr > th{
                background: rgba(234, 241, 255, 1);
                z-index: 1;
            }
            .new-custom-table table tbody tr > td:first-child{
                position: sticky;
                left: 0;
                z-index: 1;
                background-color: white;
            }
            .new-custom-table table tbody tr td input.new-form-control{
                max-width: 100%;
            }
            .new-custom-table table thead tr > th:first-child{
                left: 0;
                z-index: 2;
                position: sticky;
            }
            /*
           .new-custom-table{
           max-height: calc(100vh - 230px);
           overflow-y: auto;
           display: block;
           width: 100%;
           padding: 0;
           }
           
           .new-custom-table thead th{
           position: sticky;
           top: 0;
           background: white;
           }
           
           .new-custom-table .table > thead > tr > th{
             background: rgba(234, 241, 255, 1);
             z-index: 1;
           }
           .new-custom-table table tbody tr > td:first-child{
             position: sticky;
             left: 0;
             z-index: 1;
             background-color: white;
           }
           .new-custom-table table tbody tr td input.new-form-control{
             max-width: 100%;
           }
           .new-custom-table table thead tr > th:first-child{
             left: 0;
             z-index: 2;
             position: sticky;
           }*/

        </style>

    </head>
    <body style="color: black;">
        <!-- BEGIN LOADER -->
        <div id="load_screen">
            <div class="loader">
                <div class="loader-content">
                    <div class="spinner-grow align-self-center"></div>
                </div>
            </div>
        </div>
        <!--  END LOADER -->
        <!--  BEGIN NAVBAR  -->
        <div class="header-container fixed-top">
            <header class="header navbar navbar-expand-sm">
                <ul class="navbar-item theme-brand flex-row  text-center">
                    <li class="nav-item theme-logo">
                        <a href="<?php echo base_url(); ?>">
                            <img src="<?php echo base_url() ?>assets/backend/assets/img/90x90.jpg" class="navbar-logo" alt="logo">
                        </a>
                    </li>
                    <li class="nav-item theme-text">
                        <a href="<?php echo base_url(); ?>" class="nav-link"> HR Plug </a>
                    </li>
                </ul>
                <ul class="navbar-item flex-row ml-md-auto">

                    <li class="nav-item dropdown notification-dropdown" style="margin-right:10px;margin-top:5px;">
                        <a href="javascript:void(0);" class="nav-link dropdown-toggle" id="notificationDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
<?php if (count($notifications) >= 1) { ?>
                                <span class="badge badge-success"></span>
<?php } ?>
                        </a>
                        <div class="dropdown-menu position-absolute" aria-labelledby="notificationDropdown">
                            <div class="notification-scroll">
                            <?php
                            if (count($notifications) >= 1) {
                                foreach ($notifications as $row) {
                                    ?>
                                        <div class="dropdown-item">
                                            <div class="media server-log">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-server"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6" y2="6"></line><line x1="6" y1="18" x2="6" y2="18"></line></svg>
                                                <div class="media-body">
                                                    <div class="data-info">
                                                        <h6 class=""><a href="<?php echo base_url() ?>notifications"><?php echo $row->subject; ?></a></h6>
                                                        <!--<p class="">45 min ago</p>-->
                                                    </div>

                                                    <!--<div class="icon-status">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                                    </div>-->
                                                </div>
                                            </div>
                                        </div> 
    <?php }
} else { ?>
                                    <div class="dropdown-item">
                                        <div class="media server-log">
                                            <div class="media-body">
                                                <div class="data-info">
                                                    <center>  <h6 class="">No record added yet!<br/><br/><br/><a href="<?php echo base_url() ?>notifications"><b>View History</b></a></h6></center>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
<?php } ?>

                                <!--                            <div class="dropdown-item">
                                                                <div class="media ">
                                                                     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-server"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6" y2="6"></line><line x1="6" y1="18" x2="6" y2="18"></line></svg>
                                                                                                        
                                                                    <div class="media-body">
                                                                        <div class="data-info">
                                                                            <h6 class="">Licence Expiring Soon</h6>
                                                                         </div>
                                                                     </div>
                                                                </div>
                                                            </div>
                                
                                                            <div class="dropdown-item">
                                                                <div class="media file-upload">
                                                                     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-server"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6" y2="6"></line><line x1="6" y1="18" x2="6" y2="18"></line></svg>
                                                                    
                                                                    <div class="media-body">
                                                                        <div class="data-info">
                                                                            <h6 class="">Kelly Portfolio.pdf</h6>
                                                                         </div>
                                 
                                                                    </div>
                                                                </div>
                                                            </div>-->
                            </div>
                        </div>
                    </li>          
                    <li class="nav-item">
                        <a href="<?php echo base_url(); ?>user/profile" class="nav-link" style="color:white;">
                            <b><?php echo $name; ?></b>
                        </a>
                    </li>

                    <li class="nav-item dropdown user-profile-dropdown">
                        <a href="javascript:void(0);" class="nav-link dropdown-toggle user" id="userProfileDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <!--<img src="<?php echo base_url() ?>assets/backend/assets/img/90x90.jpg" alt="avatar">-->
<?php if (!empty($user_details->profile_picture)) { ?>
                                <img src="<?php echo base_url() . $user_details->profile_picture ?>" alt="avatar"  alt="User Image">
                            <?php } else { ?>
                                <img src="<?php echo base_url() ?>assets/backend/assets/img/90x90.jpg" alt="avatar">
                            <?php } ?>
                        </a>
                        <div class="dropdown-menu position-absolute" aria-labelledby="userProfileDropdown">
                            <div class="">
                                <div class="dropdown-item" style="display: none;">
                                    <a class="" href="user_profile.html">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                        Profile
                                    </a>
                                </div>
                                <div class="dropdown-item" style="display: none;">
                                    <a class="" href="apps_mailbox.html">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-inbox">
                                        <polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline>
                                        <path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path>
                                        </svg>
                                        Inbox
                                    </a>
                                </div>
                                <div class="dropdown-item" style="display: none;">
                                    <a class="" href="auth_lockscreen.html">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-lock">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                        </svg>
                                        Lock Screen
                                    </a>
                                </div>
                                <div class="dropdown-item">
                                    <a class="" href="<?php echo base_url(); ?>loadChangePass">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-lock">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                        </svg>
                                        Change Password
                                    </a>
<?php if ($role == 3) { ?>
                                        <a class="" href="<?php echo base_url(); ?>my_profile">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-lock">
                                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                            </svg>
                                            Account
                                        </a>
<?php } ?>
                                    <a class="" href="<?php echo base_url(); ?>user/profile">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                        Profile Picture
                                    </a>
                                    <a class="" href="<?php echo base_url(); ?>logout">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                        <polyline points="16 17 21 12 16 7"></polyline>
                                        <line x1="21" y1="12" x2="9" y2="12"></line>
                                        </svg>
                                        Sign Out
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                    </li>

                </ul>
            </header>
        </div>
        <!--  END NAVBAR  -->
        <!--  BEGIN NAVBAR  -->
        <div class="sub-header-container">
            <header class="header navbar navbar-expand-sm">
                <a href="javascript:void(0);" class="sidebarCollapse" data-placement="bottom">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </a>
            </header>
        </div>
        <!--  END NAVBAR  -->
        <!--  BEGIN MAIN CONTAINER  -->
        <div class="main-container" id="container">
            <div class="overlay"></div>
            <div class="search-overlay"></div>
            <!--  BEGIN SIDEBAR  -->
            <div class="sidebar-wrapper sidebar-theme">
                <nav id="sidebar">
                    <div class="shadow-bottom"></div>
                    <ul class="list-unstyled menu-categories" id="accordionExample">
<?php
if ($role == ROLE_ADMIN) {
    ?>
                            <li class="menu">
                                <a href="<?php echo base_url() ?>company_listing" aria-expanded="false" class="dropdown-toggle">
                                    <div class="">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="3" y1="9" x2="21" y2="9"></line>
                                        <line x1="9" y1="21" x2="9" y2="9"></line>
                                        </svg>
                                        <span>Companies</span>
                                    </div>
                                </a>
                            </li>
                            <li class="menu">
                                <a href="<?php echo base_url() ?>hrListing" aria-expanded="false" class="dropdown-toggle">
                                    <div class="">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="3" y1="9" x2="21" y2="9"></line>
                                        <line x1="9" y1="21" x2="9" y2="9"></line>
                                        </svg>
                                        <span>Users</span>
                                    </div>
                                </a>
                            </li>
                            <li class="menu">
                                <a href="#kpiBoss" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle collapsed">
                                    <div class="">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                        </svg>
                                        <span>KPI</span>
                                    </div>
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                        </svg>
                                    </div>
                                </a>
                                <ul class="submenu list-unstyled collapse" id="kpiBoss" data-parent="#accordionExample" style="">
                                    <li><a href="<?php echo base_url(); ?>kpi_listing"><i class="fa fa-files-o"></i><span>KPI
                                                Listing</span></a>
                                    </li>
                                    <li><a href="<?php echo base_url(); ?>kpi_company_list"><i
                                                class="fa fa-files-o"></i><span>Create New KPI</span></a></li>
                                    <li><a href="<?php echo base_url(); ?>check_kpi_status/1"><i
                                                class="fa fa-files-o"></i><span>Submitted KPI </span></a></li>
                                    <li><a href="<?php echo base_url(); ?>check_kpi_status/2"><i
                                                class="fa fa-files-o"></i><span>Pending KPI </span></a></li>
                                </ul>
                            </li>
                            <li class="menu">
                                <a href="<?php echo base_url() ?>get_employee_dsr" aria-expanded="false" class="dropdown-toggle">
                                    <div class="">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="3" y1="9" x2="21" y2="9"></line>
                                        <line x1="9" y1="21" x2="9" y2="9"></line>
                                        </svg>
                                        <span>DSR</span>
                                    </div>
                                </a>
                            </li>
                            <li class="menu">
                                <a href="<?php echo base_url() ?>company_worksheet" aria-expanded="false" class="dropdown-toggle">
                                    <div class="">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="3" y1="9" x2="21" y2="9"></line>
                                        <line x1="9" y1="21" x2="9" y2="9"></line>
                                        </svg>
                                        <span>Worksheet</span>
                                    </div>
                                </a>
                            </li>
                            <li class="menu">
                                <a href="#payrollBoss" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle collapsed">
                                    <div class="">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                        </svg>
                                        <span>Payroll</span>
                                    </div>
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                        </svg>
                                    </div>
                                </a>
                                <ul class="submenu list-unstyled collapse" id="payrollBoss" data-parent="#accordionExample" style="">
                                   <!--   <li><a href="<?php echo base_url(); ?>payroll/payroll_master"><i
                                      class="fa fa-files-o"></i><span>Payroll Master</span></a></li>
                                      <li><a href="<?php echo base_url(); ?>payroll/payroll_company_master"><i
                                      class="fa fa-files-o"></i><span>Create Payroll</span></a></li>-->
                                    <li><a href="<?php echo base_url(); ?>reports/payroll_monthly_report"><i
                                                class="fa fa-files-o"></i><span>Employee Report</span></a></li>
                                    <li><a href="<?php echo base_url(); ?>reports/payroll_yearly_report"><i
                                                class="fa fa-files-o"></i><span>Monthly Report</span></a></li>
                                </ul>
                            </li>
                            <li class="menu">
                                <a href="<?php echo base_url() ?>professional/taxmaster" aria-expanded="false" class="dropdown-toggle">
                                    <div class="">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="3" y1="9" x2="21" y2="9"></line>
                                        <line x1="9" y1="21" x2="9" y2="9"></line>
                                        </svg>
                                        <span>Professional Tax</span>
                                    </div>
                                </a>
                            </li>
                            <li class="menu">
                                <a href="<?php echo base_url() ?>tds" aria-expanded="false" class="dropdown-toggle">
                                    <div class="">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="3" y1="9" x2="21" y2="9"></line>
                                        <line x1="9" y1="21" x2="9" y2="9"></line>
                                        </svg>
                                        <span>TDS Management</span>
                                    </div>
                                </a>
                            </li>
    <?php }
?>
                        <!--  ************************************** MD Header **************************************************  -->

                        <?php if ($role == 4) { // MD
                            ?>
                            <li class="menu">
                                <a href="<?php echo base_url() ?>dashboard" aria-expanded="false" class="dropdown-toggle">
                                    <div class="">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="3" y1="9" x2="21" y2="9"></line>
                                        <line x1="9" y1="21" x2="9" y2="9"></line>
                                        </svg>
                                        <span>Dashboard</span>
                                    </div>
                                </a>
                            </li>
    <?php if (!empty($assign_module) && in_array("14", $assign_module)) { ?>
                                <li class="menu">
                                    <a href="<?php echo base_url() ?>annoucement" aria-expanded="false" class="dropdown-toggle">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="3" y1="9" x2="21" y2="9"></line>
                                            <line x1="9" y1="21" x2="9" y2="9"></line>
                                            </svg>
                                            <span>Annoucement</span>
                                        </div>
                                    </a>
                                </li>
    <?php } ?>

                            <li class="menu">
                                <a href="<?php echo base_url() ?>CompanyMaster" aria-expanded="false" class="dropdown-toggle">
                                    <div class="">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="3" y1="9" x2="21" y2="9"></line>
                                        <line x1="9" y1="21" x2="9" y2="9"></line>
                                        </svg>
                                        <span>Master Data</span>
                                    </div>
                                </a>
                            </li>
                            <li class="menu">
                                <a href="<?php echo base_url() ?>hrListing" aria-expanded="false" class="dropdown-toggle">
                                    <div class="">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="3" y1="9" x2="21" y2="9"></line>
                                        <line x1="9" y1="21" x2="9" y2="9"></line>
                                        </svg>
                                        <span>Employee<br/><span style="padding-left:35px">Directory</span></span>
                                    </div>
                                </a>
                            </li>
    <?php if (!empty($assign_module) && in_array("18", $assign_module)) { ?>
                                <li class="menu">
                                    <a href="#attendance" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle collapsed">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                            </svg>
                                            <span>Attendance</span>
                                        </div>
                                        <div>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                            </svg>
                                        </div>
                                    </a>
                                    <ul class="submenu list-unstyled collapse" id="attendance" data-parent="#attendance" style="">
                                        <li>
                                            <a href="<?php echo base_url() ?>attendance/listall"> Attendance Report </a>
                                        </li>  
                                        <li>
                                            <a href="<?php echo base_url(); ?>attendance/get_request"> Mis punch requests </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo base_url(); ?>holiday/get_weekoff_request"> Week Off requests </a>
                                        </li>

                                    </ul>
                                </li>
    <?php } if (!empty($assign_module) && in_array("6", $assign_module)) {
        ?>
                                <li class="menu">
                                    <a href="#users" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle collapsed">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                            </svg>
                                            <span>Leave Module</span>
                                        </div>
                                        <div>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                            </svg>
                                        </div>
                                    </a>
                                    <ul class="submenu list-unstyled collapse" id="users" data-parent="#accordionExample" style="">
                                        <li>
                                            <a href="<?php echo base_url(); ?>get_leave_request"> Leave Request </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo base_url(); ?>leave/list">Add Leave  </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo base_url(); ?>leave/CompensatoryOffListing">Compensatory Off</a>
                                        </li>
        <?php if (!empty($assign_module) && in_array("3", $assign_module)) { ?>
                                            <li>
                                                <a href="<?php echo base_url() ?>leave/credits">

                                                    <span>Leave Credits</span>

                                                </a>
                                            </li>
        <?php } ?>
                                        <!--
                                        <li>
                                           <a href="<?php echo base_url(); ?>employee_leaveReport"> Leave Report </a>
                                        </li>-->
                                    </ul>
                                </li>
        <?php }
    ?>
    <?php if (!empty($assign_module) && in_array("9", $assign_module)) {
        ?>
                                <li class="menu">
                                    <a href="<?php echo base_url() ?>loan" aria-expanded="false" class="dropdown-toggle">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="3" y1="9" x2="21" y2="9"></line>
                                            <line x1="9" y1="21" x2="9" y2="9"></line>
                                            </svg>
                                            <span>Loan/Advance</span>
                                        </div>
                                    </a>
                                </li>
        <?php }
    ?>

    <?php if (!empty($assign_module) && in_array("5", $assign_module)) {
        ?>
                                <li class="menu">
                                    <a href="<?php echo base_url() ?>travel_policy_form" aria-expanded="false" class="dropdown-toggle">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="3" y1="9" x2="21" y2="9"></line>
                                            <line x1="9" y1="21" x2="9" y2="9"></line>
                                            </svg>
                                            <span>Expense</span>
                                        </div>
                                    </a>
                                </li>
        <?php }
    ?>
    <?php if (!empty($assign_module) && in_array("21", $assign_module)) { ?>
                                <li class="menu">
                                    <a href="<?php echo base_url() ?>settlement/" aria-expanded="false" class="dropdown-toggle">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="3" y1="9" x2="21" y2="9"></line>
                                            <line x1="9" y1="21" x2="9" y2="9"></line>
                                            </svg>
                                            <span>Full & Final <br/><span style="padding-left:35px"> settlement </span> </span>
                                        </div>
                                    </a>
                                </li>
    <?php } ?>

    <?php if (!empty($assign_module) && in_array("20", $assign_module)) { ?>
                                <li class="menu">
                                    <a href="<?php echo base_url() ?>resignation/get_request" aria-expanded="false" class="dropdown-toggle">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="3" y1="9" x2="21" y2="9"></line>
                                            <line x1="9" y1="21" x2="9" y2="9"></line>
                                            </svg>
                                            <span>Resignation <br/><span style="padding-left:35px">Requests</span></span>
                                        </div>
                                    </a>
                                </li>
    <?php } ?>

    <?php if (!empty($assign_module) && in_array("15", $assign_module)) { ?>
                                <li class="menu">
                                    <a href="<?php echo base_url() ?>increment/request" aria-expanded="false" class="dropdown-toggle">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="3" y1="9" x2="21" y2="9"></line>
                                            <line x1="9" y1="21" x2="9" y2="9"></line>
                                            </svg>
                                            <span>Employee History</span>
                                        </div>
                                    </a>
                                </li>

    <?php } ?>
    <?php
    if (!empty($assign_module) && in_array("3", $assign_module)) {
        ?>
                                <li class="menu">
                                    <a href="<?php echo base_url() ?>payroll/payroll_company_master" aria-expanded="false" class="dropdown-toggle">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="3" y1="9" x2="21" y2="9"></line>
                                            <line x1="9" y1="21" x2="9" y2="9"></line>
                                            </svg>
                                            <span>Payroll</span>
                                        </div>
                                    </a>
                                </li>
        <?php if (!empty($assign_module) && in_array("3", $assign_module)) {
            ?>
                                    <li class="menu">
                                        <a href="#reports" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle collapsed">
                                            <div class="">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users">
                                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="9" cy="7" r="4"></circle>
                                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                                </svg>
                                                <span>Reports</span>
                                            </div>
                                            <div>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                                                <polyline points="9 18 15 12 9 6"></polyline>
                                                </svg>
                                            </div>
                                        </a>
                                        <ul class="submenu list-unstyled collapse" id="reports" data-parent="#accordionExample" style="">
                                            <li><a href="<?php echo base_url(); ?>reports/payroll_monthly_report"><span>Employee Salary <br/>Details</span></a></li>
                                            <li><a href="<?php echo base_url(); ?>reports/payroll_yearly_report"></i><span>Monthly Salary</span></a></li>
                                            <li><a href="<?php echo base_url(); ?>reports/time_office_employee_listing"></i><span>Monthly Attendence</span></a></li>
            <?php if (!empty($assign_module) && in_array("10", $assign_module)) { ?>

            <?php } ?>
            <?php if (!empty($assign_module) && in_array("9", $assign_module)) {
                ?>
                                                <li><a href="<?php echo base_url(); ?>reports/loan_report"></i><span>Loan</span></a></li>
                                            <?php } ?>
                                            <?php if (!empty($assign_module) && in_array("5", $assign_module)) { ?>
                                                <li><a href="<?php echo base_url(); ?>reports/travel_policy_form_reports"></i><span>Expense</span></a></li>
                                            <?php } ?>
                                            <li><a href="<?php echo base_url(); ?>reports/documentUploadReports"></i><span>Employees Uploaded<br/>Documents</span></a></li>
                                            <?php if (!empty($assign_module) && in_array("2", $assign_module)) {
                                                ?>
                                                <li><a href="<?php echo base_url(); ?>reports/DSR_Reports"></i><span>DSR</span></a></li>
                                            <?php } ?>
                                            <?php if (!empty($assign_module) && in_array("6", $assign_module)) {
                                                ?>
                                                <li><a href="<?php echo base_url(); ?>user/yearly_leaveReport">
                                                        <i class="fa fa-users"></i>
                                                        <span>Employees's Yearly <br/>Leave</span>
                                                    </a>
                                                </li>
                <?php }
            ?>

                                        </ul>
                                    </li>

                                            <?php }
                                        ?>
                                <!--
                                      
                                     <li class="menu">
                                   <a href="#PayrollD" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle collapsed">
                                      <div class="">
                                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                         </svg>
                                         <span>Payroll</span>
                                      </div>
                                      <div>
                                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                         </svg>
                                      </div>
                                   </a>
                                   <ul class="submenu list-unstyled collapse" id="PayrollD" data-parent="#accordionExample" style="">
                                
                                        <li>
                                            <a href="<?php echo base_url() ?>payroll/payroll_company_master" >
                                               <i class="fa fa-files-o"></i>
                                               <span>Payroll</span>
                                           </a>
                                       </li>
                                
                                
                                   </ul>
                                </li>
                                -->
    <?php } ?>
    <?php if (!empty($assign_module) && in_array("10", $assign_module) || $this->session->userdata('Statutory_Compliance') == 1) {
        ?>
                                <li class="menu">
                                    <a href="#AdvancePayroll" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle collapsed">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                            </svg>
                                            <span>Statutory </br> <span style="padding-left:35px">Compliance</span></span>
                                        </div>
                                        <div>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                            </svg>
                                        </div>
                                    </a>
                                    <ul class="submenu list-unstyled collapse" id="AdvancePayroll" data-parent="#accordionExample" style="">
                                        <li><a href="<?php echo base_url(); ?>reports/payment_report"><i
                                                    class="fa fa-files-o"></i><span>Payment Report</span></a></li>
                                        <li><a href="<?php echo base_url(); ?>reports/esic_report"><i
                                                    class="fa fa-files-o"></i><span>ESIC Report</span></a></li>
                                        <li><a href="<?php echo base_url(); ?>reports/professional_text_report"><i
                                                    class="fa fa-files-o"></i><span>Professional Tax</span></a></li>
                                        <li><a href="<?php echo base_url(); ?>reports/PF_report"><i
                                                    class="fa fa-files-o"></i><span>PF Reports</span></a></li>
                                        <li><a href="<?php echo base_url(); ?>reports/bonus">
                                                <i class="fa fa-users"></i>
                                                <span>Bonus Report</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
        <?php }
    ?>

    <?php if (!empty($assign_module) && in_array("1", $assign_module)) {
        ?>
                                <li class="menu">
                                    <a href="#MDKPI" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle collapsed">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                            </svg>
                                            <span>KPI</span>
                                        </div>
                                        <div>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                            </svg>
                                        </div>
                                    </a>
                                    <ul class="submenu list-unstyled collapse" id="MDKPI" data-parent="#accordionExample" style="">
                                        <li><a href="<?php echo base_url(); ?>kpi_listing"><i class="fa fa-files-o"></i><span>KPI
                                                    Listing</span></a></li>
                                        <li><a href="<?php echo base_url(); ?>kpi_company_list"><i
                                                    class="fa fa-files-o"></i><span>Create New KPI</span></a></li>
                                        <li><a href="<?php echo base_url(); ?>check_kpi_status/1"><i
                                                    class="fa fa-files-o"></i><span>Submitted KPI </span></a></li>
                                        <li><a href="<?php echo base_url(); ?>check_kpi_status/2"><i
                                                    class="fa fa-files-o"></i><span>Pending KPI </span></a></li>
                                    </ul>
                                </li>

                                <li class="menu">
                                    <a href="#MDKPIReports" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle collapsed">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                            </svg>
                                            <span>KPI Reports</span>
                                        </div>
                                        <div>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                            </svg>
                                        </div>
                                    </a>
                                    <ul class="submenu list-unstyled collapse" id="MDKPIReports" data-parent="#accordionExample" style="">
                                        <li><a href="<?php echo base_url(); ?>check_employee_kpi"><i
                                                    class="fa fa-files-o"></i><span>Employee KPI</span></a></li>
                                        <li><a href="<?php echo base_url(); ?>compair_employee_kpi"><i
                                                    class="fa fa-files-o"></i><span>Compair KPI</span></a></li>
                                        <li><a href="<?php echo base_url(); ?>list_gen_kpi"><i
                                                    class="fa fa-files-o"></i><span>Company KPI</span></a></li>
                                        <li><a href="<?php echo base_url(); ?>reports/employee_kpi_reports"><i
                                                    class="fa fa-files-o"></i><span>KPI Reports</span></a></li>  
                                    </ul>
                                </li>

    <?php } ?>
                            <!-- Change on 22 05 2022--> 
                            <!--
    <?php if (!empty($assign_module) && in_array("2", $assign_module)) {
        ?>
                         <li class="menu">
                              <a href="<?php echo base_url() ?>reports/DSR_Reports" aria-expanded="false" class="dropdown-toggle">
                                 <div class="">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                       <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                       <line x1="3" y1="9" x2="21" y2="9"></line>
                                       <line x1="9" y1="21" x2="9" y2="9"></line>
                                    </svg>
                                    <span>DSR</span>
                                 </div>
                              </a>
                           </li>
    <?php } ?> -->





    <?php if (!empty($assign_module) && in_array("8", $assign_module)) {
        ?>
                                <li class="menu">
                                    <a href="#Projects" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle collapsed">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                            </svg>
                                            <span>Projects</span>
                                        </div>
                                        <div>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                            </svg>
                                        </div>
                                    </a>
                                    <ul class="submenu list-unstyled collapse" id="Projects" data-parent="#accordionExample" style="">
                                        <li><a href="<?php echo base_url(); ?>project">
                                                <i class="fa fa-files-o"></i>
                                                <span>Projects List</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo base_url(); ?>project/get_monthly_worksheet_report">
                                                <i class="fa fa-files-o"></i>
                                                <span>Project Reports</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo base_url(); ?>project/project_list">
                                                <i class="fa fa-files-o"></i>
                                                <span>Project Sheet</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
    <?php } ?>







    <?php /* if (!empty($assign_module) && in_array("3", $assign_module))
      { ?>
      <li class="menu">
      <a href="<?php echo base_url() ?>payroll/payroll_company_master" aria-expanded="false" class="dropdown-toggle">
      <div class="">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
      <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
      <line x1="3" y1="9" x2="21" y2="9"></line>
      <line x1="9" y1="21" x2="9" y2="9"></line>
      </svg>
      <span>Payroll</span>
      </div>
      </a>
      </li>
      <?php } */ ?>




                            <?php if (!empty($assign_module) && (in_array("2", $assign_module) || in_array("4", $assign_module) || in_array("11", $assign_module) )) {
                                ?>

                                <li class="menu">
                                    <a href="#Performance" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle collapsed">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                            </svg>
                                            <span>Performance</span>
                                        </div>
                                        <div>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                            </svg>
                                        </div>
                                    </a>
                                    <ul class="submenu list-unstyled collapse" id="Performance" data-parent="#accordionExample" style="">
        <?php if (!empty($assign_module) && (in_array("4", $assign_module) )) {
            ?>
                                            <li>
                                                <a href="<?php echo base_url() ?>employee_worksheet" >
                                                    <i class="fa fa-files-o"></i>
                                                    <span>Worksheet</span>

                                                </a>
                                            </li>
            <?php }
        ?>
        <?php if (!empty($assign_module) && in_array("11", $assign_module)) {
            ?>
                                            <li>
                                                <a href="<?php echo base_url() ?>tasksheet" aria-expanded="false" class="dropdown-toggle">
                                                    <i class="fa fa-files-o"></i>
                                                    <span>Tasksheet</span>

                                                </a>
                                            </li>
        <?php } ?>

        <?php if (!empty($assign_module) && in_array("2", $assign_module)) {
            ?>
                                            <li>
                                                <a href="<?php echo base_url() ?>reports/DSR_Reports" aria-expanded="false" class="dropdown-toggle">
                                                    <i class="fa fa-files-o"></i>
                                                    <span>DSR</span>

                                                </a>
                                            </li>
                                            <!-- <li>
                                                 <a href="<?php echo base_url(); ?>dsr" >
                                                       <i class="fa fa-files-o"></i>
                                                       <span>DSR</span>
                                                 </a>
                                              </li>-->
                                            <!-- <li class="menu">
                                                         <a href="#DsrMd" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle collapsed">
                                                            <div class="">
                                                               <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users">
                                                                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                                  <circle cx="9" cy="7" r="4"></circle>
                                                                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                                                  <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                                               </svg>
                                                               <span>DSR</span>
                                                            </div>
                                                            <div>
                                                               <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                                                                  <polyline points="9 18 15 12 9 6"></polyline>
                                                               </svg>
                                                            </div>
                                                         </a>
                                                         <ul class="submenu list-unstyled collapse" id="DsrMd" data-parent="#accordionExample" style="">
                                                            <li><a href="<?php echo base_url(); ?>get_employee_dsr">
                                                               <i class="fa fa-files-o"></i>
                                                               <span>DSR</span>
                                                               </a>
                                                            </li>
                                                            <li>
                                                               <a href="<?php echo base_url(); ?>dsr">
                                                               <i class="fa fa-files-o"></i>
                                                               <span>NEW DSR</span>
                                                               </a>
                                                            </li>
                                                            
                                                         </ul>
                                                      </li>-->


            <?php
        }
        ?>
                                    </ul>
                                </li>

                                        <?php
                                    }
                                    ?>



                        <?php } ?>


































                        <!--  ************************************** Employee **************************************************  -->


<?php
if ($role == '3') {
    ?>

                            <li class="menu">
                                <a href="<?php echo base_url() ?>user/document_upload" aria-expanded="false" class="dropdown-toggle">
                                    <div class="">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="3" y1="9" x2="21" y2="9"></line>
                                        <line x1="9" y1="21" x2="9" y2="9"></line>
                                        </svg>
                                        <span>Document Upload</span>
                                    </div>
                                </a>
                            </li> 

    <?php if (!empty($assign_module) && in_array("18", $assign_module)) { ?>
                                <li class="menu">
                                    <a href="#attendance" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle collapsed">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                            </svg>
                                            <span>Attendance</span>
                                        </div>
                                        <div>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                            </svg>
                                        </div>
                                    </a>
                                    <ul class="submenu list-unstyled collapse" id="attendance" data-parent="#attendance" style="">
                                        <li>
                                            <a href="<?php echo base_url() ?>attendance/user/<?php echo $this->session->userdata('userId') ?>"> Attendance Report </a>
                                        </li>  
                                        <li>
                                            <a href="<?php echo base_url(); ?>attendance/get_request"> Mis punch request </a>
                                        </li>

                                    </ul>
                                </li>
        <?php }
    ?>

    <?php
    if (!empty($assign_module) && in_array("3", $assign_module)) {
        ?>

                                <li class="menu">
                                    <a href="<?php echo base_url() ?>payroll/payroll_listing" aria-expanded="false" class="dropdown-toggle">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="3" y1="9" x2="21" y2="9"></line>
                                            <line x1="9" y1="21" x2="9" y2="9"></line>
                                            </svg>
                                            <span>Payroll Report</span>
                                        </div>
                                    </a>
                                </li>


        <?php }
    ?>

    <?php if (!empty($assign_module) && in_array("7", $assign_module)) {
        ?>
                                <li class="menu">
                                    <a href="<?php echo base_url() ?>company_letter" aria-expanded="false" class="dropdown-toggle">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="3" y1="9" x2="21" y2="9"></line>
                                            <line x1="9" y1="21" x2="9" y2="9"></line>
                                            </svg>
                                            <span>General Policy</span>
                                        </div>
                                    </a>
                                </li>

    <?php } ?>

    <?php if (!empty($assign_module) && in_array("12", $assign_module)) { ?>
                                <li class="menu">
                                    <a href="<?php echo base_url() ?>holiday/lists" aria-expanded="false" class="dropdown-toggle">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="3" y1="9" x2="21" y2="9"></line>
                                            <line x1="9" y1="21" x2="9" y2="9"></line>
                                            </svg>
                                            <span>Holiday</span>
                                        </div>
                                    </a>
                                </li>
    <?php } ?>


    <?php if (!empty($assign_module) && in_array("6", $assign_module)) {
        ?>
                                <li class="menu">
                                    <a href="<?php echo base_url() ?>my_leave_listing" aria-expanded="false" class="dropdown-toggle">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="3" y1="9" x2="21" y2="9"></line>
                                            <line x1="9" y1="21" x2="9" y2="9"></line>
                                            </svg>
                                            <span>Request for Leave</span>
                                        </div>
                                    </a>
                                </li>
    <?php } ?> 
    <?php
    if (!empty($assign_module) && in_array("13", $assign_module)) {
        ?>

                                <li class="menu">
                                    <a href="<?php echo base_url() ?>holiday/week_off_request_list_emp" aria-expanded="false" class="dropdown-toggle">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="3" y1="9" x2="21" y2="9"></line>
                                            <line x1="9" y1="21" x2="9" y2="9"></line>
                                            </svg>
                                            <span>Week Off Request</span>
                                        </div>
                                    </a>
                                </li>


        <?php }
    ?>

    <?php if ($company_id == 12) { ?>
                                <li class="menu">
                                    <a href="<?php echo base_url() ?>leave/CompensatoryOffListing" aria-expanded="false" class="dropdown-toggle">


                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="3" y1="9" x2="21" y2="9"></line>
                                        <line x1="9" y1="21" x2="9" y2="9"></line>
                                        </svg>
                                        <span>Compensatory Off</span>

                                    </a>
                                </li>
    <?php } ?> 



    <?php if (!empty($assign_module) && in_array("9", $assign_module)) {
        ?>
                                <li class="menu">
                                    <a href="#loanEmployee" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle collapsed">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                            </svg>
                                            <span>Loan</span>
                                        </div>
                                        <div>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                            </svg>
                                        </div>
                                    </a>
                                    <ul class="submenu list-unstyled collapse" id="loanEmployee" data-parent="#accordionExample" style="">
                                        <li>
                                            <a href="<?php echo base_url(); ?>loan/">
                                                <i class="fa fa-files-o"></i>
                                                <span>Loan / Advance</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo base_url(); ?>loan/loan_deduction_history/0">
                                                <i class="fa fa-files-o"></i>
                                                <span>Deduction History</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

    <?php } ?>
    <?php if (!empty($assign_module) && in_array("5", $assign_module)) {
        ?>
                                <li class="menu">
                                    <a href="#ExpenseEmployee" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle collapsed">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                            </svg>
                                            <span>Expense</span>
                                        </div>
                                        <div>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                            </svg>
                                        </div>
                                    </a>
                                    <ul class="submenu list-unstyled collapse" id="ExpenseEmployee" data-parent="#accordionExample" style="">
                                        <li><a href="<?php echo base_url(); ?>user/my_travel_policy"><i
                                                    class="fa fa-files-o"></i><span>Expense List</span></a></li>
                                        <li><a href="<?php echo base_url(); ?>travel_policy"><i class="fa fa-files-o"></i><span>Add
                                                    Expense</span></a></li>
                                        <li><a href="<?php echo base_url(); ?>user/refere_travel_policy"><i
                                                    class="fa fa-files-o"></i><span>Refere Expense</span></a></li>
                                    </ul>
                                </li>



        <?php }
    ?>
                  























  
    
    <?php if ($this->session->userdata('Statutory_Compliance') == 1) { ?>
                                <li class="menu">
                                    <a href="#AdvancePayroll" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle collapsed">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                            </svg>
                                            <span>Statutory </br> <span style="padding-left:35px">Compliance</span></span>
                                        </div>
                                        <div>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                            </svg>
                                        </div>
                                    </a>
                                    <ul class="submenu list-unstyled collapse" id="AdvancePayroll" data-parent="#accordionExample" style="">
                                        <li><a href="<?php echo base_url(); ?>reports/payment_report"><i
                                                    class="fa fa-files-o"></i><span>Payment Report</span></a></li>
                                        <li><a href="<?php echo base_url(); ?>reports/esic_report"><i
                                                    class="fa fa-files-o"></i><span>ESIC Report</span></a></li>
                                        <li><a href="<?php echo base_url(); ?>reports/professional_text_report"><i
                                                    class="fa fa-files-o"></i><span>Professional Tax</span></a></li>
                                        <li><a href="<?php echo base_url(); ?>reports/PF_report"><i
                                                    class="fa fa-files-o"></i><span>PF Reports</span></a></li>
                                        <li><a href="<?php echo base_url(); ?>reports/bonus">
                                                <i class="fa fa-users"></i>
                                                <span>Bonus Report</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
    <?php } ?>
    <?php if (!empty($assign_module) && (in_array("4", $assign_module))) {
        ?>
                                <li class="menu">
                                    <a href="<?php echo base_url() ?>get_my_worksheet" aria-expanded="false" class="dropdown-toggle">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="3" y1="9" x2="21" y2="9"></line>
                                            <line x1="9" y1="21" x2="9" y2="9"></line>
                                            </svg>
                                            <span>Worksheet</span>
                                        </div>
                                    </a>
                                </li>

        <?php if (in_array("8", $assign_module)) {
            ?>
                                    <li class="menu">
                                        <a href="<?php echo base_url() ?>project/worksheet_list" aria-expanded="false" class="dropdown-toggle">
                                            <div class="">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                <line x1="3" y1="9" x2="21" y2="9"></line>
                                                <line x1="9" y1="21" x2="9" y2="9"></line>
                                                </svg>
                                                <span>Project Sheet</span>
                                            </div>
                                        </a>
                                    </li> 

            <?php
        }
    }
    ?>

                            <?php if (!empty($assign_module) && in_array("11", $assign_module)) {
                                ?>

                                <li class="menu">
                                    <a href="#TaskEmployee" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle collapsed">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                            </svg>
                                            <span>TaskSheet</span>
                                        </div>
                                        <div>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                            </svg>
                                        </div>
                                    </a>
                                    <ul class="submenu list-unstyled collapse" id="TaskEmployee" data-parent="#accordionExample" style="">
                                        <li><a href="<?php echo base_url(); ?>tasksheet/mytask"><i
                                                    class="fa fa-files-o"></i><span>My Task Sheet</span></a></li>
                                        <li><a href="<?php echo base_url(); ?>tasksheet/refer_task"><i
                                                    class="fa fa-files-o"></i><span>Refer TaskSheet</span></a></li>
                                    </ul>
                                </li>



        <?php }
    ?>
    <?php if (!empty($assign_module) && in_array("2", $assign_module)) {
        ?>

                                <li class="menu">
                                    <a href="<?php echo base_url(); ?>dsr/assign" aria-expanded="false" class="dropdown-toggle">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="3" y1="9" x2="21" y2="9"></line>
                                            <line x1="9" y1="21" x2="9" y2="9"></line>
                                            </svg>
                                            <span>DSR</span>
                                        </div>
                                    </a>
                                </li> 

                                <!--
                                          <li class="menu">
                                                  <a href="#DSREmployee" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle collapsed">
                                                     <div class="">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users">
                                                           <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                           <circle cx="9" cy="7" r="4"></circle>
                                                           <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                                           <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                                        </svg>
                                                        <span>DSR</span>
                                                     </div>
                                                     <div>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                                                           <polyline points="9 18 15 12 9 6"></polyline>
                                                        </svg>
                                                     </div>
                                                  </a>
                                                  <ul class="submenu list-unstyled collapse" id="DSREmployee" data-parent="#accordionExample" style="">
                                                  
                                                    <li class="treeview">
                                                        <a href="<?php echo base_url(); ?>list_dsr">
                                                            <i class="fa fa-files-o"></i>
                                                            <span>DSR</span>
                                                        </a>
                                                    </li>
                                                      <li class="treeview">
                                                        <a href="<?php echo base_url(); ?>dsr/assign">
                                                            <i class="fa fa-files-o"></i>
                                                            <span>Assign DSR</span>
                                                        </a>
                                                    </li>
                                                  </ul>
                                               </li>-->

        <?php }
    ?>
    <?php if (!empty($assign_module) && in_array("1", $assign_module)) {
        ?>
                                <li class="menu">
                                    <a href="#KPIEmployee" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle collapsed">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                            </svg>
                                            <span>KPI</span>
                                        </div>
                                        <div>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                            </svg>
                                        </div>
                                    </a>
                                    <ul class="submenu list-unstyled collapse" id="KPIEmployee" data-parent="#accordionExample" style="">

                                        <li>
                                            <a href="<?php echo base_url(); ?>get_my_kpi_list">
                                                <i class="fa fa-files-o"></i>
                                                <span>KPI</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo base_url(); ?>user/my_employee_kpi_report">
                                                <i class="fa fa-files-o"></i>
                                                <span>KPI Reports</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>


        <?php }
    ?>






    <?php }
?>

                        <!--  ************************************** HR **************************************************  -->

                        <?php
                        if ($role == '2') {  // HR
                            ?>
                            <?php
                            if (!empty($assign_module) && in_array("6", $assign_module)) {
                                if (!empty($hr_modules) && in_array("6", $hr_modules)) {
                                    //   if (!empty($hr_modules) && in_array("6", $hr_modules)) {
                                    ?>

                                    <li class="menu">
                                        <a href="#KPIEmployee" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle collapsed">
                                            <div class="">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users">
                                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="9" cy="7" r="4"></circle>
                                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                                </svg>
                                                <span>Leave Module</span>
                                            </div>
                                            <div>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                                                <polyline points="9 18 15 12 9 6"></polyline>
                                                </svg>
                                            </div>
                                        </a>
                                        <ul class="submenu list-unstyled collapse" id="KPIEmployee" data-parent="#accordionExample" style="">

                                            <li><a href="<?php echo base_url(); ?>leave/list"><i
                                                        class="fa fa-files-o"></i><span>Leave</span></a></li>
                                            <li><a href="<?php echo base_url(); ?>get_leave_request"><i
                                                        class="fa fa-files-o"></i><span>Applied Leave Request</span></a></li>
                                        </ul>
                                    </li>

            <?php }
    }
    ?>
    <?php
    if (!empty($assign_module) && in_array("9", $assign_module)) {
        if (!empty($hr_modules) && in_array("9", $hr_modules)) {
            ?>
                                    <li class="menu">
                                        <a href="<?php echo base_url() ?>loan" aria-expanded="false" class="dropdown-toggle">
                                            <div class="">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                <line x1="3" y1="9" x2="21" y2="9"></line>
                                                <line x1="9" y1="21" x2="9" y2="9"></line>
                                                </svg>
                                                <span>Loan/Advance</span>
                                            </div>
                                        </a>
                                    </li>
            <?php }
    }
    ?>
                            <?php
                            if (!empty($assign_module) && in_array("18", $assign_module)) {
                                if (!empty($hr_modules) && in_array("18", $hr_modules)) {
                                    ?>
                                    <li class="menu">
                                        <a href="#attendance" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle collapsed">
                                            <div class="">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users">
                                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="9" cy="7" r="4"></circle>
                                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                                </svg>
                                                <span>Attendance</span>
                                            </div>
                                            <div>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                                                <polyline points="9 18 15 12 9 6"></polyline>
                                                </svg>
                                            </div>
                                        </a>
                                        <ul class="submenu list-unstyled collapse" id="attendance" data-parent="#attendance" style="">
                                            <li>
                                                <a href="<?php echo base_url() ?>attendance/listall"> Attendance Report </a>
                                            </li>  
                                            <li>
                                                <a href="<?php echo base_url(); ?>attendance/get_request"> Mis punch requests </a>
                                            </li>
                                            <li>
                                                <a href="<?php echo base_url(); ?>holiday/get_weekoff_request"> Week Off requests </a>
                                            </li>

                                        </ul>
                                    </li>
        <?php }
    } ?>
                            <?php
                            if (!empty($assign_module) && in_array("20", $assign_module)) {
                                if (!empty($hr_modules) && in_array("20", $hr_modules)) {
                                    ?>
                                    <li class="menu">
                                        <a href="<?php echo base_url() ?>resignation/get_request" aria-expanded="false" class="dropdown-toggle">
                                            <div class="">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                <line x1="3" y1="9" x2="21" y2="9"></line>
                                                <line x1="9" y1="21" x2="9" y2="9"></line>
                                                </svg>
                                                <span>Resignation <br/><span style="padding-left:35px">Requests</span></span>
                                            </div>
                                        </a>
                                    </li>
                                <?php }
                            } ?>
                            <?php
                            if (!empty($assign_module) && in_array("21", $assign_module)) {
                                if (!empty($hr_modules) && in_array("21", $hr_modules)) {
                                    ?>
                                    <li class="menu">
                                        <a href="<?php echo base_url() ?>settlement/" aria-expanded="false" class="dropdown-toggle">
                                            <div class="">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                <line x1="3" y1="9" x2="21" y2="9"></line>
                                                <line x1="9" y1="21" x2="9" y2="9"></line>
                                                </svg>
                                                <span>Full & Final <br/><span style="padding-left:35px"> settlement </span> </span>
                                            </div>
                                        </a>
                                    </li>
                                <?php }
                            } ?>
                            <?php
                            if (!empty($assign_module) && in_array("5", $assign_module)) {
                                if (!empty($hr_modules) && in_array("5", $hr_modules)) {
                                    ?>
                                    <li class="menu">
                                        <a href="<?php echo base_url() ?>travel_policy_form" aria-expanded="false" class="dropdown-toggle">
                                            <div class="">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                <line x1="3" y1="9" x2="21" y2="9"></line>
                                                <line x1="9" y1="21" x2="9" y2="9"></line>
                                                </svg>
                                                <span>Expense</span>
                                            </div>
                                        </a>
                                    </li> 

                                    <?php }
                            }
                            ?>
    <?php if (!empty($assign_module) && in_array("7", $assign_module)) {
        ?>
                                <!-- <li class="menu">
                                                  <a href="<?php echo base_url() ?>letter_list" aria-expanded="false" class="dropdown-toggle">
                                                     <div class="">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                                           <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                           <line x1="3" y1="9" x2="21" y2="9"></line>
                                                           <line x1="9" y1="21" x2="9" y2="9"></line>
                                                        </svg>
                                                        <span>Hr Annexure & General policy</span>
                                                     </div>
                                                  </a>
                                              </li>  -->


                                <?php }
                            ?>
                            <?php if (!empty($assign_module) && in_array("7", $assign_module)) {
                                ?>

                                <?php }
                            ?>
    <?php if (!empty($assign_module) && in_array("2", $assign_module)) {
        ?>

                                <li class="menu">
                                    <a href="<?php echo base_url() ?>get_employee_report_dsr" aria-expanded="false" class="dropdown-toggle">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="3" y1="9" x2="21" y2="9"></line>
                                            <line x1="9" y1="21" x2="9" y2="9"></line>
                                            </svg>
                                            <span>DSR</span>
                                        </div>
                                    </a>
                                </li> 


                                <?php }
                            ?>
    <?php if (!empty($assign_module) && in_array("4", $assign_module)) {
        ?>

                                <li class="menu">
                                    <a href="<?php echo base_url() ?>employee_worksheet" aria-expanded="false" class="dropdown-toggle">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="3" y1="9" x2="21" y2="9"></line>
                                            <line x1="9" y1="21" x2="9" y2="9"></line>
                                            </svg>
                                            <span>Worksheet</span>
                                        </div>
                                    </a>
                                </li> 


        <?php }
    ?>

                            <!-- <li class="menu">
                                              <a href="<?php echo base_url() ?>company_letter" aria-expanded="false" class="dropdown-toggle">
                                                 <div class="">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                                       <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                       <line x1="3" y1="9" x2="21" y2="9"></line>
                                                       <line x1="9" y1="21" x2="9" y2="9"></line>
                                                    </svg>
                                                    <span>General Policy</span>
                                                 </div>
                                              </a>
                                          </li>  -->


                            <!-- <li class="menu">
                                              <a href="<?php echo base_url() ?>payroll/payroll_master" aria-expanded="false" class="dropdown-toggle">
                                                 <div class="">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                                       <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                       <line x1="3" y1="9" x2="21" y2="9"></line>
                                                       <line x1="9" y1="21" x2="9" y2="9"></line>
                                                    </svg>
                                                    <span>Payroll Master</span>
                                                 </div>
                                              </a>
                                          </li>  -->


                            <!-- <li class="menu">
                                              <a href="<?php echo base_url() ?>payroll/payroll_company_master" aria-expanded="false" class="dropdown-toggle">
                                                 <div class="">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                                       <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                       <line x1="3" y1="9" x2="21" y2="9"></line>
                                                       <line x1="9" y1="21" x2="9" y2="9"></line>
                                                    </svg>
                                                    <span>Create Payroll</span>
                                                 </div>
                                              </a>
                                          </li>  -->

    <?php }
?>
                    </ul>
                </nav>
            </div>
            <!--  END SIDEBAR  -->