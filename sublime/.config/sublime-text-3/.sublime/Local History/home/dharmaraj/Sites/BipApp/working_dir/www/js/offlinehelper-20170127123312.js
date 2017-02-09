/********* Task Related Functions  ---- Training List page--*****sdfasdfasdfasdf*/
var offlinehelper = {
    count: 0,
    syncstarted: false,
    currentpage: "",
    synctime: 0,
    offlineInterval: 0,
    hidealert: false,
    registrationsync: false,
    othermodulessynced: false,
    isSelfHarm: true,
    trainingsycn: false,
    loginstarted: false,
    syncInterval: 0,
    syncIntervalOnRegistration: 0,
    datafetched: true,
    responseDataTextChunk: {},
    EnabledModules: {},
    createdTables: [],
    synccomplete: 0,
    isLoggedOut: false,
    registrationSaveCount: 0, //Used while saving registartion

    dataForServer: {
        'training': [],
        'Registraion': {
            'answers': [],
            'answercat': [],
            'patientAssignment': [],
            'patientAssignmentDetail': [],
            'crisisplan': [],
            'homework_assignment': []
        },
        'other_modules': {
            'feelingAssignments': [],
            'thoughtAssignments': [],
            'exposureAssignments': [],
            'skillAssignments': []
        }
    },
    checkFirstTime: function(success) {
        sqlhelper.initiateDatabase();
        sqlhelper.db.transaction(function(tx) {
            tx.executeSql("SELECT * from tbl_user;", [], function(txs, results) {

                console.log("Result data length is " + results.rows.length);
                console.log('existing app user',results.rows.item(0).tokenkey);
                $.jStorage.set('bip_jwt',results.rows.item(0).tokenkey)

                if (results != undefined && results.rows != undefined && results.rows.length > 0) {
                    console.log("App is not running for first time");
                    success(false);
                } else {

                    if (navigator.onLine == true) {
                        offlinehelper.initiateDatabase(function() {
                            success(true);
                        });
                        console.log("App is running for first time. Create all necessary tables");
                    } else {
                        console.log("App is running for first time but device is offline.");
                        success("error");

                    }
                    // sqlhelper.initiateTables();
                }
            }, function(error) {
                //First time user
                console.log("error " + error);
                if (navigator.onLine == true) {
                    offlinehelper.initiateDatabase(function() {
                        success(true);
                    });
                    console.log("App is running for first time. Create all necessary tables");

                } else {
                    console.log("App is running for first time but device is offline.");
                    success("error");

                }
                // sqlhelper.initiateTables();

            });
        });
    },

    initiateDatabase: function(success) {
        $(".download-content-msg").show();
        $(".download-overlay").show();
        //console.warn("DATABASE INITIALIZATION START");
        //Version 1 tables structure
        var tabledata = {
            'tablename': 'tbl_user',
            'fielddatas': [{
                    "field": "app_user_id",
                    "datatype": "integer",
                    "primary_key": 1
                }, {
                    "field": "tokenkey",
                    "datatype": "text",
                    "primary_key": 0
                }, {
                    "field": "user_id",
                    "datatype": "integer",
                    "primary_key": 0
                }, {
                    "field": "username",
                    "datatype": "text",
                    "primary_key": 0
                }, {
                    "field": "password",
                    "datatype": "text",
                    "primary_key": 0
                }, {
                    "field": "fullname",
                    "datatype": "integer",
                    "primary_key": 0
                }, {
                    "field": "new_start_page",
                    "datatype": "integer",
                    "primary_key": 0
                }, {
                    "field": "enable_msg_alert",
                    "datatype": "integer",
                    "primary_key": 0
                }, {
                    "field": "stage_number",
                    "datatype": "integer",
                    "primary_key": 0
                }, {
                    "field": "training",
                    "datatype": "integer",
                    "primary_key": 0
                }, {
                    "field": "hasRegistrations",
                    "datatype": "integer",
                    "primary_key": 0
                }, {
                    "field": "homeworks",
                    "datatype": "text",
                    "primary_key": 0
                }, {
                    "field": "lastSyncedDate2",
                    "datatype": "text",
                    "primary_key": 0
                }, {
                    "field": "reminders",
                    "datatype": "text",
                    "primary_key": 0
                }, {
                    "field": "feedbackMessage",
                    "datatype": "text",
                    "primary_key": 0
                }, {
                    "field": "crisisplans",
                    "datatype": "text",
                    "primary_key": 0

                }, {
                    "field": "specialAnswers",
                    "datatype": "text",
                    "primary_key": 0

                }, {
                    "field": "availableModules",
                    "datatype": "text",
                    "primary_key": 0

                }, {
                    "field": "hide_graph",
                    "datatype": "integer",
                    "primary_key": 0
                }

            ]
        };

        sqlhelper.CreateTable(tabledata, function() {

            console.log("Table user created successfully");
            offlinehelper.createdTables.push("tbl_user");
        });



        var tabledata = {
            'tablename': 'tbl_tasks',
            'fielddatas': [{
                    "field": "task_id",
                    "datatype": "integer",
                    "primary_key": 0
                }, {
                    "field": "task_tag",
                    "datatype": "text",
                    "primary_key": 0
                }, {
                    "field": "task_heading",
                    "datatype": "text",
                    "primary_key": 0
                }, {
                    "field": "task_hide_graph",
                    "datatype": "integer",
                    "primary_key": 0
                }, {
                    "field": "app_task_id",
                    "datatype": "integer",
                    "primary_key": 1
                }

            ]
        };


        sqlhelper.CreateTable(tabledata, function() {
            console.log("Table tbl_tasks created successfully");
            offlinehelper.createdTables.push("tbl_tasks");
        });

        var tabledata = {
            'tablename': 'tbl_training',
            'fielddatas': [{
                    "field": "app_training_id",
                    "datatype": "integer",
                    "primary_key": 1
                }, {
                    "field": "training_id",
                    "datatype": "integer",
                    "primary_key": 0
                }, {
                    "field": "task_id",
                    "datatype": "text",
                    "primary_key": 0
                }, {
                    "field": "trainingDateTime",
                    "datatype": "text",
                    "primary_key": 0
                }, {
                    "field": "estimatedValue",
                    "datatype": "text",
                    "primary_key": 0
                }, {
                    "field": "estimatedValueEnd",
                    "datatype": "integer",
                    "primary_key": 0
                }, {
                    "field": "trainingDuration",
                    "datatype": "text",
                    "primary_key": 0
                }, {
                    "field": "type",
                    "datatype": "text",
                    "primary_key": 0
                }, {
                    "field": "comment",
                    "datatype": "text",
                    "primary_key": 0
                }, {
                    "field": "edited",
                    "datatype": "integer",
                    "primary_key": 0
                }

            ]
        };

        sqlhelper.CreateTable(tabledata, function() {
            console.log("Table tbl_training created successfully");
            offlinehelper.createdTables.push("tbl_training");
        });


        //Version table structure



        var tabledata = {
            'tablename': 'tbl_registrations',
            'fielddatas': [{
                "field": "app_registration_id",
                "datatype": "integer",
                "primary_key": 1
            }, {
                "field": "registration_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "registration_name",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "difficulty_id",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "flow_type",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "added_date",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "last_updated",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "registration_status",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "sort_order",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "bar_color",
                "datatype": "text",
                "primary_key": 0
            }]
        };

        sqlhelper.CreateTable(tabledata, function() {
            console.log("Table tbl_registrations created successfully");
            offlinehelper.createdTables.push("tbl_registrations");

        });



        var tabledata = {
            'tablename': 'tbl_registration_steps',
            'fielddatas': [{
                "field": "app_step_id",
                "datatype": "integer",
                "primary_key": 1
            }, {
                "field": "step_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "step_name",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "registration_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "flow_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "is_multiple_choice",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "max_selection_allowed",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "template",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "show_date",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "show_time",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "time_format",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "answer_text",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "button_text",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "allow_custom_answer",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "allow_edit",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "allow_to_add_answer_category",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "added_date",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "last_updated",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "step_status",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "sort_order",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "special_case",
                "datatype": "integer",
                "primary_key": 0
            }]
        };

        sqlhelper.CreateTable(tabledata, function() {
            console.log("Table tbl_registration_steps created successfully");
            offlinehelper.createdTables.push("tbl_registration_steps");
        });



        var tabledata = {
            'tablename': 'tbl_answer_category',
            'fielddatas': [{
                "field": "app_answer_cat_id",
                "datatype": "integer",
                "primary_key": 1
            }, {
                "field": "answer_cat_id ",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "answer_cat_name",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "step_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "added_date",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "last_updated",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "answer_cat_status",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "sort_order",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "answer_type",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "created_by",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "belongs_to",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "added_by",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "mapp_cat_id",
                "datatype": "text",
                "primary_key": 0
            }]
        };

        sqlhelper.CreateTable(tabledata, function() {
            console.log("Table tbl_answer_category created successfully");
            offlinehelper.createdTables.push("tbl_answer_category");
        });

        var tabledata = {
            'tablename': 'tbl_answers',
            'fielddatas': [{
                "field": "app_answer_id",
                "datatype": "integer",
                "primary_key": 1
            }, {
                "field": "answer_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "answer",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "step_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "answer_cat_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "app_answer_cat_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "added_date",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "last_updated",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "answer_status",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "sort_order",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "answer_type",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "created_by",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "belongs_to",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "added_by",
                "datatype": "integer",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "mapped_answer_id",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "special_answer",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "difficulty_id",
                "datatype": "text",
                "primary_key": 0
            }]
        };

        sqlhelper.CreateTable(tabledata, function() {
            console.log("Table tbl_answers created successfully");
            offlinehelper.createdTables.push("tbl_answers");
        });


        var tabledata = {
            'tablename': 'tbl_homeworks',
            'fielddatas': [{
                "field": "app_homework_id",
                "datatype": "integer",
                "primary_key": 1
            }, {
                "field": "homework_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "headline",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "created_by",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "difficulty_id",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "homework_status",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "hw_type",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "updated_at",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "created_at",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "contents",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "added_by",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "sort_order",
                "datatype": "integer",
                "primary_key": 0
            }]
        };

        sqlhelper.CreateTable(tabledata, function() {
            console.log("Table tbl_homeworks created successfully");
            offlinehelper.createdTables.push("tbl_homeworks");
        });


        var tabledata = {
            'tablename': 'tbl_homework_assignments',
            'fielddatas': [{
                "field": "app_assignment_id",
                "datatype": "integer",
                "primary_key": 1
            }, {
                "field": "assignment_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "homework_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "patient_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "published_by",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "published_date",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "is_published",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "already_viewed",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "updated",
                "datatype": "integer",
                "primary_key": 0
            }]
        };

        sqlhelper.CreateTable(tabledata, function() {
            console.log("Table tbl_homework_assignments created successfully");
            offlinehelper.createdTables.push("tbl_homework_assignments");
        });



        var tabledata = {
            'tablename': 'tbl_crisisplans',
            'fielddatas': [{
                "field": "plan_id",
                "datatype": "integer",
                "primary_key": 1
            }, {
                "field": "difficulty_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "headline",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "contents",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "created_at",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "updated_at",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "plan_type",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "belongs_to",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "added_by",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "plan_status",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "already_read",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "created_by",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "updated",
                "datatype": "integer",
                "primary_key": 0
            }]
        };

        sqlhelper.CreateTable(tabledata, function() {
            console.log("Table tbl_crisisplans created successfully");
            offlinehelper.createdTables.push("tbl_crisisplans");
        });

        var tabledata = {
            'tablename': 'tbl_patient_assignments',
            'fielddatas': [{
                "field": "app_assignment_id",
                "datatype": "integer",
                "primary_key": 1
            }, {
                "field": "assignment_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "assignment_code",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "registration_id",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "flow_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "patient_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "incident_date",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "incident_time",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "answered_date",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "stage_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "edited",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "date_only",
                "datatype": "text",
                "primary_key": 0
            }]
        };

        sqlhelper.CreateTable(tabledata, function() {
            console.log("Table tbl_patient_assignments created successfully");
            offlinehelper.createdTables.push("tbl_patient_assignments");
        });

        var tabledata = {
            'tablename': 'tbl_patient_assignment_details',
            'fielddatas': [{
                "field": "app_assignment_details_id",
                "datatype": "integer",
                "primary_key": 1
            }, {
                "field": "assignment_details_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "app_assignment_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "assignment_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "registration_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "flow_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "step_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "answer_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "app_answer_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "assignment_code",
                "datatype": "text",
                "primary_key": 0
            }]
        };

        sqlhelper.CreateTable(tabledata, function() {
            console.log("Table tbl_patient_assignment_details created successfully");
            offlinehelper.createdTables.push("tbl_patient_assignment_details");
        });


        /*Added by Sabin @ 28th August 2015 >>*/
        //FEELINGS
        var tabledata = {
            'tablename': 'tbl_v2_feelings',
            'fielddatas': [{
                    "field": "app_feeling_id",
                    "datatype": "integer",
                    "primary_key": 1
                }, {
                    "field": "feeling_id",
                    "datatype": "integer",
                    "primary_key": 0
                }, {
                    "field": "difficulty_id",
                    "datatype": "integer",
                    "primary_key": 0
                }, {
                    "field": "feeling_name",
                    "datatype": "text",
                    "primary_key": 0
                }, {
                    "field": "description",
                    "datatype": "text",
                    "primary_key": 0
                }, {
                    "field": "created_at",
                    "datatype": "text",
                    "primary_key": 0
                }, {
                    "field": "last_updated",
                    "datatype": "text",
                    "primary_key": 0
                }, {
                    "field": "created_by",
                    "datatype": "integer",
                    "primary_key": 0
                }, {
                    "field": "feeling_status",
                    "datatype": "integer",
                    "primary_key": 0
                }, {
                    "field": "sort_order",
                    "datatype": "integer",
                    "primary_key": 0
                }

            ]
        };


        sqlhelper.CreateTable(tabledata, function() {
            console.log("Table tbl_v2_feelings created successfully");
            offlinehelper.createdTables.push("tbl_v2_feelings");
        });


        //primary secondary feelings definition
        var tabledata = {
            'tablename': 'tbl_v2_feelings_definition',
            'fielddatas': [{
                "field": "app_def_id",
                "datatype": "integer",
                "primary_key": 1
            }, {
                "field": "def_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "primary_feelings",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "secondary_feelings",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "last_updated",
                "datatype": "text",
                "primary_key": 0
            }]
        };


        sqlhelper.CreateTable(tabledata, function() {
            console.log("Table tbl_v2_feelings_definition created successfully");
            offlinehelper.createdTables.push("tbl_v2_feelings_definition");
        });


        //MODULES
        var tabledata = {
            'tablename': 'tbl_v2_modules',
            'fielddatas': [{
                    "field": "app_module_id",
                    "datatype": "integer",
                    "primary_key": 1
                }, {
                    "field": "module_id",
                    "datatype": "integer",
                    "primary_key": 0
                }, {
                    "field": "module_name",
                    "datatype": "text",
                    "primary_key": 0
                }, {
                    "field": "module_desc",
                    "datatype": "text",
                    "primary_key": 0
                }, {
                    "field": "difficulty_id",
                    "datatype": "integer",
                    "primary_key": 0
                }, {
                    "field": "module_icon",
                    "datatype": "text",
                    "primary_key": 0
                }, {
                    "field": "created_date",
                    "datatype": "text",
                    "primary_key": 0
                }, {
                    "field": "modified_date",
                    "datatype": "text",
                    "primary_key": 0
                }, {
                    "field": "sort_order",
                    "datatype": "integer",
                    "primary_key": 0
                }, {
                    "field": "module_status",
                    "datatype": "integer",
                    "primary_key": 0
                }, {
                    "field": "asset_url",
                    "datatype": "text",
                    "primary_key": 0
                }

            ]
        };


        sqlhelper.CreateTable(tabledata, function() {
            console.log("Table tbl_v2_modules created successfully");
            offlinehelper.createdTables.push("tbl_v2_modules");
        });


        //EXPOSURE ADDED BY PATIENTS

        var tabledata = {
            'tablename': 'tbl_v2_sk_exposure_patients',
            'fielddatas': [{
                    "field": "app_exposure_id",
                    "datatype": "integer",
                    "primary_key": 1
                }, {
                    "field": "exposure_id",
                    "datatype": "integer",
                    "primary_key": 0
                }, {
                    "field": "skill_id",
                    "datatype": "integer",
                    "primary_key": 0
                }, {
                    "field": "exposure_name",
                    "datatype": "text",
                    "primary_key": 0
                }, {
                    "field": "started_date",
                    "datatype": "text",
                    "primary_key": 0
                }, {
                    "field": "closed_date",
                    "datatype": "text",
                    "primary_key": 0
                }, {
                    "field": "added_date",
                    "datatype": "text",
                    "primary_key": 0
                }, {
                    "field": "last_updated",
                    "datatype": "text",
                    "primary_key": 0
                }, {
                    "field": "exposure_status",
                    "datatype": "integer",
                    "primary_key": 0
                }, {
                    "field": "added_by",
                    "datatype": "text",
                    "primary_key": 0
                }, {
                    "field": "added_by_id",
                    "datatype": "integer",
                    "primary_key": 0
                }, {
                    "field": "belongs_to",
                    "datatype": "integer",
                    "primary_key": 0
                }

            ]
        };


        sqlhelper.CreateTable(tabledata, function() {
            console.log("Table tbl_v2_sk_exposure_patients created successfully");
            offlinehelper.createdTables.push("tbl_v2_sk_exposure_patients");
        });



        //EXPOSURE ASSIGNMENTS

        var tabledata = {
            'tablename': 'tbl_v2_sk_exposure_patients_assignments',
            'fielddatas': [{
                "field": "app_assignment_id",
                "datatype": "integer",
                "primary_key": 1
            }, {
                "field": "assignment_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "exposure_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "date_answered",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "patient_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "last_updated",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "rating",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "countdown_timer",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "countdown_completed",
                "datatype": "text",
                "primary_key": 0
            }]
        };


        sqlhelper.CreateTable(tabledata, function() {
            console.log("Table tbl_v2_sk_exposure_patients_assignments created successfully");
            offlinehelper.createdTables.push("tbl_v2_sk_exposure_patients_assignments");
        });


        //EXPOSURE ASSIGNMENTS DETAILS
        var tabledata = {
            'tablename': 'tbl_v2_sk_exposure_patients_assignments_details',
            'fielddatas': [{
                "field": "app_assignment_details_id",
                "datatype": "integer",
                "primary_key": 1
            }, {
                "field": "assignment_details_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "app_assignment_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "assignment_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "exposure_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "step_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "answer_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "app_answer_id",
                "datatype": "integer",
                "primary_key": 0
            }]
        };


        sqlhelper.CreateTable(tabledata, function() {
            console.log("Table tbl_v2_sk_exposure_patients_assignments_details created successfully");
            offlinehelper.createdTables.push("tbl_v2_sk_exposure_patients_assignments_details");
        });



        //SKILL ASSIGNMENTS

        var tabledata = {
            'tablename': 'tbl_v2_sk_skills_assignments',
            'fielddatas': [{
                "field": "app_assignment_id",
                "datatype": "integer",
                "primary_key": 1
            }, {
                "field": "assignment_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "skill_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "date_answered",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "patient_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "last_updated",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "rating",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "countdown_timer",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "countdown_completed",
                "datatype": "text",
                "primary_key": 0
            }]
        };


        sqlhelper.CreateTable(tabledata, function() {
            console.log("Table tbl_v2_sk_skills_assignments created successfully");
            offlinehelper.createdTables.push("tbl_v2_sk_skills_assignments");
        });


        //SKILL  ASSIGNMENTS DETAILS
        var tabledata = {
            'tablename': 'tbl_v2_sk_skills_assignments_details',
            'fielddatas': [{
                "field": "app_assignment_details_id",
                "datatype": "integer",
                "primary_key": 1
            }, {
                "field": "assignment_details_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "app_assignment_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "assignment_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "skill_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "step_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "answer_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "app_answer_id",
                "datatype": "integer",
                "primary_key": 0
            }]
        };


        sqlhelper.CreateTable(tabledata, function() {
            console.log("Table tbl_v2_sk_skills_assignments_details created successfully");
            offlinehelper.createdTables.push("tbl_v2_sk_skills_assignments_details");
        });



        //EXPOSURE STEPS

        var tabledata = {
            'tablename': 'tbl_v2_sk_exposure_steps',
            'fielddatas': [{
                "field": "app_step_id",
                "datatype": "integer",
                "primary_key": 1
            }, {
                "field": "step_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "step_name",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "module_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "skill_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "skill_type",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "step_label_10",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "step_label_0",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "is_multiple_choice",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "max_selection_allowed",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "template",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "answer_text",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "alternate_text",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "countdown_title",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "countdown_desc",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "allow_custom_answer",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "allow_edit",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "allow_to_add_answer_category",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "added_date",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "last_updated",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "step_status",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "sort_order",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "title_same_as_skill_ex_name",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "enable_countdown",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "cntdown_min_minutes",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "cntdown_max_minutes",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "cntdown_start_title",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "cntdown_start_desc",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "cntdown_countdown_desc",
                "datatype": "text",
                "primary_key": 0
            }]
        };


        sqlhelper.CreateTable(tabledata, function() {
            console.log("Table tbl_v2_sk_exposure_steps created successfully");
            offlinehelper.createdTables.push("tbl_v2_sk_exposure_steps");
        });


        //MANAGE THOUGHTS
        var tabledata = {
            'tablename': 'tbl_v2_sk_thoughts',
            'fielddatas': [{
                "field": "app_thought_id",
                "datatype": "integer",
                "primary_key": 1
            }, {
                "field": "thought_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "skill_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "module_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "headline",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "thought_type",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "thought_text",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "thought_sound_file",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "sound_background_color",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "sound_url",
                "datatype": "text",
                "primary_key": 0
            }]
        };


        sqlhelper.CreateTable(tabledata, function() {
            console.log("Table tbl_v2_sk_thoughts created successfully");
            offlinehelper.createdTables.push("tbl_v2_sk_thoughts");
        });




        //EXPOSURE ANSWER CATEGORY
        var tabledata = {
            'tablename': 'tbl_v2_skill_exposure_answer_category',
            'fielddatas': [{
                "field": "app_answer_cat_id",
                "datatype": "integer",
                "primary_key": 1
            }, {
                "field": "answer_cat_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "answer_cat_name",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "step_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "added_date",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "last_updated",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "answer_cat_status",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "sort_order",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "answer_type",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "created_by",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "belongs_to",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "added_by",
                "datatype": "text",
                "primary_key": 0
            }]
        };


        sqlhelper.CreateTable(tabledata, function() {
            console.log("Table tbl_v2_skill_exposure_answer_category created successfully");
            offlinehelper.createdTables.push("tbl_v2_skill_exposure_answer_category");
        });


        //EXPOSURE ANSWERS
        var tabledata = {
            'tablename': 'tbl_v2_skill_exposure_answers',
            'fielddatas': [{
                "field": "app_answer_id",
                "datatype": "integer",
                "primary_key": 1
            }, {
                "field": "answer_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "answer",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "step_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "answer_cat_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "added_date",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "last_updated",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "answer_status",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "sort_order",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "answer_type",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "created_by",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "belongs_to",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "added_by",
                "datatype": "text",
                "primary_key": 0
            }]
        };


        sqlhelper.CreateTable(tabledata, function() {
            console.log("Table tbl_v2_skill_exposure_answers created successfully");
            offlinehelper.createdTables.push("tbl_v2_skill_exposure_answers");
        });



        //SKILLS
        var tabledata = {
            'tablename': 'tbl_v2_skills',
            'fielddatas': [{
                "field": "app_skill_id",
                "datatype": "integer",
                "primary_key": 1
            }, {
                "field": "skill_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "skill_name",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "module_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "skill_type",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "created_date",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "last_updated",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "added_by",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "created_by",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "skill_status",
                "datatype": "integer",
                "primary_key": 0
            }]
        };


        sqlhelper.CreateTable(tabledata, function() {
            console.log("Table tbl_v2_skills created successfully");
            offlinehelper.createdTables.push("tbl_v2_skills");
        });


        //THOUGHTS ASSIGNMENTS
        var tabledata = {
            'tablename': 'tbl_v2_sk_thoughts_assignments',
            'fielddatas': [{
                "field": "app_assignment_id",
                "datatype": "integer",
                "primary_key": 1
            }, {
                "field": "assignment_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "thought_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "skill_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "patient_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "times_used",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "last_updated",
                "datatype": "text",
                "primary_key": 0
            }]
        };


        sqlhelper.CreateTable(tabledata, function() {
            console.log("Table tbl_v2_sk_thoughts_assignments created successfully");
            offlinehelper.createdTables.push("tbl_v2_sk_thoughts_assignments");
        });


        //FEELINGS ASSIGNMENTS
        var tabledata = {
            'tablename': 'tbl_v2_feelings_assignments',
            'fielddatas': [{
                "field": "app_assignment_id",
                "datatype": "integer",
                "primary_key": 1
            }, {
                "field": "assignment_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "feeling_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "patient_id",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "answered_date",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "module_version",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "feeling_type",
                "datatype": "integer",
                "primary_key": 0
            }, {
                "field": "last_updated",
                "datatype": "text",
                "primary_key": 0
            }]
        };


        sqlhelper.CreateTable(tabledata, function() {
            console.log("Table tbl_v2_feelings_assignments created successfully");
            offlinehelper.createdTables.push("tbl_v2_feelings_assignments");
            success();
        });

        var tabledata = {
            'tablename': 'tbl_extra_files_to_download',
            'fielddatas': [{
                "field": "file_id",
                "datatype": "integer",
                "primary_key": 1
            }, {
                "field": "file_url",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "item_type",
                "datatype": "text",
                "primary_key": 0
            }, {
                "field": "file_name",
                "datatype": "text",
                "primary_key": 0
            }]
        };


        sqlhelper.CreateTable(tabledata, function() {
            console.log("Table tbl_extra_files_to_download created successfully");
            offlinehelper.createdTables.push("tbl_extra_files_to_download");
        });
        //console.warn("DATABASE INITIALIZATION END");
        /*Added by Sabin @ 28th August 2015 <<*/

    },

    syncTable: function(userdetail, ftime) {
        if (offlinehelper.syncstarted == false) {

            offlinehelper.syncstarted = true;
            //inserting data to tbl_user
            var data = {
                'tokenkey': userdetail.tokenkey,
                'user_id': userdetail.userid,
                'username': userdetail.username,
                'password': userdetail.password,
                'fullname': userdetail.Name,
                'new_start_page': userdetail.new_start_page,
                'enable_msg_alert': userdetail.enable_msg_alert,
                'stage_number': userdetail.stage_number,
                'training': JSON.stringify(userdetail.training),
                'hasRegistrations': userdetail.hasRegistration,
                'homeworks': JSON.stringify(userdetail.homeworks),
                'crisisplans': JSON.stringify(userdetail.crisisplans),
                'reminders': JSON.stringify(userdetail.reminder),
                'specialAnswers': userdetail.specialAnswers,
                'feedbackMessage': JSON.stringify(userdetail.feedback),
                'availableModules': JSON.stringify(userdetail.available_modules),
                'hide_graph': userdetail.hide_graph
            };


            sqlhelper.insertJSONData('tbl_user', data);

            var json = '{"userid":"' + userdetail.userid + '","tokenkey":"' + userdetail.tokenkey + '","deviceId":"ABBBSBS"}';


            callWebServiceLive("sync_user_data/first", json, function(response) {
                offlinehelper.syncRegistration(response.data, fnLogin, false);
            });

        }

    },
    syncRegistration: function(ResponseData, callback, showDialog) {
        var totalLengths = -1;
        $tasklength = typeof ResponseData.active_tasks != "undefined" ? offlinehelper.getItemLength(ResponseData.active_tasks.task, "task") : 0;

        totalLengths = $tasklength +
            offlinehelper.getItemLength(ResponseData.other_modules.modules.default.feelings, "feelings") +
            offlinehelper.getItemLength(ResponseData.other_modules.modules.feelings.assignment, "feeling assignments") +
            offlinehelper.getItemLength(ResponseData.other_modules.feeling_definitions, "feeling_definitions") +
            offlinehelper.getItemLength(ResponseData.other_modules.modules.others, "Other modules") +
            offlinehelper.getItemLength(ResponseData.other_modules.modules.skills, "Skills") +
            offlinehelper.getItemLength(ResponseData.other_modules.modules.thoughts, "Thoughts") +
            offlinehelper.getItemLength(ResponseData.other_modules.modules.thoughts_assignment, "Thought assignments") +
            offlinehelper.getItemLength(ResponseData.other_modules.exposure.patient_exposure, "Patient Exposure") +
            offlinehelper.getItemLength(ResponseData.other_modules.exposure.patient_exposure_assignments, "Patient Exposure Assignments") +
            offlinehelper.getItemLength(ResponseData.other_modules.exposure.patient_exposure_assignments_details, "Patient Exposure Assignments Details") +
            offlinehelper.getItemLength(ResponseData.other_modules.exposure.steps, "Exposure Steps") +
            offlinehelper.getItemLength(ResponseData.other_modules.exposure.answer_cats, "Exposure Answer Cats") +
            offlinehelper.getItemLength(ResponseData.other_modules.exposure.answers, "Exposure Answers") +
            offlinehelper.getItemLength(ResponseData.registration_task.homework_module.homeworks, "Homeworks") +
            offlinehelper.getItemLength(ResponseData.registration_task.crisis_plan, "Crisis plans") +
            offlinehelper.getItemLength(ResponseData.registration_task.homework_module.homework_assignments, "Homework Assignments") +
            offlinehelper.getItemLength(ResponseData.registration_task.registration_module.registrations, "Registration Tasks") +
            offlinehelper.getItemLength(ResponseData.registration_task.registration_module.patients.assignments, "Registration Assignments") +
            offlinehelper.getItemLength(ResponseData.registration_task.registration_module.patients.assignment_details, "Registration Assignment Details") +
            offlinehelper.getItemLength(ResponseData.registration_task.registration_module.steps, "Registration Steps") +
            offlinehelper.getItemLength(ResponseData.registration_task.registration_module.answer_category, "Registration answer Cats") +
            offlinehelper.getItemLength(ResponseData.registration_task.registration_module.answers, "Registration Answers") +
            offlinehelper.getItemLength(ResponseData.other_modules.skills.assignments, "Skill Assignments") +
            offlinehelper.getItemLength(ResponseData.other_modules.skills.assignment_details, "Skill assignment details");


        console.log(ResponseData);
        offlinehelper.responseDataTextChunk = ResponseData;
        offlinehelper.resetSyncProgressBar();



        var isValid = true,
            iVal = 0,
            sum = 0,
            pcdone = 0;
        //sync active tasks



        if (showDialog == true && totalLengths > 0) {
            $(".download-content-msg").show();
            $(".download-overlay").show();
        }



        // 

        var otherqueries = [];
        //console.warn("total length = "+totalLengths);
        var tasksync = false;



        if (ResponseData.countdown_audio != "") {
            $testURL = ResponseData.countdown_audio;
            $countdownFile = $testURL.split("/").pop();


            var sqldel = "DELETE FROM tbl_extra_files_to_download WHERE item_type='countdown_audio'";
            otherqueries.push(sqldel);

            var data = {
                'file_url': ResponseData.countdown_audio,
                'item_type': 'countdown_audio',
                'file_name': $countdownFile
            };

            var fields = sqlhelper.separateFieldData(data, "field");
            var dataval = sqlhelper.separateFieldData(data, "value");
            var sqlquery = 'INSERT INTO tbl_extra_files_to_download (' + fields + ') values(' + dataval + ')';
            otherqueries.push(sqlquery);
        }

        if (ResponseData.slide3_image != "") {
            $testURL = ResponseData.slide3_image;
            $countdownFile = $testURL.split("/").pop();

            var sqldel = "DELETE FROM tbl_extra_files_to_download WHERE item_type='slide3_image'";
            otherqueries.push(sqldel);

            var data = {
                'file_url': ResponseData.slide3_image,
                'item_type': 'slide3_image',
                'file_name': $countdownFile
            };

            var fields = sqlhelper.separateFieldData(data, "field");
            var dataval = sqlhelper.separateFieldData(data, "value");
            var sqlquery = 'INSERT INTO tbl_extra_files_to_download (' + fields + ') values(' + dataval + ')';
            otherqueries.push(sqlquery);
        }

        if (typeof ResponseData.active_tasks != "undefined") {
            if (typeof ResponseData.active_tasks.task != "undefined") {

                $(ResponseData.active_tasks.task).each(function(i, e) {
                    taskid = e.taskid;
                    var data = {
                        'task_id': e.taskid,
                        'task_tag': e.tag,
                        'task_heading': e.heading.replace("'", "\'"),
                        'task_hide_graph': e.hide_graph
                    };


                    //  sqlhelper.insertJSONData('tbl_tasks',data);
                    //totalLengths++;
                    //console.log(tx_reg);
                    var fields = sqlhelper.separateFieldData(data, "field");
                    var dataval = sqlhelper.separateFieldData(data, "value");
                    var sqlquery = 'DELETE FROM tbl_tasks where task_id=' + e.taskid;
                    otherqueries.push(sqlquery);
                    var sqlquery = 'INSERT INTO tbl_tasks (' + fields + ') values(' + dataval + ')';
                    otherqueries.push(sqlquery);

                    console.log(sqlquery);




                    if (e.training != undefined && e.training.length != 0) {
                        var traininglength = e.training.length;

                        totalLengths = totalLengths + e.training.length;



                        $(e.training).each(function(i, e) {
                            var data = {
                                'training_id': e.trainingId,
                                'task_id': taskid,
                                'trainingDateTime': e.trainingdatetime,
                                'estimatedValue': e.estimatedvalue,
                                'trainingDuration': e.training_duration,
                                'type': e.type,
                                'comment': e.comment,
                                'edited': e.edited,
                                'estimatedValueEnd': e.estimatedvalue_end,
                            };

                            //sqlhelper.insertJSONData('tbl_training',data);
                            //totalLengths++;

                            var fields = sqlhelper.separateFieldData(data, "field");
                            var dataval = sqlhelper.separateFieldData(data, "value");
                            var sqlquery = 'DELETE FROM tbl_training where training_id=' + e.trainingId;
                            otherqueries.push(sqlquery);
                            var sqlquery = 'INSERT INTO tbl_training (' + fields + ') values(' + dataval + ')';
                            console.log(sqlquery);
                            otherqueries.push(sqlquery);

                        })
                    }

                });

                //console.log(queries);
                // sqlhelper.db.transaction(function(tx) {
                //  for(i=0;i<queries_training.length;i++){
                //    console.log(queries_training[i]);
                //    tx.executeSql(queries_training[i]);

                //    iVal++;
                //    checkSyncComplete(iVal);

                //  }
                //   });
            } else {
                tasksync = true;
            }
        } else {
            tasksync = true;
        }


        var RegistrationData = ResponseData;
        // var module_queries=[];
        if (typeof RegistrationData.other_modules.modules.default.feelings != "undefined") {
            $(RegistrationData.other_modules.modules.default.feelings).each(function(i, e) {



                var todeelete = {
                    where: {
                        'feeling_id': e.feeling_id,
                    }
                };
                var where = sqlhelper.generateUpdateFields(todeelete.where);

                var sqlquery = "DELETE FROM tbl_v2_feelings where " + where;
                otherqueries.push(sqlquery);
                var data = {
                    'feeling_id': e.feeling_id,
                    'difficulty_id': e.difficulty_id,
                    'feeling_name': e.feeling_name,
                    'description': e.description,
                    'created_at': e.created_at,
                    'last_updated': e.last_updated,
                    'created_by': e.created_by,
                    'feeling_status': e.feeling_status,
                    'sort_order': e.sort_order
                };

                //console.warn("Now Processing - FEELINGS");
                //sqlhelper.insertJSONData('tbl_v2_feelings',data);
                //totalLengths++;
                var fields = sqlhelper.separateFieldData(data, "field");
                var dataval = sqlhelper.separateFieldData(data, "value");
                var sqlquery = 'INSERT INTO tbl_v2_feelings (' + fields + ') values(' + dataval + ')';
                otherqueries.push(sqlquery);
                // sqlhelper.insertJSONDataFirstSync(tx_reg,'tbl_v2_feelings', data, function (results) {

                //              if (!results) {
                //                  isValid = false;
                //                  sum++;
                //              }
                //              iVal++;//onSuccess function

                //              checkSyncComplete(iVal);//call this lastly method or each
                //          }, function () {
                //              iVal++;//onError function
                ////              console.warn("Failure Insert tbl_v2_feelings");
                //          });


            });
        }

        // sqlhelper.db.transaction(function(tx) {
        //   for(i=0;i<module_queries.length;i++){
        //     console.log(module_queries[i]);
        //     tx.executeSql(module_queries[i]);

        //     iVal++;
        //     checkSyncComplete(iVal);
        //   }
        // });

        //sync feeling assignments
        //var otherqueries=[];

        if (typeof RegistrationData.other_modules.modules.feelings.assignment != "undefined") {
            $(RegistrationData.other_modules.modules.feelings.assignment).each(function(i, e) {




                var todeelete = {
                    where: {
                        'assignment_id': e.assignment_id,
                    }
                };
                var where = sqlhelper.generateUpdateFields(todeelete.where);

                var sqlquery = "DELETE FROM tbl_v2_feelings_assignments where " + where;
                console.log(sqlquery);
                otherqueries.push(sqlquery);
                var data = {
                    'assignment_id': e.assignment_id,
                    'feeling_id': e.feeling_id,
                    'patient_id': e.patient_id,
                    'answered_date': e.answered_date,
                    'module_version': e.module_version,
                    'feeling_type': e.feeling_type,
                    'last_updated': e.last_updated
                };

                //console.warn("Now Processing - FEELINGS ASSIGNMENTS");
                //sqlhelper.insertJSONData('tbl_v2_feelings',data);
                //totalLengths++;
                // sqlhelper.insertJSONDataFirstSync(tx_reg,'tbl_v2_feelings_assignments', data, function (results) {

                //              if (!results) {
                //                  isValid = false;
                //                  sum++;
                //              }
                //              iVal++;//onSuccess function

                //              checkSyncComplete(iVal);//call this lastly method or each
                //          }, function () {
                //              iVal++;//onError function
                ////              console.warn("Failure Insert tbl_v2_feelings_assignments");
                //          });
                var fields = sqlhelper.separateFieldData(data, "field");
                var dataval = sqlhelper.separateFieldData(data, "value");
                var sqlquery = 'INSERT INTO tbl_v2_feelings_assignments (' + fields + ') values(' + dataval + ')';
                console.log(sqlquery);
                otherqueries.push(sqlquery);


            });
        }

        //sync feeling definitions
        if (RegistrationData.other_modules.feeling_definitions !== 0) {


            var todeelete = {
                where: {
                    'def_id': RegistrationData.other_modules.feeling_definitions.def_id,
                }
            };
            var where = sqlhelper.generateUpdateFields(todeelete.where);

            var sqlquery = "DELETE FROM tbl_v2_feelings_definition where " + where;
            console.log(sqlquery);
            otherqueries.push(sqlquery);
            var data = {
                "def_id": RegistrationData.other_modules.feeling_definitions.def_id,
                "primary_feelings": RegistrationData.other_modules.feeling_definitions.primary,
                "secondary_feelings": RegistrationData.other_modules.feeling_definitions.secondary
            }

            //console.warn("Now Processing - FEELINGS DEFINITIONS");
            //sqlhelper.insertJSONData("tbl_v2_feelings_definition",data);
            //totalLengths++;
            // sqlhelper.insertJSONDataFirstSync(tx_reg,'tbl_v2_feelings_definition', data, function (results) {

            //                   if (!results) {
            //                       isValid = false;
            //                       sum++;
            //                   }
            //                   iVal++;//onSuccess function

            //                   checkSyncComplete(iVal);//call this lastly method or each
            //               }, function () {
            //                   iVal++;//onError function
            ////                   console.warn("Failure Insert tbl_v2_feelings_definition");
            //               });
            var fields = sqlhelper.separateFieldData(data, "field");
            var dataval = sqlhelper.separateFieldData(data, "value");
            var sqlquery = 'INSERT INTO tbl_v2_feelings_definition (' + fields + ') values(' + dataval + ')';
            console.log(sqlquery);
            otherqueries.push(sqlquery);

        }

        //sync modules
        if (typeof RegistrationData.other_modules.modules.others != "undefined") {
            $(RegistrationData.other_modules.modules.others).each(function(i, e) {



                var todeelete = {
                    where: {
                        'module_id': e.module_id,
                    }
                };
                var where = sqlhelper.generateUpdateFields(todeelete.where);

                var sqlquery = "DELETE FROM tbl_v2_modules where " + where;
                // console.log(sqlquery);
                otherqueries.push(sqlquery);
                var data = {
                    'module_id': e.module_id,
                    'module_name': e.module_name.replace("'", "\'"),
                    'module_desc': e.module_desc,
                    'difficulty_id': e.difficulty_id,
                    'module_icon': e.module_icon,
                    'created_date': e.created_date,
                    'modified_date': e.modified_date,
                    'module_status': e.module_status,
                    'sort_order': e.sort_order,
                    'asset_url': e.assetURL
                };

                /* if(e.assetURL!=""){
                     var fileInfo = {
                                          'file': e.module_icon,
                                          'type': "icon",
                                          'url': e.assetURL
                                      };

                      offlinehelper.modulefiles.push(fileInfo);
                  }*/
                //console.warn("Now Processing - MODULES");
                //sqlhelper.insertJSONData("tbl_v2_modules",data);

                //totalLengths++;
                // sqlhelper.insertJSONDataFirstSync(tx_reg,'tbl_v2_modules', data, function (results) {

                //      if (!results) {
                //          isValid = false;
                //          sum++;
                //      }
                //      iVal++;//onSuccess function

                //      checkSyncComplete(iVal);//call this lastly method or each
                //  }, function () {
                //      iVal++;//onError function
                ////      console.warn("Failure Insert tbl_v2_modules");
                //  });
                var fields = sqlhelper.separateFieldData(data, "field");
                var dataval = sqlhelper.separateFieldData(data, "value");
                var sqlquery = 'INSERT INTO tbl_v2_modules (' + fields + ') values(' + dataval + ')';
                console.log(sqlquery);
                otherqueries.push(sqlquery);

            });
        }

        //sync skills
        if (typeof RegistrationData.other_modules.modules.skills != "undefined") {
            $(RegistrationData.other_modules.modules.skills).each(function(i, e) {



                var todeelete = {
                    where: {
                        'skill_id': e.skill_id,
                    }
                };
                var where = sqlhelper.generateUpdateFields(todeelete.where);

                var sqlquery = "DELETE FROM tbl_v2_skills where " + where;
                console.log(sqlquery);
                otherqueries.push(sqlquery);
                var data = {
                    'skill_id': e.skill_id,
                    'skill_name': e.skill_name,
                    'module_id': e.module_id,
                    'skill_type': e.skill_type,
                    'created_date': e.created_date,
                    'last_updated': e.last_updated,
                    'added_by': e.added_by,
                    'created_by': e.created_by,
                    'skill_status': e.skill_status
                };

                //console.warn("Now Processing - SKILLS");
                //sqlhelper.insertJSONData("tbl_v2_skills",data);
                //totalLengths++;
                // sqlhelper.insertJSONDataFirstSync(tx_reg,'tbl_v2_skills', data, function (results) {

                //             if (!results) {
                //                 isValid = false;
                //                 sum++;
                //             }
                //             iVal++;//onSuccess function

                //             checkSyncComplete(iVal);//call this lastly method or each
                //         }, function () {
                //             iVal++;//onError function
                ////             console.warn("Failure Insert tbl_v2_skills");
                //         });
                var fields = sqlhelper.separateFieldData(data, "field");
                var dataval = sqlhelper.separateFieldData(data, "value");
                var sqlquery = 'INSERT INTO tbl_v2_skills (' + fields + ') values(' + dataval + ')';
                console.log(sqlquery);
                otherqueries.push(sqlquery);

            });
        }

        //sync thought contents
        if (typeof RegistrationData.other_modules.modules.thoughts != "undefined") {
            $(RegistrationData.other_modules.modules.thoughts).each(function(i, e) {



                var todeelete = {
                    where: {
                        'thought_id': e.thought_id,
                    }
                };
                var where = sqlhelper.generateUpdateFields(todeelete.where);

                var sqlquery = "DELETE FROM tbl_v2_sk_thoughts where " + where;
                console.log(sqlquery);
                otherqueries.push(sqlquery);
                var data = {
                    "thought_id": e.thought_id,
                    "skill_id": e.skill_id,
                    "module_id": e.module_id,
                    "headline": e.headline,
                    "thought_type": e.thought_type,
                    "thought_text": e.thought_text,
                    "thought_sound_file": e.thought_sound_file,
                    "sound_background_color": e.sound_background_color,
                    "sound_url": e.sound_url
                };
                /*if(e.sound_url!=""){
                    offlinehelper.modulefiles.push({
                        'file': e.thought_sound_file,
                        'type': "audio",
                        'url': e.sound_url
                    });
                }*/
                //console.warn("Now Processing - THOUGHTS");
                //sqlhelper.insertJSONData("tbl_v2_sk_thoughts",data);
                //totalLengths++;
                // sqlhelper.insertJSONDataFirstSync(tx_reg,'tbl_v2_sk_thoughts', data, function (results) {

                //                 if (!results) {
                //                     isValid = false;
                //                     sum++;
                //                 }

                //                 iVal++;//onSuccess function

                //                 checkSyncComplete(iVal);//call this lastly method or each
                //             }, function () {
                //                 iVal++;//onError function
                ////                 console.warn("Failure Insert tbl_v2_sk_thoughts");
                //             });
                var fields = sqlhelper.separateFieldData(data, "field");
                var dataval = sqlhelper.separateFieldData(data, "value");
                var sqlquery = 'INSERT INTO tbl_v2_sk_thoughts (' + fields + ') values(' + dataval + ')';
                console.log(sqlquery);
                otherqueries.push(sqlquery);
            });

        }

        //Sync Thoughts

        if (typeof RegistrationData.other_modules.modules.thoughts_assignment != "undefined") {
            $(RegistrationData.other_modules.modules.thoughts_assignment).each(function(i, e) {

                var todeelete = {
                    where: {
                        'assignment_id': e.assignment_id,
                    }
                };
                var where = sqlhelper.generateUpdateFields(todeelete.where);

                var sqlquery = "DELETE FROM tbl_v2_sk_thoughts_assignments where " + where;
                console.log(sqlquery);
                otherqueries.push(sqlquery);
                var data = {
                    'assignment_id': e.assignment_id,
                    'thought_id': e.thought_id,
                    'patient_id': e.patient_id,
                    'skill_id': e.skill_id,
                    'times_used': e.times_used,
                    'last_updated': e.last_updated
                };

                //console.warn("Now Processing - THOUGHTS ASSIGNMENTS");


                // sqlhelper.insertJSONDataFirstSync(tx_reg,'tbl_v2_sk_thoughts_assignments', data, function (results) {

                //               if (!results) {
                //                   isValid = false;
                //                   sum++;
                //               }
                //               iVal++;//onSuccess function

                //               checkSyncComplete(iVal);//call this lastly method or each
                //           }, function () {
                //               iVal++;//onError function
                ////               console.warn("Failure Insert tbl_v2_sk_thoughts_assignments");
                //           });
                var fields = sqlhelper.separateFieldData(data, "field");
                var dataval = sqlhelper.separateFieldData(data, "value");
                var sqlquery = 'INSERT INTO tbl_v2_sk_thoughts_assignments (' + fields + ') values(' + dataval + ')';
                console.log(sqlquery);
                otherqueries.push(sqlquery);


            });
        }

        //sync patient specific exposure
        if (typeof RegistrationData.other_modules.exposure.patient_exposure != "undefined") {
            $(RegistrationData.other_modules.exposure.patient_exposure).each(function(i, e) {



                var todeelete = {
                    where: {
                        'exposure_id': e.exposure_id,
                    }
                };
                var where = sqlhelper.generateUpdateFields(todeelete.where);

                var sqlquery = "DELETE FROM tbl_v2_sk_exposure_patients where " + where;
                console.log(sqlquery);
                otherqueries.push(sqlquery);
                var data = {
                    "exposure_id": e.exposure_id,
                    "skill_id": e.skill_id,
                    "exposure_name": e.exposure_name,
                    "started_date": e.started_date,
                    "closed_date": e.closed_date,
                    "added_date": e.added_date,
                    "last_updated": e.last_updated,
                    "exposure_status": e.exposure_status,
                    "added_by": e.added_by,
                    "added_by_id": e.added_by_id,
                    "belongs_to": e.belongs_to
                };

                //console.warn("Now Processing - PATIENT'S EXPOSURE");
                //sqlhelper.insertJSONData("tbl_v2_sk_exposure_patients",data);
                //totalLengths++;

                // sqlhelper.insertJSONDataFirstSync(tx_reg,'tbl_v2_sk_exposure_patients', data, function (results) {

                //                if (!results) {
                //                    isValid = false;
                //                    sum++;
                //                }
                //                iVal++;//onSuccess function
                ////                console.warn("Success Insert tbl_v2_sk_exposure_patients");
                //                checkSyncComplete(iVal);//call this lastly method or each
                //            }, function () {
                //                iVal++;//onError function
                ////                console.warn("Failure Insert tbl_v2_sk_exposure_patients");
                //            });
                var fields = sqlhelper.separateFieldData(data, "field");
                var dataval = sqlhelper.separateFieldData(data, "value");
                var sqlquery = 'INSERT INTO tbl_v2_sk_exposure_patients (' + fields + ') values(' + dataval + ')';
                console.log(sqlquery);
                otherqueries.push(sqlquery);

            });
        }

        //sync patient's answered exposure assignments
        if (typeof RegistrationData.other_modules.exposure.patient_exposure_assignments != "undefined") {
            $(RegistrationData.other_modules.exposure.patient_exposure_assignments).each(function(i, e) {




                var todeelete = {
                    where: {
                        'assignment_id': e.assignment_id,
                    }
                };
                var where = sqlhelper.generateUpdateFields(todeelete.where);

                var sqlquery = "DELETE FROM tbl_v2_sk_exposure_patients_assignments where " + where;
                console.log(sqlquery);
                otherqueries.push(sqlquery);
                var data = {
                    "assignment_id": e.assignment_id,
                    "exposure_id": e.exposure_id,
                    "date_answered": e.date_answered,
                    "patient_id": e.patient_id,
                    "last_updated": e.last_updated,
                    "rating": e.rating,
                    "countdown_timer": e.countdown_timer,
                    "countdown_completed": e.countdown_completed
                };

                //console.warn("Now Processing - EXPOSURE ASSIGNMENTS");
                //sqlhelper.insertJSONData("tbl_v2_sk_exposure_patients_assignments",data);
                //totalLengths++;
                // sqlhelper.insertJSONDataFirstSync(tx_reg,'tbl_v2_sk_exposure_patients_assignments', data, function (results) {

                //              if (!results) {
                //                  isValid = false;
                //                  sum++;
                //              }
                //              iVal++;//onSuccess function

                //              checkSyncComplete(iVal);//call this lastly method or each
                //          }, function () {
                //              iVal++;//onError function
                ////              console.warn("Failure Insert tbl_v2_sk_exposure_patients_assignments");
                //          });
                var fields = sqlhelper.separateFieldData(data, "field");
                var dataval = sqlhelper.separateFieldData(data, "value");
                var sqlquery = 'INSERT INTO tbl_v2_sk_exposure_patients_assignments (' + fields + ') values(' + dataval + ')';
                console.log(sqlquery);
                otherqueries.push(sqlquery);

            });
        }


        //sync patient's answered exposure assignments details
        if (typeof RegistrationData.other_modules.exposure.patient_exposure_assignments_details != "undefined") {
            $(RegistrationData.other_modules.exposure.patient_exposure_assignments_details).each(function(i, e) {


                var todeelete = {
                    where: {
                        'assignment_details_id': e.assignment_details_id,
                    }
                };
                var where = sqlhelper.generateUpdateFields(todeelete.where);

                var sqlquery = "DELETE FROM tbl_v2_sk_exposure_patients_assignments_details where " + where;
                console.log(sqlquery);
                otherqueries.push(sqlquery);
                var data = {
                    "assignment_details_id": e.assignment_details_id,
                    "assignment_id": e.assignment_id,
                    "exposure_id": e.exposure_id,
                    "step_id": e.step_id,
                    "answer_id": e.answer_id
                };

                //console.warn("Now Processing - EXPOSURE ASSIGNMENT DETAILS");
                //sqlhelper.insertJSONData("tbl_v2_sk_exposure_patients_assignments_details",data);
                //totalLengths++;
                // sqlhelper.insertJSONDataFirstSync(tx_reg,'tbl_v2_sk_exposure_patients_assignments_details', data, function (results) {

                //              if (!results) {
                //                  isValid = false;
                //                  sum++;
                //              }
                //              iVal++;//onSuccess function

                //              checkSyncComplete(iVal);//call this lastly method or each
                //          }, function () {
                //              iVal++;//onError function
                ////              console.warn("Failure Insert tbl_v2_sk_exposure_patients_assignments_details");
                //          });
                var fields = sqlhelper.separateFieldData(data, "field");
                var dataval = sqlhelper.separateFieldData(data, "value");
                var sqlquery = 'INSERT INTO tbl_v2_sk_exposure_patients_assignments_details (' + fields + ') values(' + dataval + ')';
                console.log(sqlquery);
                otherqueries.push(sqlquery);

            });
        }

        //sync skills response.data.other_modules.skills.assignments
        if (typeof RegistrationData.other_modules.skills.assignments != "undefined") {
            $(RegistrationData.other_modules.skills.assignments).each(function(i, e) {



                var todeelete = {
                    where: {
                        'assignment_id': e.assignment_id,
                    }
                };
                var where = sqlhelper.generateUpdateFields(todeelete.where);

                var sqlquery = "DELETE FROM tbl_v2_sk_skills_assignments where " + where;
                console.log(sqlquery);
                otherqueries.push(sqlquery);
                var data = {
                    "assignment_id": e.assignment_id,
                    "skill_id": e.skill_id,
                    "date_answered": e.date_answered,
                    "patient_id": e.patient_id,
                    "last_updated": e.last_updated,
                    "rating": e.rating,
                    "countdown_timer": e.countdown_timer,
                    "countdown_completed": e.countdown_completed
                };

                //console.warn("Now Processing - SKILL ASSIGNMENTS");
                //sqlhelper.insertJSONData("tbl_v2_sk_exposure_patients_assignments",data);
                //totalLengths++;
                // sqlhelper.insertJSONDataFirstSync(tx_reg,'tbl_v2_sk_skills_assignments', data, function (results) {

                //              if (!results) {
                //                  isValid = false;
                //                  sum++;
                //              }
                //              iVal++;//onSuccess function

                //              checkSyncComplete(iVal);//call this lastly method or each
                //          }, function () {
                //              iVal++;//onError function
                ////              console.warn("Failure Insert tbl_v2_sk_skills_assignments");
                //          });
                var fields = sqlhelper.separateFieldData(data, "field");
                var dataval = sqlhelper.separateFieldData(data, "value");
                var sqlquery = 'INSERT INTO tbl_v2_sk_skills_assignments (' + fields + ') values(' + dataval + ')';
                console.log(sqlquery);
                otherqueries.push(sqlquery);

            });
        }

        //sync skills details
        if (typeof RegistrationData.other_modules.skills.assignment_details != "undefined") {
            $(RegistrationData.other_modules.skills.assignment_details).each(function(i, e) {



                var todeelete = {
                    where: {
                        'assignment_details_id': e.assignment_details_id,
                    }
                };
                var where = sqlhelper.generateUpdateFields(todeelete.where);

                var sqlquery = "DELETE FROM tbl_v2_sk_skills_assignments_details where " + where;
                console.log(sqlquery);
                otherqueries.push(sqlquery);
                var data = {
                    "assignment_details_id": e.assignment_details_id,
                    "assignment_id": e.assignment_id,
                    "skill_id": e.skill_id,
                    "step_id": e.step_id,
                    "answer_id": e.answer_id
                };

                //console.warn("Now Processing - SKILL ASSIGNMENT DETAILS");
                //sqlhelper.insertJSONData("tbl_v2_sk_exposure_patients_assignments_details",data);
                //totalLengths++;
                // sqlhelper.insertJSONDataFirstSync(tx_reg,'tbl_v2_sk_skills_assignments_details', data, function (results) {

                //              if (!results) {
                //                  isValid = false;
                //                  sum++;
                //              }
                //              iVal++;//onSuccess function

                //              checkSyncComplete(iVal);//call this lastly method or each
                //          }, function () {
                //              iVal++;//onError function
                ////              console.warn("Failure Insert tbl_v2_sk_skills_assignments_details");
                //          });
                var fields = sqlhelper.separateFieldData(data, "field");
                var dataval = sqlhelper.separateFieldData(data, "value");
                var sqlquery = 'INSERT INTO tbl_v2_sk_skills_assignments_details (' + fields + ') values(' + dataval + ')';
                console.log(sqlquery);
                otherqueries.push(sqlquery);

            });
        }

        //sync exposure master templates(steps)
        if (typeof RegistrationData.other_modules.exposure.steps != "undefined") {
            $(RegistrationData.other_modules.exposure.steps).each(function(i, e) {




                var todeelete = {
                    where: {
                        'step_id': e.step_id,
                    }
                };
                var where = sqlhelper.generateUpdateFields(todeelete.where);

                var sqlquery = "DELETE FROM tbl_v2_sk_exposure_steps where " + where;
                console.log(sqlquery);
                otherqueries.push(sqlquery);
                var data = {
                    "step_id": e.step_id,
                    "step_name": e.step_name,
                    "module_id": e.module_id,
                    "skill_id": e.skill_id,
                    "skill_type": e.skill_type,
                    "step_label_10": e.step_label_10,
                    "step_label_0": e.step_label_0,
                    "is_multiple_choice": e.is_multiple_choice,
                    "max_selection_allowed": e.max_selection_allowed,
                    "template": e.template,
                    "answer_text": e.answer_text,
                    "alternate_text": e.alternate_text,
                    "countdown_title": e.countdown_title,
                    "countdown_desc": e.countdown_desc,
                    "allow_custom_answer": e.allow_custom_answer,
                    "allow_edit": e.allow_edit,
                    "allow_to_add_answer_category": e.allow_to_add_answer_category,
                    "added_date": e.added_date,
                    "last_updated": e.last_updated,
                    "step_status": e.step_status,
                    "sort_order": e.sort_order,
                    "title_same_as_skill_ex_name": e.title_same_as_skill_ex_name,
                    "enable_countdown": e.enable_countdown,
                    "cntdown_min_minutes": e.cntdown_min_minutes,
                    "cntdown_max_minutes": e.cntdown_max_minutes,
                    "cntdown_start_title": e.cntdown_start_title,
                    "cntdown_start_desc": e.cntdown_start_desc,
                    "cntdown_countdown_desc": e.cntdown_countdown_desc
                };
                //console.warn("Now Processing - EXPOSURE STEPS");
                //sqlhelper.insertJSONData("tbl_v2_sk_exposure_steps",data);
                //totalLengths++;
                // sqlhelper.insertJSONDataFirstSync(tx_reg,'tbl_v2_sk_exposure_steps', data, function (results) {

                //              if (!results) {
                //                  isValid = false;
                //                  sum++;
                //              }
                //              iVal++;//onSuccess function

                //              checkSyncComplete(iVal);//call this lastly method or each
                //          }, function () {
                //              iVal++;//onError function
                ////              console.warn("Failure Insert tbl_v2_sk_exposure_steps");
                //          });
                var fields = sqlhelper.separateFieldData(data, "field");
                var dataval = sqlhelper.separateFieldData(data, "value");
                var sqlquery = 'INSERT INTO tbl_v2_sk_exposure_steps (' + fields + ') values(' + dataval + ')';
                console.log(sqlquery);
                otherqueries.push(sqlquery);

            });
        }


        //sync exposure master answer categories
        if (typeof RegistrationData.other_modules.exposure.answer_cats != "undefined") {
            $(RegistrationData.other_modules.exposure.answer_cats).each(function(i, e) {



                var todeelete = {
                    where: {
                        'answer_cat_id': e.answer_cat_id,
                    }
                };
                var where = sqlhelper.generateUpdateFields(todeelete.where);

                var sqlquery = "DELETE FROM tbl_v2_skill_exposure_answer_category where " + where;
                console.log(sqlquery);
                otherqueries.push(sqlquery);
                var data = {
                    'answer_cat_id': e.answer_cat_id,
                    'answer_cat_name': e.answer_cat_name,
                    'step_id': e.step_id,
                    'added_date': e.added_date,
                    'last_updated': e.last_updated,
                    'answer_cat_status': e.answer_cat_status,
                    'sort_order': e.sort_order,
                    'answer_type': e.answer_type,
                    'created_by': e.created_by,
                    'belongs_to': e.belongs_to,
                    'added_by': e.added_by
                };

                //console.warn("Now Processing - EXPOSURE ANSWER CATS");
                //sqlhelper.insertJSONData("tbl_v2_skill_exposure_answer_category",data);
                //totalLengths++;
                // sqlhelper.insertJSONDataFirstSync(tx_reg,'tbl_v2_skill_exposure_answer_category', data, function (results) {

                //               if (!results) {
                //                   isValid = false;
                //                   sum++;
                //               }
                //               iVal++;//onSuccess function

                //               checkSyncComplete(iVal);//call this lastly method or each
                //           }, function () {
                //               iVal++;//onError function
                ////               console.warn("Failure Insert tbl_v2_skill_exposure_answer_category");
                //           });
                var fields = sqlhelper.separateFieldData(data, "field");
                var dataval = sqlhelper.separateFieldData(data, "value");
                var sqlquery = 'INSERT INTO tbl_v2_skill_exposure_answer_category (' + fields + ') values(' + dataval + ')';
                console.log(sqlquery);
                otherqueries.push(sqlquery);

            });
        }


        //sync exposure master answers
        if (typeof RegistrationData.other_modules.exposure.answers != "undefined") {
            $(RegistrationData.other_modules.exposure.answers).each(function(i, e) {



                var todeelete = {
                    where: {
                        'answer_id': e.answer_id,
                    }
                };
                var where = sqlhelper.generateUpdateFields(todeelete.where);

                var sqlquery = "DELETE FROM tbl_v2_skill_exposure_answers where " + where;
                console.log(sqlquery);
                otherqueries.push(sqlquery);
                var data = {
                    'answer_id': e.answer_id,
                    'answer': e.answer,
                    'step_id': e.step_id,
                    'answer_cat_id': e.answer_cat_id,
                    'added_date': e.added_date,
                    'last_updated': e.last_updated,
                    'answer_status': e.answer_status,
                    'sort_order': e.sort_order,
                    'answer_type': e.answer_type,
                    'created_by': e.created_by,
                    'belongs_to': e.belongs_to,
                    'added_by': e.added_by
                };

                //console.warn("Now Processing - EXPOSURE ANSWERS");
                //sqlhelper.insertJSONData("tbl_v2_skill_exposure_answers",data);
                //totalLengths++;
                // sqlhelper.insertJSONDataFirstSync(tx_reg,'tbl_v2_skill_exposure_answers', data, function (results) {

                //              if (!results) {
                //                  isValid = false;
                //                  sum++;
                //              }
                //              iVal++;//onSuccess function

                //              checkSyncComplete(iVal);//call this lastly method or each
                //          }, function () {
                //              iVal++;//onError function
                ////              console.warn("Failure Insert tbl_v2_skill_exposure_answers");
                //          });
                var fields = sqlhelper.separateFieldData(data, "field");
                var dataval = sqlhelper.separateFieldData(data, "value");
                var sqlquery = 'INSERT INTO tbl_v2_skill_exposure_answers (' + fields + ') values(' + dataval + ')';
                console.log(sqlquery);
                otherqueries.push(sqlquery);

            });
        }


        //REGISTRATION TASKS
        if (typeof RegistrationData.registration_task.homework_module.homeworks != "undefined") {
            $(RegistrationData.registration_task.homework_module.homeworks).each(function(i, e) {



                var todeelete = {
                    where: {
                        'homework_id': e.homework_id,
                    }
                };
                var where = sqlhelper.generateUpdateFields(todeelete.where);

                var sqlquery = "DELETE FROM tbl_homeworks where " + where;
                console.log(sqlquery);
                otherqueries.push(sqlquery);
                var data = {
                    'added_by': e.added_by,
                    'contents': e.contents,
                    'created_at': e.created_at,
                    'created_by': e.created_by,
                    'difficulty_id': e.difficulty_id,
                    'headline': e.headline.replace("'", "\'"),
                    'homework_id': e.homework_id,
                    'homework_status': e.homework_status,
                    'updated_at': e.updated_at,
                    'hw_type': e.hw_type,
                    'sort_order': e.sort_order
                };
                //console.warn("Now Processing - HOMEWORKS");
                //sqlhelper.insertJSONData('tbl_homeworks',data);
                //totalLengths++;
                // sqlhelper.insertJSONDataFirstSync(tx_reg,'tbl_homeworks', data, function (results) {

                //                   if (!results) {
                //                       isValid = false;
                //                       sum++;
                //                   }
                //                   iVal++;//onSuccess function

                //                   checkSyncComplete(iVal);//call this lastly method or each
                //               }, function () {
                //                   iVal++;//onError function
                ////                   console.warn("Failure Insert tbl_homeworks");
                //               });
                var fields = sqlhelper.separateFieldData(data, "field");
                var dataval = sqlhelper.separateFieldData(data, "value");
                var sqlquery = 'INSERT INTO tbl_homeworks (' + fields + ') values(' + dataval + ')';
                console.log(sqlquery);
                otherqueries.push(sqlquery);

            })
        }

        if (typeof RegistrationData.registration_task.crisis_plan != "undefined") {
            $(RegistrationData.registration_task.crisis_plan).each(function(i, e) {



                var todeelete = {
                    where: {
                        'plan_id': e.plan_id,
                    }
                };
                var where = sqlhelper.generateUpdateFields(todeelete.where);

                var sqlquery = "DELETE FROM tbl_crisisplans where " + where;
                console.log(sqlquery);
                otherqueries.push(sqlquery);
                var data = {
                    'plan_id': e.plan_id,
                    'difficulty_id': e.difficulty_id,
                    'headline': e.headline.replace("'", "\'"),
                    'created_by': e.created_by,
                    'contents': e.contents,
                    'created_at': e.created_at,
                    'updated_at': e.updated_at,
                    'plan_type': e.plan_type,
                    'created_by': e.created_by,
                    'added_by': e.added_by,
                    'plan_status': e.plan_status,
                    'already_read': e.already_read,
                    'belongs_to': e.belongs_to
                };
                //console.warn("Now Processing - CRISIS PLANS");
                //sqlhelper.insertJSONData('tbl_crisisplans',data);
                //totalLengths++;
                // sqlhelper.insertJSONDataFirstSync(tx_reg,'tbl_crisisplans', data, function (results) {

                //                   if (!results) {
                //                       isValid = false;
                //                       sum++;
                //                   }
                //                   iVal++;//onSuccess function

                //                   checkSyncComplete(iVal);//call this lastly method or each
                //               }, function () {
                //                   iVal++;//onError function
                ////                   console.warn("Failure Insert tbl_crisisplans");
                //               });
                var fields = sqlhelper.separateFieldData(data, "field");
                var dataval = sqlhelper.separateFieldData(data, "value");
                var sqlquery = 'INSERT INTO tbl_crisisplans (' + fields + ') values(' + dataval + ')';
                console.log(sqlquery);
                otherqueries.push(sqlquery);

            })
        }

        if (typeof RegistrationData.registration_task.homework_module.homework_assignments != "undefined") {
            $(RegistrationData.registration_task.homework_module.homework_assignments).each(function(i, e) {



                var todeelete = {
                    where: {
                        'assignment_id': e.assignment_id,
                    }
                };
                var where = sqlhelper.generateUpdateFields(todeelete.where);

                var sqlquery = "DELETE FROM tbl_homework_assignments where " + where;
                console.log(sqlquery);
                otherqueries.push(sqlquery);
                var data = {
                    'assignment_id': e.assignment_id,
                    'homework_id': e.homework_id,
                    'patient_id': e.patient_id,
                    'published_by': e.published_by,
                    'published_date': e.published_date,
                    'is_published': e.is_published,
                    'already_viewed': e.already_viewed
                };
                //console.warn("Now Processing - HOMEWORK ASSIGNMENTS");
                //sqlhelper.insertJSONData('tbl_homework_assignments',data);
                //totalLengths++;
                // sqlhelper.insertJSONDataFirstSync(tx_reg,'tbl_homework_assignments', data, function (results) {

                //               if (!results) {
                //                   isValid = false;
                //                   sum++;
                //               }
                //               iVal++;//onSuccess function

                //               checkSyncComplete(iVal);//call this lastly method or each
                //           }, function () {
                //               iVal++;//onError function
                ////               console.warn("Failure Insert tbl_homework_assignments");
                //           });

                var fields = sqlhelper.separateFieldData(data, "field");
                var dataval = sqlhelper.separateFieldData(data, "value");
                var sqlquery = 'INSERT INTO tbl_homework_assignments (' + fields + ') values(' + dataval + ')';
                //console.log(sqlquery);
                console.log(".");

                otherqueries.push(sqlquery);

            })
        }


        if (typeof RegistrationData.registration_task.registration_module.registrations != "undefined") {
            $(RegistrationData.registration_task.registration_module.registrations).each(function(i, e) {



                var todeelete = {
                    where: {
                        'registration_id': e.registration_id,
                    }
                };
                var where = sqlhelper.generateUpdateFields(todeelete.where);

                var sqlquery = "DELETE FROM tbl_registrations where " + where;
                console.log(sqlquery);
                otherqueries.push(sqlquery);
                var data = {
                    'registration_id': e.registration_id,
                    'registration_name': e.registration_name.replace("'", "\'"),
                    'difficulty_id': e.difficulty_id,
                    'flow_type': e.flow_type,
                    'added_date': e.added_date,
                    'last_updated': e.last_updated,
                    'registration_status': e.registration_status,
                    'sort_order': e.sort_order,
                    'bar_color': e.bar_color
                };
                //console.warn("Now Processing - REGISTRATIONS");
                //sqlhelper.insertJSONData('tbl_registrations',data);
                //totalLengths++;
                // sqlhelper.insertJSONDataFirstSync(tx_reg,'tbl_registrations', data, function (results) {

                //                   if (!results) {
                //                       isValid = false;
                //                       sum++;
                //                   }
                //                   iVal++;//onSuccess function

                //                   checkSyncComplete(iVal);//call this lastly method or each
                //               }, function () {
                //                   iVal++;//onError function
                ////                   console.warn("Failure Insert tbl_registrations");
                //               });
                var fields = sqlhelper.separateFieldData(data, "field");
                var dataval = sqlhelper.separateFieldData(data, "value");
                var sqlquery = 'INSERT INTO tbl_registrations (' + fields + ') values(' + dataval + ')';
                //console.log(sqlquery);
                console.log(".");
                otherqueries.push(sqlquery);

            });
        }


        if (typeof RegistrationData.registration_task.registration_module.patients != undefined) {
            if (typeof RegistrationData.registration_task.registration_module.patients.assignments != undefined) {
                $(RegistrationData.registration_task.registration_module.patients.assignments).each(function(i, e) {





                    var todeelete = {
                        where: {
                            'assignment_id': e.assignment_id,
                        }
                    };
                    var where = sqlhelper.generateUpdateFields(todeelete.where);

                    var sqlquery = "DELETE FROM tbl_patient_assignments where " + where;
                    console.log(sqlquery);
                    otherqueries.push(sqlquery);
                    var data = {
                        'assignment_id': e.assignment_id,
                        'assignment_code': e.assignment_code,
                        'registration_id': e.registration_id,
                        'flow_id': e.flow_id,
                        'patient_id': e.patient_id,
                        'incident_date': e.incident_date,
                        'incident_time': e.incident_time,
                        'answered_date': e.answered_date,
                        'date_only': e.date_only,
                        'stage_id': e.stage_id
                    };
                    //console.warn("Now Processing - REGISTRATIONS ASSIGNMENTS");
                    //sqlhelper.insertJSONData('tbl_patient_assignments',data);
                    //totalLengths++;
                    // sqlhelper.insertJSONDataFirstSync(tx_reg,'tbl_patient_assignments', data, function (results) {

                    //           if (!results) {
                    //               isValid = false;
                    //               sum++;
                    //           }
                    //           iVal++;//onSuccess function

                    //           checkSyncComplete(iVal);//call this lastly method or each
                    //       }, function () {
                    //           iVal++;//onError function
                    ////           console.warn("Failure Insert tbl_patient_assignments");
                    //       });
                    var fields = sqlhelper.separateFieldData(data, "field");
                    var dataval = sqlhelper.separateFieldData(data, "value");
                    var sqlquery = 'INSERT INTO tbl_patient_assignments (' + fields + ') values(' + dataval + ')';
                    //console.log(sqlquery);
                    console.log(".");
                    otherqueries.push(sqlquery);

                })
            }
            if (typeof RegistrationData.registration_task.registration_module.patients.assignment_details != undefined) {
                $(RegistrationData.registration_task.registration_module.patients.assignment_details).each(function(i, e) {



                    var todeelete = {
                        where: {
                            'assignment_details_id': e.assignment_details_id,
                        }
                    };
                    var where = sqlhelper.generateUpdateFields(todeelete.where);

                    var sqlquery = "DELETE FROM tbl_patient_assignment_details where " + where;
                    console.log(sqlquery);
                    otherqueries.push(sqlquery);


                    var data = {
                        'assignment_details_id': e.assignment_details_id,
                        'app_assignment_id': 0,
                        'assignment_id': e.assignment_id,
                        'registration_id': e.registration_id,
                        'flow_id': e.flow_id,
                        'step_id': e.step_id,
                        'answer_id': e.answer_id,
                        'app_answer_id': e.app_answer_id,
                        'assignment_code': e.assignment_code
                    };
                    //console.warn("Now Processing - REGISTRATION ASSIGNMENT DETAILS");
                    //sqlhelper.insertJSONData('tbl_patient_assignment_details',data);
                    //totalLengths++;
                    // sqlhelper.insertJSONDataFirstSync(tx_reg,'tbl_patient_assignment_details', data, function (results) {

                    //         if (!results) {
                    //             isValid = false;
                    //             sum++;
                    //         }
                    //         iVal++;//onSuccess function

                    //         checkSyncComplete(iVal);//call this lastly method or each
                    //     }, function () {
                    //         iVal++;//onError function
                    ////         console.warn("Failure Insert tbl_patient_assignment_details");
                    //     });
                    var fields = sqlhelper.separateFieldData(data, "field");
                    var dataval = sqlhelper.separateFieldData(data, "value");
                    var sqlquery = 'INSERT INTO tbl_patient_assignment_details (' + fields + ') values(' + dataval + ')';
                    //console.log(sqlquery);
                    console.log(".");
                    otherqueries.push(sqlquery);

                })
            }
        }


        if (RegistrationData.registration_task.registration_module.steps != null && RegistrationData.registration_task.registration_module.steps.length > 0) {
            $(RegistrationData.registration_task.registration_module.steps).each(function(i, e) {



                var todeelete = {
                    where: {
                        'step_id': e.step_id,
                    }
                };
                var where = sqlhelper.generateUpdateFields(todeelete.where);

                var sqlquery = "DELETE FROM tbl_registration_steps where " + where;
                console.log(sqlquery);
                otherqueries.push(sqlquery);

                var data = {
                    'step_id': e.step_id,
                    'step_name': e.step_name,
                    'registration_id': e.registration_id,
                    'flow_id': e.flow_id,
                    'is_multiple_choice': e.is_multiple_choice,
                    'max_selection_allowed': e.max_selection_allowed,
                    'show_date': e.show_date,
                    'show_time': e.show_time,
                    'time_format': e.time_format,
                    'answer_text': e.answer_text,
                    'button_text': e.button_text,
                    'allow_custom_answer': e.allow_custom_answer,
                    'allow_edit': e.allow_edit,
                    'allow_to_add_answer_category': e.allow_to_add_answer_category,
                    'added_date': e.added_date,
                    'last_updated': e.last_updated,
                    'step_status': e.step_status,
                    'sort_order': e.sort_order,
                    'special_case': e.special_case,
                    'template': e.template
                };
                //console.warn("Now Processing - REGISTRATION STEPS");
                //sqlhelper.insertJSONData('tbl_registration_steps',data);
                //totalLengths++;
                // sqlhelper.insertJSONDataFirstSync(tx_reg,'tbl_registration_steps', data, function (results) {

                //            if (!results) {
                //                isValid = false;
                //                sum++;
                //            }
                //            iVal++;//onSuccess function

                //            checkSyncComplete(iVal);//call this lastly method or each
                //        }, function () {
                //            iVal++;//onError function
                ////            console.warn("Failure Insert tbl_registration_steps");
                //        });
                var fields = sqlhelper.separateFieldData(data, "field");
                var dataval = sqlhelper.separateFieldData(data, "value");
                var sqlquery = 'INSERT INTO tbl_registration_steps (' + fields + ') values(' + dataval + ')';
                //console.log(sqlquery);
                //

                console.log(".");

                otherqueries.push(sqlquery);

            })
        }


        if (RegistrationData.registration_task.registration_module.answer_category != null && typeof RegistrationData.registration_task.registration_module.answer_category != "undefined" && RegistrationData.registration_task.registration_module.answer_category.length > 0) {
            $(RegistrationData.registration_task.registration_module.answer_category).each(function(i, e) {





                var todeelete = {
                    where: {
                        'answer_cat_id': e.answer_cat_id,
                    }
                };
                var where = sqlhelper.generateUpdateFields(todeelete.where);

                var sqlquery = "DELETE FROM tbl_answer_category where " + where;
                console.log(sqlquery);
                otherqueries.push(sqlquery);
                var data = {
                    'answer_cat_id': e.answer_cat_id,
                    'answer_cat_name': e.answer_cat_name.replace("'", "\'"),
                    'step_id': e.step_id,
                    'added_date': e.added_date,
                    'last_updated': e.last_updated,
                    'answer_cat_status': e.answer_cat_status,
                    'sort_order': e.sort_order,
                    'answer_type': e.answer_type,
                    'created_by': e.created_by,
                    'belongs_to': e.belongs_to,
                    'added_by': e.added_by,
                    'mapp_cat_id': e.mapp_cat_id
                };
                //console.warn("Now Processing - REGISTRATION ANSWER CATS");
                //sqlhelper.insertJSONData('tbl_answer_category',data);
                //totalLengths++;
                // sqlhelper.insertJSONDataFirstSync(tx_reg,'tbl_answer_category', data, function (results) {

                //                 if (!results) {
                //                     isValid = false;
                //                     sum++;
                //                 }
                //                 iVal++;//onSuccess function

                //                 checkSyncComplete(iVal);//call this lastly method or each
                //             }, function () {
                //                 iVal++;//onError function
                ////                 console.warn("Failure Insert tbl_answer_category");
                //             });
                var fields = sqlhelper.separateFieldData(data, "field");
                var dataval = sqlhelper.separateFieldData(data, "value");
                var sqlquery = 'INSERT INTO tbl_answer_category (' + fields + ') values(' + dataval + ')';
                //console.log(sqlquery);
                console.log(".");
                otherqueries.push(sqlquery);

            })
        }


        //console.log(typeof RegistrationData.registration_task.registration_module.answers);
        if (RegistrationData.registration_task.registration_module.answers != null && typeof RegistrationData.registration_task.registration_module.answers != "undefined" && RegistrationData.registration_task.registration_module.answers.length > 0) {
            var totalregans = $(RegistrationData.registration_task.registration_module.answers).length;
            var regans = 1;
            $(RegistrationData.registration_task.registration_module.answers).each(function(i, e) {



                var todeelete = {
                    where: {
                        'answer_id': e.answer_id,
                    }
                };
                var where = sqlhelper.generateUpdateFields(todeelete.where);

                var sqlquery = "DELETE FROM tbl_answers where " + where;
                console.log(sqlquery);
                otherqueries.push(sqlquery);

                var data = {
                    'answer_id': e.answer_id,
                    'answer': e.answer.replace("'", "\'"),
                    'step_id': e.step_id,
                    'answer_cat_id': e.answer_cat_id,
                    'added_date': e.added_date,
                    'last_updated': e.last_updated,
                    'answer_status': e.answer_status,
                    'sort_order': e.sort_order,
                    'answer_type': e.answer_type,
                    'created_by': e.created_by,
                    'belongs_to': e.belongs_to,
                    'mapped_answer_id': e.mapped_answer_id,
                    'special_answer': e.special_answer,
                    'difficulty_id': e.difficulty_id
                };
                //console.warn("Now Processing - REGISTRATION ANSWERS");
                // sqlhelper.insertJSONData('tbl_answers',data);

                //totalLengths++;
                // sqlhelper.insertJSONDataFirstSync(tx_reg,'tbl_answers', data, function (results) {

                //              if (!results) {
                //                  isValid = false;
                //                  sum++;
                //              }
                //              iVal++;//onSuccess function

                //              checkSyncComplete(iVal);//call this lastly method or each
                //          }, function () {
                //              iVal++;//onError function
                ////              console.warn("Failure Insert tbl_answers");
                //          });
                var fields = sqlhelper.separateFieldData(data, "field");
                var dataval = sqlhelper.separateFieldData(data, "value");
                var sqlquery = 'INSERT INTO tbl_answers (' + fields + ') values(' + dataval + ')';
                //console.log(sqlquery);
                console.log(".");
                otherqueries.push(sqlquery);



            })
        }
        //console.log(otherqueries);
        console.log("Array prepared successfully now inserting into database");
        sqlhelper.db.transaction(function(tx) {
            for (i = 0; i < otherqueries.length; i++) {
                //console.log(otherqueries[i]);
                try {
                    if (otherqueries[i].indexOf("DELETE") == -1) {
                        console.log("sql >> " + otherqueries[i]);
                        tx.executeSql(otherqueries[i], [], function() {
                            checkSyncComplete(++iVal);
                        })
                    } else {
                        tx.executeSql(otherqueries[i]);
                    }


                } catch (ex) {
                    iVal++;
                    console.log("Error at" + i);
                }


            }
        });

        function checkSyncComplete(i) {
            //console.log(i);
            console.log("I = " + i + ", Total Length = " + totalLengths);

            if ($(".download-content-msg").length > 0) {
                pcdone = Math.round((100 / totalLengths) * i);
                $(".download-content-msg").find(".download-msg-preparing").hide();
                $(".download-content-msg").find(".download-progress-holder").show();
                $(".download-content-msg").find(".pc-done").html(pcdone + "% done ");
                $(".download-content-msg").find(".download-progress").css("width", pcdone + "%");
                if ($(".ui-loader").is(":visible")) $(".ui-loader").hide();
            }
            // console.log(pcdone);
            // if(pcdone>98) //sometimes download stucks at 98% , so let them login, dirty fix, needs imrovement
            // {
            //   var b = setTimeout(function(){
            //       if(pcdone!=100){
            //           if(typeof callback!="undefined"){
            //             console.log("Login now");
            //             callback();
            //           }
            //           if(showDialog==true){
            //              $(".download-content-msg").hide();
            //               $(".download-overlay").hide();
            //           }
            //           offlinehelper.resetSyncProgressBar();//just reset the sync dialog's progressbar width and percentage
            //           offlinehelper.syncstarted=false;
            //           offlinehelper.loginstarted=true;
            //           filehelper.downloadModuleFiles(offlinehelper.modulefiles);
            //           $(".ui-loader").hide();
            //       }
            //       clearTimeout(b);
            //   },4000);
            // }

            if (i >= totalLengths) {
                //login code here
                if (typeof callback != "undefined") {

                    console.log("Login now");
                    callback();
                }

                if (showDialog == true) {
                    $(".download-content-msg").hide();
                    $(".download-overlay").hide();
                }

                offlinehelper.resetSyncProgressBar(); //just reset the sync dialog's progressbar width and percentage
                offlinehelper.syncstarted = false;
                offlinehelper.loginstarted = true;
               
                //if(offlinehelper.loggingOut==false){
                //  filehelper.downloadModuleFiles();
                //}
            }
        }
    },
    saveTraining: function(json, success) {
        sqlhelper.insertJSONData('tbl_training', json);
        if (navigator.onLine && offlinehelper.syncstarted == false)
            offlinehelper.prepareForSync();
        success();
    },
    saveReviewedTraining: function(json, success) {
        json = json;
        sqlhelper.updateData('tbl_training', json);
        if (navigator.onLine && offlinehelper.syncstarted == false)
            offlinehelper.prepareForSync();
        success();
    },
    saveReminder: function(json, success) {
        json = json;
        sqlhelper.updateData('tbl_user', json);
        success();
    },
    markHomeworkRead: function(json, success) {
        json = $.parseJSON(json);
        var toupdate = {
            where: {
                'assignment_id': json.assignmentId,
            },
            fields: {
                'already_viewed': 1,
                'updated': 1
            }
        };
        sqlhelper.updateData('tbl_homework_assignments', toupdate);
        var response = {
            status: 'ok'
        };
        success(response);
    },
    markCrisisplanRead: function(json, success) {
        json = $.parseJSON(json);
        var toupdate = {
            where: {
                'plan_id': json.planId,
            },
            fields: {
                'already_read': 1,
                'updated': 1
            }
        };
        sqlhelper.updateData('tbl_crisisplans', toupdate);
        var response = {
            status: 'ok'
        };
        success(response);
    },
    getOldTrainings: function(json, success) {
        json = $.parseJSON(json);
        taskid = json.taskid;
        var toreturn = {
            'status': 'ok'
        };
        $(Training.TaskLists).each(function(i, e) {
            if (e.taskid == taskid) {

                toreturn.data = e.training;

            }

        })
        console.log(toreturn);
        success(toreturn)
    },

    GetActivityperweek: function(json, success) {
        json = $.parseJSON(json);
        taskid = json.taskid;

        var toreturn = {
            'status': 'ok',
            "data": {
                "task_id": "",
                "startdate": "",
                "todaydate": "",
                "TodayDays": 1,
                "NoOfWeek": 1,
                "TotalNoPractice": "",
                "weeksData": []
            }
        }; // Demo json we update data below

        var task = {}; // Get a task detail from tasklist
        $(Training.TaskLists).each(function(i, e) {




            if (parseInt(e.taskid) == taskid) {

                task = e;

            }
        })

        if (task.training == undefined) {
            console.log("No training found");
            toreturn.status = "Not ok";
            success(toreturn);
            return;
        }
        console.log(task.training);

        var lstindex = task.training.length; // First training of the task is in out last index

        var startdate = task.training[parseInt(lstindex) - 1].trainingdatetime; // Get the date

        var enddate = task.training[0].trainingdatetime; // Get the last time task was done by user
        toreturn.data.task_id = taskid;
        toreturn.data.startdate = moment(startdate).format("YYYY-MM-DD"); // initiating start date on return array
        startdate = moment(startdate).format("YYYY-MM-DD");
        enddate = moment().format("YYYY-MM-DD");
        toreturn.data.todaydate = moment().format("YYYY-MM-DD"); // initiating today date on return array
        toreturn.data.TodayDays = Math.ceil(moment(enddate).diff(moment(startdate), 'days', true)) + 1; // initiating Total day from start to end
        var weekdays = [];
        calculatingdate = startdate;


        // Now finding out weeks spent on training with training in each week
        while (Date.parse(calculatingdate) <= Date.parse(enddate)) {

            n = calculatingdate;
            m = moment(calculatingdate).add(7, "days").format("YYYY-MM-DD"); // Add 7 day to start date for looping
            calculatingdate = m;
            count = 0;
            $(task.training).each(function(i, e) {
                if (moment(e.trainingdatetime).diff(n) >= 0 && moment(e.trainingdatetime).diff(calculatingdate) < 0) {
                    console.log("task " + e.trainingdatetime);
                    count++;

                }

            })

            weekdays.push(count);

            console.log(calculatingdate);
            // m = moment(calculatingdate).add(7,"days").format("YYYY-MM-DD"); // Add 7 day to start date for looping
            //calculatingdate=m;
        }


        toreturn.data.NoOfWeek = weekdays.length; // initiating total no. of week
        toreturn.data.TotalNoPractice = lstindex; // initiating total no. of practice done
        toreturn.data.weeksData = weekdays; // initiating total no. of weeks
        console.log("Return from activity per week");
        console.log(toreturn);
        success(toreturn);
    },
    GetActivityperday: function(json, success) {
        // Need to find logic to find starting and ending date.
        var toreturn = {
            "status": "ok",
            "data": {
                "startdate": "2014-03-12",
                "todaydate": "2015-07-03",
                "TodayDays": 479,
                "NoOfWeek": 69,
                "TotalNoPractice": "0",
                "weeksData": [4, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 9, 2, 0, 0, 0, 0, 0, 0, 0, 0, 68, 91, 46, 1, 53, 0, 3, 2, 2, 0, 0, 1, 0, 20, 6, 2]
            }
        }; // Demo json we update data below

        var task = {}; // Get a task detail from tasklist
        var traininglist=[];
        console.log(task);
        var trainingstartdate="";
         $(Training.TaskLists).each(function(i, e) {
            if(e.training!=null && e.training!=undefined && e.training.length>0){
                traininglist.push(e.training);     

                if(trainingstartdate==""){
                    trainingstartdate=e.training[0].trainingDateTime;
                }else{
                    newdate=e.training[0].trainingDateTime;
                    if(Math.ceil(moment(newdate).diff(moment(trainingstartdate), 'days', true))>0){
                        trainingstartdate=newdate;
                    }
                }
            }
        })

        if (traininglist.length<=0) {
            console.log("No training found");
            toreturn.status = "Not ok";
            success(toreturn);
            return;
        }
        //  var lstindex=task.training.length; // First training of the task is in out last index

        var startdate = moment(trainingstartdate).format("YYYY-MM-DD"); // Get the date

        var enddate = moment().format("YYYY-MM-DD"); // Get the last time task was done by user
        //toreturn.data.task_id=taskid;
        toreturn.data.startdate = moment(startdate).format("YYYY-MM-DD"); // initiating start date on return array
        startdate = moment(startdate).format("YYYY-MM-DD");
        enddate = moment(enddate).format("YYYY-MM-DD");
        toreturn.data.todaydate = moment().format("YYYY-MM-DD"); // initiating today date on return array
        toreturn.data.TodayDays = Math.ceil(moment(enddate).diff(moment(startdate), 'days', true)) + 1; // initiating Total day from start to end
        var weekdays = [];
        calculatingdate = startdate;

        var totalpractice = 0;
        var count = 0;
        var prevdate = "";
        // Now finding out weeks spent on training with training in each week
        while (Date.parse(calculatingdate) <= Date.parse(enddate)) {

            prevdate = calculatingdate;
            m = moment(calculatingdate).add(7, "days").format("YYYY-MM-DD"); // Add 7 day to start date for looping
            calculatingdate = m;
            // count=0;
            $(Training.TaskLists).each(function(i, e) {
                task = e;
                $(task.training).each(function(i, e) {
                    var date = moment(e.trainingdatetime).format("YYYY-MM-DD");
                    if (moment(date).diff(calculatingdate) < 0 && moment(date).diff(prevdate) >= 0) {
                        console.log(date);
                        count++;
                        totalpractice++;
                    }

                })
            })

            weekdays.push(count);

            console.log(calculatingdate);
            // m = moment(calculatingdate).add(1,"days").format("YYYY-MM-DD"); // Add 7 day to start date for looping
            // calculatingdate=m;
        }


        toreturn.data.NoOfWeek = weekdays.length; // initiating total no. of week
        toreturn.data.TotalNoPractice = totalpractice; // initiating total no. of practice done
        toreturn.data.weeksData = weekdays; // initiating total no. of weeks
        console.log(toreturn);
        success(toreturn);
    },
    Getestimatesfromstart: function(json, success) {
        json = $.parseJSON(json);
        taskid = json.taskid;
        var toreturn = {
            'status': 'ok',
            "data": {
                "task_id": "",
                "startdate": "",
                "todaydate": "",
                "TodayDays": 1,
                "Estimates": []
            }
        }; // Demo json we update data below

        var task = {}; // Get a task detail from tasklist
        $(Training.TaskLists).each(function(i, e) {
            if (e.taskid == taskid) {

                task = e;

            }

        })
        console.log(task);

        var lstindex = task.training.length; // First training of the task is in out last index

        var startdate = task.training[parseInt(lstindex) - 1].trainingdatetime; // Get the date

        var enddate = task.training[0].trainingdatetime; // Get the last time task was done by user
        toreturn.data.task_id = taskid;
        toreturn.data.startdate = moment(startdate).format("YYYY-MM-DD"); // initiating start date on return array
        startdate = moment(startdate).format("YYYY-MM-DD");
        enddate = moment().format("YYYY-MM-DD");
        toreturn.data.todaydate = moment().format("YYYY-MM-DD"); // initiating today date on return array
        toreturn.data.TodayDays = Math.ceil(moment(enddate).diff(moment(startdate), 'days', true)) + 1; // initiating Total day from start to end
        var Estimates = [];

        $(task.training).each(function(i, e) {
            Estimates.push(e.estimatedvalue);


        })

        toreturn.data.Estimates = Estimates; // initiating total no. of weeks
        console.log(toreturn);
        success(toreturn);
    },

    ValidateUser: function(json, success) {
        json = JSON.parse(json);
        console.log(json);
        //var sql="SELECT *,hasRegistrations as hasRegistration from tbl_user where (username='"+json.username.capitalizeFirstLetter()+"' OR username='"+json.username.smallFirstLetter()+"' OR username='"+json.username.toUpperCase()+"' OR username='"+json.username.toLowerCase()+"') and password='"+json.password+"';";
        
        var sql = "SELECT * from tbl_user where username='" + json.username + "' COLLATE NOCASE and password='" + json.password + "';";
        console.log(sql);
        sqlhelper.db.transaction(function(tx) {
            tx.executeSql(sql, [], function(tx, results) {
                if (results.rows.length > 0) {
                    console.log(results.rows.item(0));
                    var userdata = results.rows.item(0);
                    console.log("Result data is " + results);
                    var len = results.rows.length;
                    console.log("Length is " + len);
                    console.log("User found && userdetail is ");
                    console.log(userdata);
                    //userdata=$.parseJSON(userdata);
                    returndata = {};
                    returndata.data = userdata;
                    returndata.status = "ok";

                    success(returndata);

                    /*var enabledHomeworks = offlinehelper.ShowHideModules("homework_id");
                    if (typeof enabledHomeworks == "object") {
                        if (enabledHomeworks.length > 0) {
                            $hwIDs = enabledHomeworks.join();
                            var sqlTotalHomeworks = "SELECT count(*) as totalhomeworks FROM tbl_homeworks  INNER JOIN tbl_homework_assignments ON (tbl_homework_assignments.homework_id=tbl_homeworks.homework_id) WHERE tbl_homeworks.homework_status=1 AND tbl_homeworks.homework_id IN(" + $hwIDs + ")";
                            var sqlNewHomeworks = "SELECT count(*) as totalhomeworks FROM tbl_homeworks  INNER JOIN tbl_homework_assignments ON (tbl_homework_assignments.homework_id=tbl_homeworks.homework_id) where already_viewed=0 AND tbl_homeworks.homework_status=1 AND tbl_homeworks.homework_id IN(" + $hwIDs + ")";
                        } else {
                            var sqlTotalHomeworks = "SELECT count(*) as totalhomeworks FROM tbl_homeworks  INNER JOIN tbl_homework_assignments ON (tbl_homework_assignments.homework_id=tbl_homeworks.homework_id) WHERE tbl_homeworks.homework_status=1 AND 1=2";
                            var sqlNewHomeworks = "SELECT count(*) as totalhomeworks FROM tbl_homeworks  INNER JOIN tbl_homework_assignments ON (tbl_homework_assignments.homework_id=tbl_homeworks.homework_id) where already_viewed=0 AND tbl_homeworks.homework_status=1 AND 1=2";
                            //1=2 used in above queries just because no homeworks are activated and we don't want to fetch them.
                        }

                    } else {
                        var sqlTotalHomeworks = "SELECT count(*) as totalhomeworks FROM tbl_homeworks  INNER JOIN tbl_homework_assignments ON (tbl_homework_assignments.homework_id=tbl_homeworks.homework_id) WHERE tbl_homeworks.homework_status=1";
                        var sqlNewHomeworks = "SELECT count(*) as totalhomeworks FROM tbl_homeworks  INNER JOIN tbl_homework_assignments ON (tbl_homework_assignments.homework_id=tbl_homeworks.homework_id) where already_viewed=0 AND tbl_homeworks.homework_status=1";
                    }



                    sqlhelper.db.transaction(function(tx) {

                        tx.executeSql(sqlTotalHomeworks, [], function(tx, results) {
                            console.warn("HOMEWORK TOTAL = " + sqlTotalHomeworks);
                            returndata_hw = {
                                total_homeworks: 0,
                                new_homeworks: 0
                            };
                            // console.log(results.rows.item(0));
                            returndata_hw.total_homeworks = results.rows.item(0).totalhomeworks;

                            sqlhelper.db.transaction(function(tx) {
                                tx.executeSql(sqlNewHomeworks, [], function(tx, results) {
                                    console.warn("TOTAL NEW HOMEWORK = " + sqlNewHomeworks);
                                    console.log(results.rows.item(0));
                                    returndata_hw.new_homeworks = results.rows.item(0).totalhomeworks;

                                    returndata.data.homeworks = JSON.stringify(returndata_hw);

                                    sqlhelper.db.transaction(function(tx) {
                                        tx.executeSql("SELECT count(*) as total_crisis_plans FROM tbl_crisisplans WHERE plan_status=1;", [], function(tx, results) {

                                            returndata_hw = {
                                                total_crisis_plans: 0,
                                                new_crisis_plans: 0
                                            };
                                            console.log(results.rows.item(0));
                                            returndata_hw.total_crisis_plans = results.rows.item(0).total_crisis_plans;

                                            sqlhelper.db.transaction(function(tx) {
                                                tx.executeSql("SELECT count(*) as total_crisis_plans FROM tbl_crisisplans where already_read=0 AND plan_status=1;", [], function(tx, results) {

                                                    console.log(results.rows.item(0));
                                                    returndata_hw.new_crisis_plans = results.rows.item(0).total_crisis_plans;

                                                    returndata.data.crisisplans = JSON.stringify(returndata_hw);
                                                    success(returndata);

                                                });
                                            });

                                        });
                                    });
                                });
                            });

                        });
                    });*/


                } else {
                    console.warn("No User found");
                    success(true);
                }
            });
        });
    },
    activeTasks: function(json, success) {

        json = JSON.parse(json);
        var sql = "SELECT rowid, app_task_id,task_id as taskid,0 as TodayDays,0 as practice,0 as training ,task_heading as heading,task_tag as tag,task_hide_graph as hide_graph from tbl_tasks;";

        var task_json = {};
        sqlhelper.db.transaction(function(tx) {
            tx.executeSql(sql, [], function(txs, results) {
                if (results.rows.length > 0) {

                    returndata = {
                        "status": "ok",
                        data: []
                    };

                    for (i = 0; i < results.rows.length; i++) {
                        returndata.data.push(results.rows.item(i));
                    }

                    count = 0;
                    totalloop = returndata.data.length;
                    var returningData = {
                        "status": "ok",
                        data: []
                    };
                    if (totalloop != 0) {

                        $(returndata.data).each(function(i, e) {

                            sqlhelper.db.transaction(function(tx) {
                                $sqlTask = "SELECT rowid,app_training_id,*,trainingDateTime as trainingdatetime,training_id as trainingId,estimatedValue as estimatedvalue  FROM tbl_training where task_id='" + e.taskid + "' order by trainingdatetime DESC";

                                tx.executeSql($sqlTask, [], function(txs, results) {

                                    if (results.rows.length > 0) {
                                        trainingdone = results.rows.length;
                                        console.log("training done is : " + trainingdone)
                                        e.practice = trainingdone;
                                        lstindex = results.rows.length

                                        var startdate = results.rows.item(parseInt(lstindex) - 1).trainingdatetime; // Get the date
                                        startdate = moment(startdate).format("YYYY-MM-DD"); // initiating start date on return array

                                        enddate = moment().format("YYYY-MM-DD");

                                        e.TodayDays = Math.ceil(moment(enddate).diff(moment(startdate), 'days', true)) + 1; // initiating 
                                        var traininglist = [];
                                        for (j = 0; j < results.rows.length; j++) {
                                            traininglist.push(results.rows.item(j));
                                        }

                                        e.training = traininglist;
                                        count++;
                                        returningData.data.push(e);
                                        if (totalloop == count) {
                                            // console.log("Returning data and my count is "+count);
                                            success(returningData);
                                        }
                                    } else {
                                        e.training = {};
                                        e.TodayDays = 1;
                                        success(returndata);
                                    }
                                });
                            });





                        })
                    } else {
                        returndata = {
                            "status": "ok",
                            data: []
                        };
                        success(returndata);
                    }

                    //     console.log(results.rows.item(0));
                    // var userdata=results.rows.item(0);
                    // console.log("Result data is "+results);

                    // var len = results.rows.length; 
                    // console.log("Length is "+ len);

                    // console.log("User found && userdetail is ");
                    // console.log(userdata);
                    // //userdata=$.parseJSON(userdata);

                    // returndata.data=userdata;
                    // returndata.status="ok";

                    //  console.log(success);

                } else {

                    console.log("No User found");

                    returndata = {
                        "status": "ok",
                        data: []
                    };
                    success(returndata);

                }
            });
        });

    },
    syncWithOnline: function() {
        var showSyncDialog = (arguments.length > 0 && arguments[0] !== true) ? arguments[0] : true;

        clearInterval(offlinehelper.syncInterval);
        var userdetails = $.jStorage.get('userdetails');
        var json = '{"userid":"' + userdetails.user_id + '","tokenkey":"' + userdetails.tokenkey + '","deviceId":"ABBBSBS","offlinedata":' + JSON.stringify(offlinehelper.dataForServer) + '}';
        callWebServiceLive('sync_to_server', json, function(res) {
            offlinehelper.dataForServer = {
                    'training': [],
                    'Registraion': {
                        'answers': [],
                        'answercat': [],
                        'patientAssignment': [],
                        'patientAssignmentDetail': [],
                        'crisisplan': [],
                        'homework_assignment': []
                    },
                    'other_modules': {
                        'feelingAssignments': [],
                        'thoughtAssignments': [],
                        'exposureAssignments': [],
                        'skillAssignments': []
                    }
                },
                userdetails = $.jStorage.get('userdetails');
            /* console.clear();
             //console.warn("SYNC RETURN = "+JSON.stringify(res));*/
            var json_update = {
                where: {
                    'app_user_id': userdetails.app_user_id,
                },
                fields: {
                    'fullname': res.data.Name,
                    'new_start_page': res.data.new_start_page,
                    'enable_msg_alert': res.data.enable_msg_alert,
                    'training': JSON.stringify(res.data.training),
                    'hasRegistrations': res.data.hasRegistration,
                    'homeworks': JSON.stringify(res.data.homeworks),
                    'crisisplans': JSON.stringify(res.data.crisisplans),
                    'reminders': JSON.stringify(res.data.reminder),
                    'specialAnswers': res.data.specialAnswers,
                    'feedbackMessage': JSON.stringify(res.data.feedback),
                    'availableModules': JSON.stringify(res.data.available_modules),
                    'hide_graph': res.data.hide_graph
                }
            };
            sqlhelper.updateData('tbl_user', json_update, function() {
                console.log("User data updated");
            });
            userdetails.specialAnswers = res.data.specialAnswers;
            userdetails.new_start_page = res.data.new_start_page;
            userdetails.enable_msg_alert = res.data.enable_msg_alert;
            userdetails.hide_graph = res.data.hide_graph;
            userdetails.hasRegistrations = res.data.hasRegistrations;
            userdetails.tokenkey=$.jStorage.get('bip_jwt');
            $.jStorage.set('userdetails', userdetails);
            if (res.data.new_start_page != 3) {

                $('#RegistrationTask div:first a:first').show()

            } else {
                $('#RegistrationTask div:first a:first').hide()
            }



            var queries = [];

            queries[0] = "DELETE FROM tbl_training where training_id='0'";
            queries[1] = "DELETE FROM tbl_answers where answer_id='0'";
            queries[2] = "DELETE FROM tbl_answer_category where answer_cat_id='0'";
            queries[3] = "DELETE FROM tbl_patient_assignment_details where assignment_details_id='0'";
            queries[4] = "DELETE FROM tbl_patient_assignments where assignment_id='0'";
            queries[5] = "DELETE FROM tbl_v2_sk_exposure_patients_assignments_details where assignment_details_id='0'";
            queries[6] = "DELETE FROM tbl_v2_sk_exposure_patients_assignments where assignment_id='0'";
            queries[7] = "DELETE FROM tbl_v2_sk_skills_assignments_details where assignment_details_id='0'";
            queries[8] = "DELETE FROM tbl_v2_sk_skills_assignments where assignment_id='0'";
            queries[9] = "DELETE FROM tbl_v2_feelings_assignments where assignment_id='0'";
            queries[10] = "DELETE FROM tbl_v2_sk_thoughts_assignments where assignment_id='0'";



            sqlhelper.db.transaction(function(tx) {
                for (var k = 0; k < queries.length; k++) {
                    tx.executeSql(queries[k], [], function(txs, results) {
                        //console.log("All new added traings are deleted. Training synced with server.");
                    });
                }

            });

            // sqlhelper.db.transaction(function(tx) {
            //    tx.executeSql("DELETE FROM tbl_training where training_id='0'", [], function(txs,results){
            //        console.log("All new added traings are deleted. Training synced with server.");
            //    });
            //  });

            // sqlhelper.db.transaction(function(tx) {
            //    tx.executeSql("DELETE FROM tbl_answers where answer_id='0'", [], function(txs,results){
            //        console.log("All new added answers are deleted.");
            //    });
            //  });   
            // sqlhelper.db.transaction(function(tx) {
            //    tx.executeSql("DELETE FROM tbl_answer_category where answer_cat_id='0'", [], function(txs,results){
            //        console.log("All new added answers are deleted.");
            //    });
            //  });
            // sqlhelper.db.transaction(function(tx) {
            //    tx.executeSql("DELETE FROM tbl_patient_assignment_details where assignment_details_id='0'", [], function(txs,results){
            //        console.log("All new patient assignments are deleted.");
            //    });
            //  });    
            // sqlhelper.db.transaction(function(tx) {
            //    tx.executeSql("DELETE FROM tbl_patient_assignments where assignment_id='0'", [], function(txs,results){
            //        console.log("All new patient assignments are deleted.");
            //    });
            //  });

            //added by sabin >>
            // sqlhelper.db.transaction(function(tx) {
            //    tx.executeSql("DELETE FROM tbl_v2_sk_exposure_patients_assignments_details where assignment_details_id='0'", [], function(txs,results){
            //        console.log("All new exposure assignment details are deleted.");
            //    });
            // });

            // sqlhelper.db.transaction(function(tx) {
            //    tx.executeSql("DELETE FROM tbl_v2_sk_exposure_patients_assignments where assignment_id='0'", [], function(txs,results){
            //        console.log("All new exposure assignments are deleted.");
            //    });
            //  });

            // sqlhelper.db.transaction(function(tx) {
            //   tx.executeSql("DELETE FROM tbl_v2_sk_skills_assignments_details where assignment_details_id='0'", [], function(txs,results){
            //       console.log("All new skill assignment details are deleted.");
            //   });
            // });    

            // sqlhelper.db.transaction(function(tx) {
            //    tx.executeSql("DELETE FROM tbl_v2_sk_skills_assignments where assignment_id='0'", [], function(txs,results){
            //        console.log("All new skill assignments are deleted.");
            //    });
            //  });

            // sqlhelper.db.transaction(function(tx) {
            //    tx.executeSql("DELETE FROM tbl_v2_feelings_assignments where assignment_id='0'", [], function(txs,results){
            //        console.log("All new feeling assignments are deleted.");
            //    });
            //  });

            // sqlhelper.db.transaction(function(tx) {
            //    tx.executeSql("DELETE FROM tbl_v2_sk_thoughts_assignments where assignment_id='0'", [], function(txs,results){
            //        console.log("All new thoughts assignments are deleted.");
            //    });
            //  });

            //added by sabin <<


            var json_update = {
                where: {
                    '1': 1
                },
                fields: {
                    'edited': '0'
                }
            };


            sqlhelper.updateData('tbl_patient_assignments', json_update, function() {
                console.log("All edited trainings cleared");
            });
            sqlhelper.updateData('tbl_training', json_update, function() {
                console.log("All edited trainings cleared");
            });
            var json_update = {
                where: {
                    '1': 1
                },
                fields: {
                    'updated': '0'
                }
            };


            sqlhelper.updateData('tbl_crisisplans', json_update, function() {
                console.log("All edited trainings cleared");
            });
            sqlhelper.updateData('tbl_homework_assignments', json_update, function() {
                console.log("All edited trainings cleared");
            });

            console.log(res);

            var tasks = res.data.tasks;
            var deletedTasks = res.data.deletedTasks;
            var trainings = res.data.trainings;
            var count = 0;

            if (deletedTasks.length > 0) {
                var delstring = "";
                $(deletedTasks).each(function(i, e) {
                    if (delstring != "")
                        delstring += " OR ";
                    delstring += " task_id='" + e + "'";
                });

                sqlhelper.db.transaction(function(tx) {
                    tx.executeSql("DELETE FROM tbl_tasks where " + delstring, [], function(txs, results) {

                    });
                });

                sqlhelper.db.transaction(function(tx) {
                    tx.executeSql("DELETE FROM tbl_training where " + delstring, [], function(txs, results) {

                    });
                });
            }

            if (tasks.length == 0) {
                offlinehelper.syncstarted = false;
                offlinehelper.synctime = 0;
            }

            $(trainings).each(function(i, e) {
                var data = {
                    'training_id': e.id,
                    'task_id': e.task_id,
                    'trainingDateTime': e.trainingdatetime,
                    'estimatedValue': e.estimatedvalue,
                    'trainingDuration': e.training_duration,
                    'type': e.type,
                    'comment': e.comment,
                    'edited': e.edited,
                    'estimatedValueEnd': e.estimatedvalue_end,
                };
                sqlhelper.insertJSONData('tbl_training', data);
            });


            $(tasks).each(function(i, e) {
                count++;
                sqlhelper.db.transaction(function(tx) {
                    tx.executeSql("Select * FROM tbl_tasks where task_id='" + e.taskid + "'", [], function(txs, results) {
                        var startinterval;
                        if (count == tasks.length) {
                            startinterval = function() {
                                offlinehelper.syncstarted = false;
                            }
                        } else {
                            startinterval = function() {

                            }
                        }
                        if (results.rows.length != 0) {

                            var json_update = {
                                where: {
                                    'task_id': e.taskid,
                                },
                                fields: {
                                    'task_tag': e.tag,
                                    'task_heading': e.heading,
                                    'task_hide_graph': e.hide_graph
                                }
                            };
                            sqlhelper.updateData('tbl_tasks', json_update, startinterval);

                        } else {

                            var json_update = {
                                'task_id': e.taskid,
                                'task_tag': e.tag,
                                'task_heading': e.heading,
                                'task_hide_graph': e.hide_graph
                            };
                            sqlhelper.insertJSONData('tbl_tasks', json_update, startinterval);

                        }

                    });
                });

            })


            offlinehelper.syncRegistration(res.data.registration_stuffs, function() {
                "Registration synced successfully"
            }, showSyncDialog);
        })
    },
    prepareForSync: function() {
        var showSyncDialog = (arguments.length > 0 && arguments[0] !== true) ? false : true;


        if (navigator.onLine == true && offlinehelper.syncstarted == false) {

            offlinehelper.syncstarted = true;
            offlineInterval = setInterval(function() {
                if (offlinehelper.syncstarted == true)
                    offlinehelper.synctime++;
                console.log("Sync state : " + offlinehelper.syncstarted + " Synced time : " + offlinehelper.synctime);

                if (offlinehelper.synctime > 55) {
                    offlinehelper.syncstarted = false;
                    offlinehelper.synctime = 0;
                    console.log("Failed to  sync data. Connection lost");
                    $.mobile.hidePageLoadingMsg();
                    clearInterval(offlineInterval);
                }

                if (offlinehelper.syncstarted == false) {
                    offlinehelper.synctime = 0;
                    console.log("Data synced successfully");
                    if (offlinehelper.currentpage == "TrainingList")
                        Training.setTrainings();
                    $.mobile.hidePageLoadingMsg();
                    clearInterval(offlineInterval);
                }

            }, 500)
            console.log("Device is online we can have a sync now");
            var totalcount = 0;
            sqlhelper.db.transaction(function(tx) {
                tx.executeSql("SELECT rowid,app_training_id,comment,edited,type,task_id as taskid,trainingDateTime as trainingdatetime,training_id as trainingId,estimatedValue as estimatedvalue,trainingDuration as training_duration,estimatedValueEnd as estimatedvalue_end  FROM tbl_training where trainingId=0 or edited=1", [], function(txs, results) {
                    // console.log(offlinehelper.count);
                    trainingdone = results.rows.length;
                    console.log("training done is : " + trainingdone)

                    var traininglist = [];
                    for (i = 0; i < results.rows.length; i++) {
                        traininglist.push(results.rows.item(i));
                    }


                    offlinehelper.dataForServer.training = traininglist;
                    console.log(offlinehelper.dataForServer);
                    console.log("Returned from training");
                    totalcount++;


                });
            });

            sqlhelper.db.transaction(function(tx) {
                tx.executeSql("SELECT * from tbl_answers where answer_id=0 and (app_answer_cat_id='undefined' OR app_answer_cat_id=0 OR answer_cat_id>0)", [], function(txs, results) {
                    // console.log(offlinehelper.count);


                    var answers_uncat = [];
                    for (i = 0; i < results.rows.length; i++)
                        answers_uncat.push(results.rows.item(i));
                    offlinehelper.dataForServer.Registraion.answers = answers_uncat;
                    console.log("Returned from tbl_answers");
                    totalcount++;
                });
            });

            sqlhelper.db.transaction(function(tx) {
                tx.executeSql("SELECT * from tbl_homework_assignments where updated=1", [], function(txs, results) {
                    // console.log(offlinehelper.count);
                    var homework_assignment = [];
                    for (i = 0; i < results.rows.length; i++)
                        homework_assignment.push(results.rows.item(i));
                    offlinehelper.dataForServer.Registraion.homework_assignment = homework_assignment;
                    console.log("Returned from homework");
                    totalcount++;
                });
            });

            sqlhelper.db.transaction(function(tx) {
                tx.executeSql("SELECT * from tbl_crisisplans where updated=1", [], function(txs, results) {
                    // console.log(offlinehelper.count);
                    var crisisplan = [];
                    for (i = 0; i < results.rows.length; i++)
                        crisisplan.push(results.rows.item(i));
                    offlinehelper.dataForServer.Registraion.crisisplan = crisisplan;
                    console.log("Returned from crisisplan");
                    totalcount++;
                });
            });

            sqlhelper.db.transaction(function(tx) {
                tx.executeSql("SELECT *,0 as answer from tbl_answer_category where answer_cat_id=0", [], function(txs, results) {
                    // console.log(offlinehelper.count);


                    var anscat = [];
                    for (i = 0; i < results.rows.length; i++)
                        anscat.push(results.rows.item(i));
                    var anscount = 1;
                    if (anscat.length > 0) {
                        $(anscat).each(function(i, e) {
                            sqlhelper.db.transaction(function(tx) {
                                tx.executeSql("SELECT * from tbl_answers where answer_id=0 and app_answer_cat_id=" + e.app_answer_cat_id, [], function(txs, results) {
                                    // console.log(offlinehelper.count);


                                    var answers = [];
                                    for (i = 0; i < results.rows.length; i++)
                                        answers.push(results.rows.item(i));
                                    e.answer = answers;
                                    if (anscount == anscat.length) {
                                        offlinehelper.dataForServer.Registraion.answercat = anscat;
                                        console.log("Returned from tbl_answer_category");
                                        totalcount++;
                                    }
                                    anscount++;

                                });
                            });
                        })
                    } else {

                        console.log("Returned from tbl_answer_category");
                        totalcount++;
                    }


                });
            });

            sqlhelper.db.transaction(function(tx) {
                tx.executeSql("SELECT *,0 as assignment_detail from tbl_patient_assignments where assignment_id=0 or edited=1", [], function(txs, results) {
                    var patientAssignment = [];
                    for (i = 0; i < results.rows.length; i++)
                        patientAssignment.push(results.rows.item(i));
                    var anscount = 1;
                    if (patientAssignment.length > 0) {
                        $(patientAssignment).each(function(i, e) {
                            sqlhelper.db.transaction(function(tx) {
                                tx.executeSql("SELECT * from tbl_patient_assignment_details where assignment_details_id=0 and app_assignment_id=" + e.app_assignment_id, [], function(txs, results) {
                                    // console.log(offlinehelper.count);


                                    var patientAssignmentDetail = [];
                                    for (i = 0; i < results.rows.length; i++)
                                        patientAssignmentDetail.push(results.rows.item(i));
                                    e.assignment_detail = patientAssignmentDetail;
                                    if (anscount == patientAssignment.length) {
                                        offlinehelper.dataForServer.Registraion.patientAssignment = patientAssignment;
                                        console.log("Returned from tbl_patient_assignments");
                                        totalcount++;
                                    }
                                    anscount++;

                                });
                            });
                        });
                    } else {

                        console.log("Returned from tbl_patient_assignments");
                        totalcount++;
                    }
                });
            });

            sqlhelper.db.transaction(function(tx) {
                tx.executeSql("SELECT *,0 as assignment_detail from tbl_patient_assignments where assignment_id=0", [], function(txs, results) {
                    // console.log(offlinehelper.count);


                    var patientAssignment = [];
                    for (i = 0; i < results.rows.length; i++)
                        patientAssignment.push(results.rows.item(i));
                    var anscount = 1;
                    if (patientAssignment.length > 0) {
                        $(patientAssignment).each(function(i, e) {
                            sqlhelper.db.transaction(function(tx) {
                                tx.executeSql("SELECT * from tbl_patient_assignment_details where assignment_details_id=0 and app_assignment_id=" + e.app_assignment_id, [], function(txs, results) {
                                    // console.log(offlinehelper.count);


                                    var patientAssignmentDetail = [];
                                    for (i = 0; i < results.rows.length; i++)
                                        patientAssignmentDetail.push(results.rows.item(i));
                                    e.assignment_detail = patientAssignmentDetail;
                                    if (anscount == patientAssignment.length) {
                                        offlinehelper.dataForServer.Registraion.patientAssignment = patientAssignment;
                                        console.log("Returned from tbl_patient_assignments");
                                        totalcount++;
                                    }
                                    anscount++;

                                });
                            });
                        })
                    } else {

                        console.log("Returned from tbl_patient_assignments");
                        totalcount++;
                    }


                });
            });

            sqlhelper.db.transaction(function(tx) {
                tx.executeSql("SELECT * from tbl_patient_assignment_details where assignment_details_id=0 and app_assignment_details_id NOT IN (SELECT app_assignment_details_id FROM tbl_patient_assignment_details where (app_assignment_id!=0 and app_assignment_id!='undefined' )) ", [], function(txs, results) {
                    // console.log(offlinehelper.count);


                    var patientAssignmentDetail = [];
                    for (i = 0; i < results.rows.length; i++)
                        patientAssignmentDetail.push(results.rows.item(i));

                    offlinehelper.dataForServer.Registraion.patientAssignmentDetail = patientAssignmentDetail;
                    console.log("Returned from tbl_patient_assignment_details");
                    totalcount++;


                });
            });

            sqlhelper.db.transaction(function(tx) {
                tx.executeSql("SELECT * from tbl_crisisplans where updated=1 ", [], function(txs, results) {
                    // console.log(offlinehelper.count);


                    var patientAssignmentDetail = [];
                    for (i = 0; i < results.rows.length; i++)
                        patientAssignmentDetail.push(results.rows.item(i));

                    offlinehelper.dataForServer.Registraion.crisisplan = patientAssignmentDetail;
                    console.log("Returned from crisisplan");
                    totalcount++;


                });
            });


            //ADDED BY SABIN >>

            sqlhelper.db.transaction(function(tx) {
                tx.executeSql("SELECT * from tbl_v2_feelings_assignments where assignment_id=0", [], function(txs, results) {

                    var feelingAssignments = [];
                    for (i = 0; i < results.rows.length; i++)
                        feelingAssignments.push(results.rows.item(i));

                    offlinehelper.dataForServer.other_modules.feelingAssignments = feelingAssignments;
                    console.log("Returned from Feeling assignments");
                    totalcount++;


                });
            });

            sqlhelper.db.transaction(function(tx) {
                tx.executeSql("SELECT * from tbl_v2_sk_thoughts_assignments where assignment_id=0", [], function(txs, results) {

                    var thoughtAssignments = [];
                    for (i = 0; i < results.rows.length; i++)
                        thoughtAssignments.push(results.rows.item(i));

                    offlinehelper.dataForServer.other_modules.thoughtAssignments = thoughtAssignments;
                    console.log("Returned from Thoughts assignments");
                    totalcount++;


                });
            });

            //Exposures
            sqlhelper.db.transaction(function(tx) {
                tx.executeSql("SELECT *,0 as assignment_detail from tbl_v2_sk_exposure_patients_assignments where assignment_id=0", [], function(txs, results) {
                    // console.log(offlinehelper.count);


                    var exposureAssignments = [];
                    for (i = 0; i < results.rows.length; i++)
                        exposureAssignments.push(results.rows.item(i));
                    var anscount = 1;
                    if (exposureAssignments.length > 0) {
                        $(exposureAssignments).each(function(i, e) {
                            sqlhelper.db.transaction(function(tx) {
                                tx.executeSql("SELECT * from tbl_v2_sk_exposure_patients_assignments_details where assignment_details_id=0 and app_assignment_id=" + e.app_assignment_id, [], function(txs, results) {
                                    // console.log(offlinehelper.count);


                                    var exposureAssignmentsDetail = [];
                                    for (i = 0; i < results.rows.length; i++)
                                        exposureAssignmentsDetail.push(results.rows.item(i));
                                    e.assignment_detail = exposureAssignmentsDetail;
                                    if (anscount == exposureAssignments.length) {
                                        offlinehelper.dataForServer.other_modules.exposureAssignments = exposureAssignments;
                                        console.log("Returned from tbl_v2_sk_exposure_patients_assignments");
                                        totalcount++;
                                    }
                                    anscount++;

                                });
                            });
                        })
                    } else {

                        console.log("Returned from tbl_v2_sk_exposure_patients_assignments");
                        totalcount++;
                    }


                });
            });



            //Skills
            sqlhelper.db.transaction(function(tx) {
                tx.executeSql("SELECT *,0 as assignment_detail from tbl_v2_sk_skills_assignments where assignment_id=0", [], function(txs, results) {
                    // console.log(offlinehelper.count);


                    var skillAssignments = [];
                    for (i = 0; i < results.rows.length; i++)
                        skillAssignments.push(results.rows.item(i));
                    var anscount = 1;
                    if (skillAssignments.length > 0) {
                        $(skillAssignments).each(function(i, e) {
                            sqlhelper.db.transaction(function(tx) {
                                tx.executeSql("SELECT * from tbl_v2_sk_skills_assignments_details where assignment_details_id=0 and app_assignment_id=" + e.app_assignment_id, [], function(txs, results) {
                                    // console.log(offlinehelper.count);


                                    var skillAssignmentsDetail = [];
                                    for (i = 0; i < results.rows.length; i++)
                                        skillAssignmentsDetail.push(results.rows.item(i));
                                    e.assignment_detail = skillAssignmentsDetail;
                                    if (anscount == skillAssignments.length) {
                                        offlinehelper.dataForServer.other_modules.skillAssignments = skillAssignments;
                                        console.log("Returned from tbl_v2_sk_exposure_patients_assignments");
                                        totalcount++;
                                    }
                                    anscount++;

                                });
                            });
                        })
                    } else {

                        console.log("Returned from tbl_v2_sk_exposure_patients_assignments");
                        totalcount++;
                    }


                });
            });
            //ADDED BY SABIN <<
            //
            offlinehelper.syncInterval = setInterval(function() {
                //console.warn("Total count is "+totalcount);
                if (totalcount == 13)
                    offlinehelper.syncWithOnline(showSyncDialog);
            }, 100);


        } else {
            console.log("Device is not online we cannot have a sync now");
        }
    },


    fetchRegistrations: function(json, success) {
        json = JSON.parse(json);
        if (json.show != "old") {
            var sql = "SELECT * from tbl_registrations where registration_status='1' ORDER BY sort_order ASC;";
        } else {
            var sql = "SELECT *,tbl_patient_assignments.answered_date as formatted_answer_date from tbl_patient_assignments INNER JOIN tbl_registrations ON (tbl_registrations.registration_id=tbl_patient_assignments.registration_id) where registration_status='1' ORDER BY answered_date DESC;";
        }
        //console.log(sql);
        var task_json = {};
        sqlhelper.db.transaction(function(tx) {
            tx.executeSql(sql, [], function(txs, results) {
                if (results.rows.length > 0) {
                    returndata = {
                        "status": "ok",
                        data: [],
                        message: "",
                        old_registrations: 0
                    };
                    for (i = 0; i < results.rows.length; i++) {
                        returndata.data.push(results.rows.item(i));
                    }
                    var sql2 = "SELECT count(*) recCount FROM tbl_patient_assignments INNER JOIN tbl_registrations ON (tbl_registrations.registration_id=tbl_patient_assignments.registration_id) WHERE registration_status='1' ORDER BY answered_date DESC;";
                    tx.executeSql(sql2, [], function(txss, results2) {
                        returndata.old_registrations = results2.rows.item(0).recCount;
                        success(returndata);
                    });
                    //success(returndata);
                } else {
                    returndata.status = "error";
                    returndata.message = "No Registrations";
                }
            });
        });
    },

    fetchHomeworks: function(json, success) {
        json = JSON.parse(json);
        var enabledHomeworks = offlinehelper.ShowHideModules("homework_id");

        if (typeof enabledHomeworks == "object") {
            if (enabledHomeworks.length > 0) {
                $hwIDs = enabledHomeworks.join();
                var sql = "SELECT * from tbl_homeworks INNER JOIN tbl_homework_assignments ON (tbl_homework_assignments.homework_id=tbl_homeworks.homework_id) WHERE tbl_homeworks.homework_status=1 AND tbl_homeworks.homework_id IN (" + $hwIDs + ") ORDER BY tbl_homeworks.sort_order ASC;";
                console.log("HOMEOWKR LIST = " + sql);
            } else {
                return false;
            }
        } else {
            var sql = "SELECT * from tbl_homeworks INNER JOIN tbl_homework_assignments ON (tbl_homework_assignments.homework_id=tbl_homeworks.homework_id) WHERE tbl_homeworks.homework_status=1 ORDER BY tbl_homeworks.sort_order ASC;";
        }

        console.log(sql);
        var task_json = {};
        sqlhelper.db.transaction(function(tx) {
            tx.executeSql(sql, [], function(txs, results) {
                if (results.rows.length > 0) {
                    returndata = {
                        "status": "ok",
                        data: [],
                        message: ""
                    };
                    for (i = 0; i < results.rows.length; i++) {
                        returndata.data.push(results.rows.item(i));
                    }
                    success(returndata);
                } else {
                    returndata.status = "error";
                    returndata.message = "No homeworks";
                }
            });
        });
    },

    fetchCrisisplans: function(json, success) {
        json = JSON.parse(json);
        var sql = "SELECT * from tbl_crisisplans WHERE plan_status=1;";
        //console.log(sql);
        var task_json = {};
        sqlhelper.db.transaction(function(tx) {
            tx.executeSql(sql, [], function(txs, results) {
                if (results.rows.length > 0) {
                    returndata = {
                        "status": "ok",
                        data: [],
                        message: ""
                    };
                    for (i = 0; i < results.rows.length; i++) {
                        returndata.data.push(results.rows.item(i));
                    }
                    success(returndata);
                } else {
                    returndata.status = "error";
                    returndata.message = "No Crisis Plans";
                }
            });
        });
    },

    fetchRegistrationSteps: function(json, success) {
        json = JSON.parse(json);
        var sql = "SELECT * from tbl_registration_steps where registration_id='" + json.registration_id + "' and step_status='1' order by sort_order ASC;";
        
        var task_json = {};
        sqlhelper.db.transaction(function(tx) {
            tx.executeSql(sql, [], function(txs, results) {
                if (results.rows.length > 0) {

                    returndata = {
                        "status": "ok",
                        data: {
                            "steps": [],
                            "old_assignment": null
                        },
                        message: "",
                        category: []
                    };
                    for (i = 0; i < results.rows.length; i++) {
                        returndata.data.steps.push(results.rows.item(i));
                    }
                    count = 0;
                    ordercount = 1;
                    if (json.assignment_id > 0 || json.app_assignment_id > 0) {
                        returndata.data.old_assignment = {
                            assignment: [],
                            details: []
                        };
                        sqlhelper.db.transaction(function(tx) {
                            //$assDetailSql = "SELECT * FROM tbl_patient_assignment_details where assignment_id ='"+json.assignment_id+"' OR app_assignment_id='"+json.app_assignment_id+"';";
                            $assDetailSql = "SELECT ad.*, s.step_name AS str_step_name,GROUP_CONCAT(ans.answer,'^') AS str_answer, GROUP_CONCAT((SELECT answer_cat_name  FROM tbl_answer_category WHERE answer_cat_id=ans.answer_cat_id),'^') AS str_cat_name FROM tbl_patient_assignment_details ad INNER JOIN tbl_registration_steps s ON s.step_id = ad.step_id INNER JOIN tbl_answers ans ON ans.answer_id=ad.answer_id WHERE ad.assignment_id='" + json.assignment_id + "' OR app_assignment_id='" + json.app_assignment_id + "' GROUP BY ad.step_id ORDER BY s.sort_order ASC;";
                            tx.executeSql($assDetailSql, [], function(txs, results) {
                                if (results.rows.length > 0) {
                                    var assignments = [];

                                    for (i = 0; i < results.rows.length; i++) {
                                        assignments.push(results.rows.item(i));
                                    }

                                    returndata.data.old_assignment.details = assignments;
                                }
                            });
                        });
                        sqlhelper.db.transaction(function(tx) {
                            // $assSql = "SELECT * FROM tbl_patient_assignments where assignment_id ='"+json.assignment_id+"' OR app_assignment_id='"+json.app_assignment_id+"';";
                            $assSql = "SELECT a.*, r.registration_name AS str_registration_name FROM tbl_patient_assignments a INNER JOIN tbl_registrations r ON r.registration_id=a.registration_id WHERE a.assignment_id ='" + json.assignment_id + "' OR app_assignment_id='" + json.app_assignment_id + "';";
                            tx.executeSql($assSql, [], function(txs, results) {
                                if (results.rows.length > 0) {
                                    var assignments = [];

                                    returndata.data.old_assignment.assignment = results.rows.item(0);
                                }
                            });
                        });

                        //fetch step name and sort order for date time template
                        sqlhelper.db.transaction(function(tx) {

                            $assDateTimeSql = "SELECT step_name, sort_order FROM tbl_registration_steps WHERE registration_id=(SELECT registration_id FROM tbl_patient_assignments WHERE assignment_id='" + json.assignment_id + "' OR app_assignment_id='" + json.app_assignment_id + "') AND template='steps_datetime';";
                            tx.executeSql($assDateTimeSql, [], function(txs, results) {
                                if (results.rows.length > 0) {

                                    returndata.data.old_assignment.datetime = results.rows.item(0);
                                }
                            });
                        });

                        //fetch step name and sort order for date  template
                        sqlhelper.db.transaction(function(tx) {

                            $assDateOnlySql = "SELECT step_name, sort_order FROM tbl_registration_steps WHERE registration_id=(SELECT registration_id FROM tbl_patient_assignments WHERE assignment_id='" + json.assignment_id + "' OR app_assignment_id='" + json.app_assignment_id + "') AND template='steps_date';";
                            tx.executeSql($assDateOnlySql, [], function(txs, results) {
                                if (results.rows.length > 0) {

                                    returndata.data.old_assignment.dateonly = results.rows.item(0);
                                }
                            });
                        });
                    }
                    var currentdate = moment().format("D MMM YYYY");
                    var current_time = moment().format("HH:mm");

                    $(returndata.data.steps).each(function(i, e) {

                        offlinehelper.datafetched = false;
                        e.current_date = currentdate;
                        e.current_time = current_time;
                        e.hid_date = moment().format("YYYY-MM-DD");
                        e.hid_time = moment().format("HH:mm:ss");
                        e.show_order = ordercount;

                        ordercount++;
                        if (e.special_case != 1) {

                            sqlhelper.db.transaction(function(tx) {
                                tx.executeSql("SELECT * FROM tbl_answer_category where step_id ='" + e.step_id + "' ORDER BY sort_order ASC;", [], function(txs, results) {
                                   /* console.log(e.step_id + " " + results.rows.length);
                                    console.log("SELECT * FROM tbl_answer_category where step_id ='" + e.step_id + "' ORDER BY sort_order ASC;");*/
                                    if (results.rows.length > 0) {
                                        var ans_cat = [];
                                        var ans_cat_length = 1;
                                        for (i = 0; i < results.rows.length; i++) {
                                            ans_cat.push(results.rows.item(i));
                                        }
                                        if (ans_cat.length > 0) {
                                            $(ans_cat).each(function(ix, ex) {
                                                if (ex.answer_cat_id == 0) {
                                                    var titlex = "app_answer_cat_id";
                                                    var valuex = ex.app_answer_cat_id;
                                                } else {
                                                    var titlex = "answer_cat_id";
                                                    var valuex = ex.answer_cat_id;
                                                }
                                                sqlhelper.db.transaction(function(tx) {
                                                    tx.executeSql("SELECT * FROM tbl_answers where " + titlex + " ='" + valuex + "' and answer_status=1 ORDER BY sort_order ASC ;", [], function(txs, results) {
                                                        if (results.rows.length > 0) {
                                                            var answers = [];
                                                            for (i = 0; i < results.rows.length; i++) {
                                                                answers.push(results.rows.item(i));
                                                            }
                                                            ex.answers = answers;
                                                        }

                                                        if (ans_cat_length >= ans_cat.length) {

                                                            e.category = ans_cat;
                                                            offlinehelper.datafetched = true;
                                                            count++;
                                                            if (count >= returndata.data.steps.length) {

                                                                console.log(returndata);
                                                                success(returndata);
                                                            }
                                                        }
                                                        ans_cat_length++;
                                                    })
                                                })
                                            })
                                        } else {
                                            offlinehelper.datafetched = true;
                                        }


                                    } else {
                                        sqlhelper.db.transaction(function(tx) {
                                            tx.executeSql("SELECT * FROM tbl_answers where step_id ='" + e.step_id + "' and answer_status=1 order by sort_order ASC;", [], function(txs, results) {
                                                if (results.rows.length > 0) {
                                                    var answers = [];
                                                    for (i = 0; i < results.rows.length; i++) {
                                                        answers.push(results.rows.item(i));
                                                    }
                                                    e.answers = answers;
                                                }
                                                count++;
                                                offlinehelper.datafetched = true;
                                                if (count >= returndata.data.steps.length) {
                                                    console.log(returndata);
                                                    success(returndata);
                                                }
                                            })
                                        })
                                    }


                                });
                            });

                        } else {
                            var userdata = $.jStorage.get('userdetails')

                            var special_answer = userdata.specialAnswers || "";
                           

                            if ($.trim(special_answer)!="") {
                                var spc_arr = special_answer.split(",");
                                sqlhelper.db.transaction(function(tx) {
                                    tx.executeSql("SELECT * FROM tbl_answers where answer_id  IN (" + special_answer + ") OR (answer_id=0 and app_answer_id IN (" + special_answer + "))  and answer_status=1 order by sort_order ASC;", [], function(txs, results) {
                                        if (results.rows.length > 0) {
                                            var answers = [];
                                            for (i = 0; i < results.rows.length; i++) {
                                                answers.push(results.rows.item(i));
                                            }
                                            e.answers = answers;
                                        }
                                        count++;
                                        if (count >= returndata.data.steps.length) {
                                            console.log(returndata);
                                            success(returndata);
                                        }
                                    });
                                });
                            } else {
                                //there is step marked as special case, but no special answer has been chosen from psychologist view
                                //so hide the registration
                                msgBox("Din lista är inte aktiverad. Skicka ett meddelande till din behandlare i BIP.");
                            }
                        }
                    })

                } else {
                    returndata.status = "error";
                    returndata.message = "No Registrations";
                }
            });
        });
    },


    /*Added by Sabin Aug 31st 2015 >>*/
    listModules: function(json, success) {
        json = JSON.parse(json);

        var feelingModuleExist = false;
        //first check if we have feelings, if yes then we need Manage feelings icon as well
        var sqlFeeling = "SELECT COUNT(*) as available_feelings FROM tbl_v2_feelings WHERE feeling_status='1'";
        sqlhelper.db.transaction(function(tx) {
            tx.executeSql(sqlFeeling, [], function(txs, results) {
                feelingModuleExist = parseInt(results.rows.item(0).available_feelings) > 0 ? true : false;
            });
        });
        $otherModsEnabled = offlinehelper.ShowHideModules("other_modules");
        if (typeof $otherModsEnabled == "object") {
            if ($otherModsEnabled.length > 0) {
                $modIDs = $otherModsEnabled.join();
                var sql = "SELECT m.module_id, m.module_name, m.module_icon, (SELECT COUNT(*) FROM tbl_v2_skills WHERE skill_type='thoughts' AND module_id=m.module_id AND skill_status=1) as total_thoughts, (SELECT COUNT(*) FROM tbl_v2_skills sk WHERE sk.skill_type='skills' AND sk.module_id=m.module_id  AND sk.skill_status=1 AND (SELECT COUNT(*) FROM tbl_v2_sk_exposure_steps WHERE module_id=m.module_id AND skill_id=sk.skill_id)>0) as total_skills, (SELECT COUNT(*) FROM tbl_v2_sk_exposure_patients ep WHERE ep.belongs_to='" + json.userid + "' AND ep.skill_id=(SELECT distinct skill_id FROM tbl_v2_skills WHERE module_id=m.module_id) AND (SELECT COUNT(*) FROM tbl_v2_sk_exposure_steps WHERE module_id=m.module_id AND skill_id=ep.skill_id)>0) as total_exposures from tbl_v2_modules m WHERE m.module_status='1' AND m.module_id IN(" + $modIDs + ") ORDER BY m.sort_order ASC;";
            } else {
                var sql = "SELECT m.module_id, m.module_name, m.module_icon, (SELECT COUNT(*) FROM tbl_v2_skills WHERE skill_type='thoughts' AND module_id=m.module_id AND skill_status=1) as total_thoughts, (SELECT COUNT(*) FROM tbl_v2_skills sk WHERE sk.skill_type='skills' AND sk.module_id=m.module_id  AND sk.skill_status=1 AND (SELECT COUNT(*) FROM tbl_v2_sk_exposure_steps WHERE module_id=m.module_id AND skill_id=sk.skill_id)>0) as total_skills, (SELECT COUNT(*) FROM tbl_v2_sk_exposure_patients ep WHERE ep.belongs_to='" + json.userid + "' AND ep.skill_id=(SELECT distinct skill_id FROM tbl_v2_skills WHERE module_id=m.module_id) AND (SELECT COUNT(*) FROM tbl_v2_sk_exposure_steps WHERE module_id=m.module_id AND skill_id=ep.skill_id)>0) as total_exposures from tbl_v2_modules m WHERE m.module_status='1' AND 1=2 ORDER BY m.sort_order ASC;";
                //we have done 1=2 in above query because we don't want to get any result if none of the modules are activated via activation template
            }
        } else {
            var sql = "SELECT m.module_id, m.module_name, m.module_icon, (SELECT COUNT(*) FROM tbl_v2_skills WHERE skill_type='thoughts' AND module_id=m.module_id AND skill_status=1) as total_thoughts, (SELECT COUNT(*) FROM tbl_v2_skills sk WHERE sk.skill_type='skills' AND sk.module_id=m.module_id  AND sk.skill_status=1 AND (SELECT COUNT(*) FROM tbl_v2_sk_exposure_steps WHERE module_id=m.module_id AND skill_id=sk.skill_id)>0) as total_skills, (SELECT COUNT(*) FROM tbl_v2_sk_exposure_patients ep WHERE ep.belongs_to='" + json.userid + "' AND ep.skill_id=(SELECT distinct skill_id FROM tbl_v2_skills WHERE module_id=m.module_id) AND (SELECT COUNT(*) FROM tbl_v2_sk_exposure_steps WHERE module_id=m.module_id AND skill_id=ep.skill_id)>0) as total_exposures from tbl_v2_modules m WHERE m.module_status='1' ORDER BY m.sort_order ASC;";
        }
      
        var task_json = {};
        sqlhelper.db.transaction(function(tx) {
            tx.executeSql(sql, [], function(txs, results) {

                returndata = {
                    "status": "ok",
                    data: [],
                    message: ""
                };

                //hide show modules as per app activation template start
                /* $feelingsEnabled = offlinehelper.ShowHideModules("my_feelings");
                 if($feelingsEnabled==1 || $feelingsEnabled==2 || $feelingsEnabled=="all"){
                     $("#MySkills_Module").find(".module-icons-stuffs").find("div[data-moduleid='0']").removeClass("hide");
                 }else{
                     $("#MySkills_Module").find(".module-icons-stuffs").find("div[data-moduleid='0']").addClass("hide");
                 }
                 //hide show modules as per app activation template end*/
                $feelingsEnabled = offlinehelper.ShowHideModules("my_feelings");

                if (results.rows.length > 0) {
                    if (feelingModuleExist == true && ($feelingsEnabled == 1 || $feelingsEnabled == 2 || $feelingsEnabled == "all")) {
                        var t = {
                            "module_id": 0,
                            "module_name": "Känslospaning",
                            "module_icon": "my_feelings.png",
                            "total_thoughts": "",
                            "total_skills": "",
                            "total_exposures": ""
                        }
                        returndata.data.push(t);
                    }
                    for (i = 0; i < results.rows.length; i++) {
                        returndata.data.push(results.rows.item(i));
                    }

                    success(returndata);
                } else {
                    if (feelingModuleExist == true && ($feelingsEnabled == 1 || $feelingsEnabled == 2 || $feelingsEnabled == "all")) {
                        var t = {
                            "module_id": 0,
                            "module_name": "Känslospaning",
                            "module_icon": "my_feelings.png",
                            "total_thoughts": "",
                            "total_skills": "",
                            "total_exposures": ""
                        }
                        returndata.data.push(t);
                        success(returndata);
                    }
                    returndata.status = "error";
                    returndata.message = "No Modules Available";
                }
            });
        });
    },
    checkIfModuleHasSkills: function(json, success) {
        json = JSON.parse(json);

        var sql = "select (SELECT COUNT(*) FROM tbl_v2_skills WHERE skill_status=1 AND skill_type='thoughts' AND module_id='" + json.moduleId + "') as total_thoughts,(SELECT COUNT(*) FROM tbl_v2_skills sk1 WHERE sk1.skill_status=1 AND sk1.skill_type='skills' AND sk1.module_id='" + json.moduleId + "' AND (SELECT COUNT(*) FROM `tbl_v2_sk_exposure_steps` WHERE step_status='1' AND skill_id=sk1.skill_id)>0) as total_skills,(SELECT COUNT(*) FROM tbl_v2_skills sk WHERE sk.skill_status=1 AND sk.skill_type='exposure' AND sk.module_id='" + json.moduleId + "' AND (SELECT COUNT(*) FROM `tbl_v2_sk_exposure_steps` WHERE step_status='1' AND skill_id=sk.skill_id)>0) as total_exposures";
        console.log(sql);
        sqlhelper.db.transaction(function(tx) {
            tx.executeSql(sql, [], function(txs, results) {
                returndata.data.total_thoughts = results.rows.item(0).total_thoughts;
                returndata.data.total_skills = results.rows.item(0).total_skills;
                returndata.data.total_exposures = results.rows.item(0).total_exposures;

                success(returndata);
            });
        });
    },
    fetchExposureSkillsSteps: function(type, json, success) {
        json = JSON.parse(json);

        if (type == "exposure") {
            var sql = "SELECT * from tbl_v2_sk_exposure_steps where module_id='" + json.moduleId + "' and skill_type='" + type + "' and step_status=1 order by sort_order ASC;";
        } else {
            var sql = "SELECT * from tbl_v2_sk_exposure_steps where module_id='" + json.moduleId + "' and skill_type='" + type + "' AND skill_id='" + json.skillId + "' and step_status=1 order by sort_order ASC;";
        }
        var task_json = {};
        var returndata = {
            "status": "ok",
            data: {
                "steps": [],
                "old_assignment": null
            },
            message: "",
            category: []
        };
        sqlhelper.db.transaction(function(tx) {
            tx.executeSql(sql, [], function(txs, results) {
                if (results.rows.length > 0) {


                    for (i = 0; i < results.rows.length; i++) {
                        returndata.data.steps.push(results.rows.item(i));
                    }
                    count = 0;
                    ordercount = 1;

                    $(returndata.data.steps).each(function(i, e) {

                        offlinehelper.datafetched = false;

                        e.show_order = ordercount;
                        ordercount++;

                        sqlhelper.db.transaction(function(tx) {
                            tx.executeSql("SELECT * FROM tbl_v2_skill_exposure_answer_category where step_id ='" + e.step_id + "' AND answer_cat_status=1 ORDER BY sort_order ASC;", [], function(txs, results) {

                                if (results.rows.length > 0) {
                                    var ans_cat = [];
                                    var ans_cat_length = 1;
                                    for (i = 0; i < results.rows.length; i++) {
                                        ans_cat.push(results.rows.item(i));
                                    }
                                    if (ans_cat.length > 0) {
                                        $(ans_cat).each(function(ix, ex) {
                                            if (ex.answer_cat_id == 0) {
                                                var titlex = "app_answer_cat_id";
                                                var valuex = ex.app_answer_cat_id;
                                            } else {
                                                var titlex = "answer_cat_id";
                                                var valuex = ex.answer_cat_id;
                                            }
                                            sqlhelper.db.transaction(function(tx) {
                                                tx.executeSql("SELECT * FROM tbl_v2_skill_exposure_answers where " + titlex + " ='" + valuex + "' and answer_status=1 ORDER BY sort_order ASC ;", [], function(txs, results) {
                                                    if (results.rows.length > 0) {
                                                        var answers = [];
                                                        for (i = 0; i < results.rows.length; i++) {
                                                            answers.push(results.rows.item(i));
                                                        }
                                                        ex.answers = answers;
                                                    }

                                                    if (ans_cat_length >= ans_cat.length) {

                                                        e.category = ans_cat;
                                                        offlinehelper.datafetched = true;
                                                        count++;
                                                        if (count >= returndata.data.steps.length) {

                                                            console.log(returndata);
                                                            success(returndata);
                                                        }
                                                    }
                                                    ans_cat_length++;
                                                })
                                            })
                                        })
                                    } else {
                                        offlinehelper.datafetched = true;
                                    }


                                } else {
                                    sqlhelper.db.transaction(function(tx) {
                                        tx.executeSql("SELECT * FROM tbl_v2_skill_exposure_answers where step_id ='" + e.step_id + "' and answer_status=1 order by sort_order ASC;", [], function(txs, results) {
                                            if (results.rows.length > 0) {
                                                var answers = [];
                                                for (i = 0; i < results.rows.length; i++) {
                                                    answers.push(results.rows.item(i));
                                                }
                                                e.answers = answers;
                                            }
                                            count++;
                                            offlinehelper.datafetched = true;
                                            if (count >= returndata.data.steps.length) {
                                                console.log(returndata);
                                                success(returndata);
                                            }
                                        })
                                    })
                                }


                            });
                        });
                    })

                } else {
                    returndata.status = "error";
                    returndata.message = "No Exposures";
                }
            });
        });
    },
    listSkillsItems: function(json, success) {
        var patientExposure = [];
        var skillStats = [];
        json = JSON.parse(json);




        if (json.skillType != "skills") {

            if (json.skillType == "exposure") {
                //fetch list of patient exposures heading
                var sqlexposure = "SELECT ea.skill_id, ea.exposure_name, ea.exposure_id, (SELECT COUNT(*) FROM tbl_v2_sk_exposure_patients_assignments WHERE exposure_id=ea.exposure_id) AS total_assignments, (SELECT module_id FROM `tbl_v2_skills` WHERE skill_id=ea.skill_id) AS moduleID  FROM tbl_v2_sk_exposure_patients ea WHERE ea.exposure_status='1' AND ea.belongs_to='" + json.userid + "'";
                sqlhelper.db.transaction(function(tx) {
                    tx.executeSql(sqlexposure, [], function(txe, resultsEx) {
                        if (resultsEx.rows.length > 0) {
                            for (var k = 0; k < resultsEx.rows.length; k++) {
                                patientExposure.push(resultsEx.rows.item(k));
                            }
                        }

                    });
                });
            }


            if (json.skillType == "thoughts") {
                var sql = "SELECT s.*, t.thought_type FROM tbl_v2_skills s INNER JOIN tbl_v2_sk_thoughts t ON t.skill_id = s.skill_id WHERE s.skill_type='" + json.skillType + "' AND s.module_id='" + json.moduleId + "' AND s.skill_status='1'";
            } else {
                var sql = "SELECT *, '' as thought_type FROM tbl_v2_skills WHERE skill_type='" + json.skillType + "' AND module_id='" + json.moduleId + "'";

            }
            sqlhelper.db.transaction(function(tx) {
                tx.executeSql(sql, [], function(txs, results) {
                    if (results.rows.length > 0) {
                        returndata = {
                            "status": "ok",
                            data: [],
                            patientExposure: "",
                            skillStats: "",
                            message: ""
                        };

                        for (i = 0; i < results.rows.length; i++) {
                            returndata.data.push(results.rows.item(i));
                        }

                        returndata.patientExposure = patientExposure;
                        returndata.skillStats = skillStats;

                        success(returndata);
                    } else {
                        returndata.status = "error";
                        returndata.message = "No Modules Available";
                    }
                });
            });
        } else {
            var sqlskills = "SELECT s.*, (SELECT COUNT(*) FROM tbl_v2_sk_skills_assignments WHERE skill_id = s.skill_id) AS total_assignments FROM tbl_v2_skills s WHERE s.module_id='" + json.moduleId + "' AND s.skill_type='skills' AND s.skill_status='1' ORDER BY last_updated DESC";
            sqlhelper.db.transaction(function(tx) {
                tx.executeSql(sqlskills, [], function(txs, resultSkills) {
                    if (resultSkills.rows.length > 0) {
                        returndata = {
                            "status": "ok",
                            data: [],
                            patientExposure: "",
                            skillStats: "",
                            message: ""
                        };
                        for (var x = 0; x < resultSkills.rows.length; x++) {
                            skillStats.push(resultSkills.rows.item(x));
                        }

                        returndata.skillStats = skillStats;
                        returndata.patientExposure = patientExposure;
                        success(returndata);
                    } else {
                        returndata.status = "error";
                        returndata.message = "No Skills Available";
                    }
                });
            });
        }

    },
    getSKillDetails: function(json, success) {
        json = JSON.parse(json);

        if (json.skillType == "thoughts") {
            var sql = "SELECT * FROM tbl_v2_sk_thoughts WHERE skill_id='" + json.skillId + "' AND module_id='" + json.moduleId + "'";
        }

        sqlhelper.db.transaction(function(tx) {
            tx.executeSql(sql, [], function(txs, results) {
                returndata.data = results.rows.item(0);

                success(returndata);
            });
        });
    },
    feelingStatistics: function(json, success) {
        var sql = "SELECT (select count(*) as no_of_days FROM (SELECT strftime('%Y-%m-%d',answered_date) as `date`, COUNT(*) FROM tbl_v2_feelings_assignments WHERE module_version='1' GROUP BY `date`) as d) AS total_days_v1, (select count(*) FROM tbl_v2_feelings_assignments WHERE module_version='1' ) as total_counts_v1, (select count(*) as no_of_days FROM (SELECT strftime('%Y-%m-%d',answered_date) as `date`, COUNT(*) FROM tbl_v2_feelings_assignments WHERE module_version='2' GROUP BY `date`) as d) AS total_days_v2, (select count(*) FROM tbl_v2_feelings_assignments WHERE module_version='2') as total_counts_v2, (select count(*) FROM tbl_v2_feelings_assignments WHERE module_version='2' AND feeling_type='1') as total_primary_feelings, (select count(*) FROM tbl_v2_feelings_assignments WHERE module_version='2' AND feeling_type='2') as total_secondary_feelings";
        sqlhelper.db.transaction(function(tx) {
            tx.executeSql(sql, [], function(txs, results) {
                returndata.status="ok";
                returndata.data.total_days_v1 = results.rows.item(0).total_days_v1;
                returndata.data.total_counts_v1 = results.rows.item(0).total_counts_v1;
                returndata.data.total_days_v2 = results.rows.item(0).total_days_v2;
                returndata.data.total_counts_v2 = results.rows.item(0).total_counts_v2;

                returndata.data.total_primary_feelings = results.rows.item(0).total_primary_feelings;
                returndata.data.total_secondary_feelings = results.rows.item(0).total_secondary_feelings;

                success(returndata);
            });
        });
    },
    getItemLength: function(obj, str) { //the second parameter is for debug purpose only
        var ret = 0;
        if (str == "feeling_definitions") {
            if (obj == 0) {
                ret = 0;
            } else {
                ret = 1;
            }
        } else {
            if (typeof obj !== "undefined" && obj !== null) {
                ret = parseInt(obj.length);

            } else {
                ret = 0;
            }
        }
        //  offlinehelper.responseDataTextChunk[str] = ret;

        return parseInt(ret);
    },
    feelingLists: function(json, success) {
        var sql = "SELECT * FROM tbl_v2_feelings WHERE feeling_status='1' ORDER BY sort_order ASC";
        sqlhelper.db.transaction(function(tx) {
            tx.executeSql(sql, [], function(txs, results) {
                if (results.rows.length > 0) {
                    returndata = {
                        "status": "ok",
                        data: [],
                        message: ""
                    };

                    for (i = 0; i < results.rows.length; i++) {
                        returndata.data.push(results.rows.item(i));
                    }

                    success(returndata);
                } else {
                    returndata.status = "error";
                    returndata.message = "No feelings Available";
                }
            });
        });
    },
    showFeelingDefinitions: function(json, success) {
        var sql = "select primary_feelings, secondary_feelings FROM tbl_v2_feelings_definition";
        sqlhelper.db.transaction(function(tx) {
            tx.executeSql(sql, [], function(txs, results) {
                returndata.data.primary = results.rows.item(0).primary_feelings;
                returndata.data.secondary = results.rows.item(0).secondary_feelings;

                success(returndata);
            });
        });
    },
    resetSyncProgressBar: function() {
        $(".download-content-msg").find(".pc-done").html("Förbereder...");
        $(".download-content-msg").find(".download-progress").css("width", "0%");
    },
    checkOnlineStatus: function() { //added by sabin remove from common.js checkOnlineStatus
        setInterval(function() {
            if (navigator.onLine == false && offlinehelper.hidealert == false) {
                $('.offline_dialogue').show();
            } else {

                $('.offline_dialogue').hide();
            }
        }, 1000);
    },
    checkIfRunningFirstTime: function() {
        sqlhelper.initiateDatabase();
        sqlhelper.db.transaction(function(tx) {
            tx.executeSql("SELECT * from tbl_user;", [], function(txs, results) {
                if (results != undefined && results.rows != undefined && results.rows.length > 0) {
                    //console.warn("App is not running for first time so display login screen");
                    offlinehelper.showHideVerificationWindow("hide");
                    // success(false);
                } else {
                    //console.warn("Application running for first time display verification screen.");
                    offlinehelper.showHideVerificationWindow("show");
                    // success(false);
                }
            }, function(error) {
                //console.warn("Error:: Application running for first time display verification screen.");
                offlinehelper.showHideVerificationWindow("show");
                //success(false);
            });
        });
    },
    showHideVerificationWindow: function(doWhat) {
        var sel = $("#Login").find(".login-wrapper");
        if (doWhat == "show") {
            sel.find("h1[data-key='Login']").hide();
            sel.find("h1[data-key='VerifyDevice']").show();

            sel.find("#txtEmail").hide();
            sel.find("#verification_code").val("").show();
            sel.find("#txtPwd").val("");

            sel.find("#btnLogin").hide();
            sel.find("#btnLogin").closest(".ui-btn").hide();
            sel.find("#btnVerify").show();
            sel.find("#btnVerify").closest(".ui-btn").show();
        } else {
            sel.find("h1[data-key='Login']").show();
            sel.find("h1[data-key='VerifyDevice']").hide();

            sel.find("#txtEmail").show();
            sel.find("#verification_code").hide();

            sel.find("#btnLogin").show();
            sel.find("#btnLogin").closest(".ui-btn").show();
            sel.find("#btnVerify").hide();
            sel.find("#btnVerify").closest(".ui-btn").hide();
        }
    },
    clearAppCache: function() {
        //Drop all tables
        if (confirm("Är du säker på att du vill ta bort all data i appen?")) {
            var tables = ['tbl_user', 'tbl_tasks', 'tbl_training', 'tbl_registrations', 'tbl_registration_steps', 'tbl_answer_category', 'tbl_answers', 'tbl_homeworks',
                'tbl_homework_assignments', 'tbl_crisisplans', 'tbl_patient_assignments', 'tbl_patient_assignment_details', 'tbl_v2_feelings', 'tbl_v2_feelings_definition', 'tbl_v2_modules',
                'tbl_v2_sk_exposure_patients', 'tbl_v2_sk_exposure_patients_assignments', 'tbl_v2_sk_exposure_patients_assignments_details', 'tbl_v2_sk_skills_assignments', 'tbl_v2_sk_skills_assignments_details',
                'tbl_v2_sk_exposure_steps', 'tbl_v2_sk_thoughts', 'tbl_v2_skill_exposure_answer_category', 'tbl_v2_skill_exposure_answers', 'tbl_v2_skills', 'tbl_v2_sk_thoughts_assignments',
                'tbl_v2_feelings_assignments','tbl_extra_files_to_download','sqlite_sequence'
            ];
            sqlhelper.clearAllData(tables);
        }

        //now clear Cache
    },
    downloadAudioFiles: function() {
        filehelper.filesToDownload = [];
        console.warn("NOW DOWNLOAD MODULES CALLED");
        if (offlinehelper.isSelfHarm == false) {
            var sql = "SELECT file_name as file, file_url as url, 'audio' as type FROM tbl_extra_files_to_download WHERE file_url!='' AND item_type!='countdown_audio' ORDER BY type ASC";
        } else {
            var sql = "SELECT thought_sound_file as file, sound_url as url, 'audio' as type FROM tbl_v2_sk_thoughts WHERE thought_sound_file!='' UNION SELECT module_icon as file, asset_url as url, 'icon' as type FROM tbl_v2_modules WHERE module_icon!='' UNION SELECT file_name as file, file_url as url, 'audio' as type FROM tbl_extra_files_to_download WHERE file_url!='' ORDER BY type ASC";
        }
        sqlhelper.db.transaction(function(tx) {
            tx.executeSql(sql, [], function(txs, results) {
                if (results.rows.length > 0) {


                    for (i = 0; i < results.rows.length; i++) {
                        /*$file = {
                            'file': results.rows.item(i).file,
                            'type': results.rows.item(i).type,
                            'url': results.rows.item(i).url
                        };
                        filehelper.filesToDownload.push($file);*/
                        filehelper.filesToDownload.push(results.rows.item(i).url);
                    }

                    /* filehelper.filesToDownload.push({
                       'file': "countdown_alert.mp3",
                       'type': "audio",
                       'url': offlinehelper.alertURL
                     });*/

                    filehelper.totalFilesToDownload = filehelper.filesToDownload.length;

                    filehelper.downloadModuleFiles();


                } else {
                    returndata.status = "error";
                    returndata.message = "No feelings Available";
                }

            }, function(err) {
                console.log("Error : " + JSON.stringify(err));
            });
        });
    },
    ShowHideModules: function(key) {
        var mod = offlinehelper.EnabledModules;
        /* var mod = {
                     "available_modules": {
                       "registration":1,
                       "homework_module":1,
                       "homework_id": [3],
                       "crisis_plan": 1,
                       "my_skills":1,
                       "my_feelings":1,
                       "other_modules": [1,2]
                     }
                 };*/

        if (mod != undefined) {
            $val = mod[key];
            return $val;
        } else {
            return "all";
        }
    },
    UpdateAvailableModules: function(modules){
        var userdetails = $.jStorage.get('userdetails');
        var toupdate = {
            where: {
                'user_id': userdetails.user_id,
            },
            fields: {
                'availableModules': modules
            }
        };
        sqlhelper.updateData('tbl_user', toupdate);
    },
    checkModulesEnabled: function(json, success) {

            json = JSON.parse(json);
          
            var returndata = {
                    'status': 'ok',
                    'data': {
                        'hasRegistration': [],
                        'homeworks': [],
                        'crisisplans': []
                    }
                };

            var enabledHomeworks = offlinehelper.ShowHideModules("homework_id");
            if (typeof enabledHomeworks == "object") {
                if (enabledHomeworks.length > 0) {
                    $hwIDs = enabledHomeworks.join();
                    var sqlHomeworks = "SELECT (SELECT count(*) as totalhomeworks FROM tbl_homeworks  INNER JOIN tbl_homework_assignments ON (tbl_homework_assignments.homework_id=tbl_homeworks.homework_id) WHERE tbl_homeworks.homework_status=1 AND tbl_homeworks.homework_id IN(" + $hwIDs + ")) AS TotalHomeworks, (SELECT count(*) as totalhomeworks FROM tbl_homeworks  INNER JOIN tbl_homework_assignments ON (tbl_homework_assignments.homework_id=tbl_homeworks.homework_id) where already_viewed=0 AND tbl_homeworks.homework_status=1 AND tbl_homeworks.homework_id IN(" + $hwIDs + ")) as NewHomeworks, (SELECT count(*) as total_crisis_plans FROM tbl_crisisplans WHERE plan_status=1) AS TotalCrisisPlans, (SELECT count(*) as total_crisis_plans FROM tbl_crisisplans where already_read=0 AND plan_status=1) AS NewCrisisPlans, (SELECT hasRegistrations from tbl_user where username='" + json.username + "' COLLATE NOCASE and password='" + json.password + "') as hasRegistration";
                } else {
                    var sqlHomeworks = "SELECT (SELECT count(*) as totalhomeworks FROM tbl_homeworks  INNER JOIN tbl_homework_assignments ON (tbl_homework_assignments.homework_id=tbl_homeworks.homework_id) WHERE tbl_homeworks.homework_status=1 AND 1=2) AS TotalHomeworks, (SELECT count(*) as totalhomeworks FROM tbl_homeworks  INNER JOIN tbl_homework_assignments ON (tbl_homework_assignments.homework_id=tbl_homeworks.homework_id) where already_viewed=0 AND tbl_homeworks.homework_status=1 AND 1=2) as NewHomeworks, (SELECT count(*) as total_crisis_plans FROM tbl_crisisplans WHERE plan_status=1) AS TotalCrisisPlans, (SELECT count(*) as total_crisis_plans FROM tbl_crisisplans where already_read=0 AND plan_status=1) AS NewCrisisPlans, (SELECT hasRegistrations from tbl_user where username='" + json.username + "' COLLATE NOCASE and password='" + json.password + "') as hasRegistration";
                    //1=2 used in above queries just because no homeworks are activated and we don't want to fetch them.
                }

            } else {
                var sqlHomeworks = "SELECT (SELECT count(*) as totalhomeworks FROM tbl_homeworks  INNER JOIN tbl_homework_assignments ON (tbl_homework_assignments.homework_id=tbl_homeworks.homework_id) WHERE tbl_homeworks.homework_status=1) AS TotalHomeworks, (SELECT count(*) as totalhomeworks FROM tbl_homeworks  INNER JOIN tbl_homework_assignments ON (tbl_homework_assignments.homework_id=tbl_homeworks.homework_id) where already_viewed=0 AND tbl_homeworks.homework_status=1) as NewHomeworks, (SELECT count(*) as total_crisis_plans FROM tbl_crisisplans WHERE plan_status=1) AS TotalCrisisPlans, (SELECT count(*) as total_crisis_plans FROM tbl_crisisplans where already_read=0 AND plan_status=1) AS NewCrisisPlans, (SELECT hasRegistrations from tbl_user where username='" + json.username + "' COLLATE NOCASE and password='" + json.password + "') as hasRegistration";
            }
            console.clear();

            returndata.status = "ok";
            var returndata_hw = {
                total_homeworks: 0,
                new_homeworks: 0
            };

            var returndata_cp = {
                total_crisis_plans: 0,
                new_crisis_plans: 0
            };
            console.clear();
            sqlhelper.db.transaction(function(tx) {
                tx.executeSql(sqlHomeworks, [], function(txs, results) {

                    returndata.data.hasRegistration = results.rows.item(0).hasRegistration;

                    returndata_hw.total_homeworks = results.rows.item(0).TotalHomeworks;
                    returndata_hw.new_homeworks = results.rows.item(0).NewHomeworks;
                    returndata.data.homeworks = JSON.stringify(returndata_hw);

                    returndata_cp.total_crisis_plans = results.rows.item(0).TotalCrisisPlans;
                    returndata_cp.new_crisis_plans = results.rows.item(0).NewCrisisPlans;
                    returndata.data.crisisplans = JSON.stringify(returndata_cp);

                    success(returndata);

                }, function(err) {
                    console.log("Error : " + JSON.stringify(err));
                });
            });
        }
        /*Added by Sabin Aug 31st 2015 <<*/

    //Functions for webservice
};



// $('#review_rating_1_1 select').on('focus', valueReviewFocused);
// $('#review_rating_2_2 select').on('focus', valueReviewFocused);
// $('#review_rating_2_4 select').on('focus', valueReviewFocused);

// $('#review_rating_1_1 select').on('blur', valueReviewBlurred);
// $('#review_rating_2_2 select').on('blur', valueReviewBlurred);
// $('#review_rating_2_4 select').on('blur', valueReviewBlurred);


//$('#tidigare_review').off('click', '.bip_edit', onBipReviewEdit);
