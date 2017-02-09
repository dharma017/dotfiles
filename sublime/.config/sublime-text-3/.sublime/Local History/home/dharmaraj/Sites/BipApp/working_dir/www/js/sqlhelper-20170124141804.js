/********* Task Related Functions  ---- Training List page--******/
var sqlhelper = {
    db:0,
  //  fields:"",
   // dataValues:"",
    initiateDatabase:function(response,success){
      console.log("Initiating database");
     // username =$("#txtEmail").val().trim();
    // pass=$("#txtPwd").val().trim();
      if(window.cordova && IsIDevice){
        db = window.sqlitePlugin.openDatabase({name: "bip.sqlite", location: 'default',androidDatabaseImplementation:2,androidLockWorkaround: 1});
        sqlhelper.db = window.sqlitePlugin.openDatabase({name: "bip.sqlite", location: 'default',androidDatabaseImplementation:2,androidLockWorkaround: 1});  
      }else{
        sqlhelper.db = window.openDatabase("bip.sqlite", '1', 'my', 1024 * 1024 * 100); // browser
      }
      
     // console.log(sqlhelper.db);
//      sqlhelper.initiateTables(db)
    },
    initiateTables:function(db){

      //Selecting rowid gives you autoincremented value hence no need for primary key
          db.transaction(function(tx) {
          
   
            var tabledata = {
                'tablename':'tbl_user',
                'fielddatas': [
                    {
                      "field": "user_id",
                      "datatype": "integer",
                      "primary_key": 0
                    },
                    {
                      "field": "username",
                      "datatype": "text",
                      "primary_key": 0
                    },
                    {
                      "field": "password",
                      "datatype": "text",
                      "primary_key": 0
                    },
                    {
                      "field": "fullname",
                      "datatype": "integer",
                      "primary_key": 0
                    },
                    {
                      "field": "new_start_page",
                      "datatype": "integer",
                      "primary_key": 0
                    },
                    {
                      "field": "enable_msg_alert ",
                      "datatype": "integer",
                      "primary_key": 0
                    },
                    {
                      "field": "training",
                      "datatype": "integer",
                      "primary_key": 0
                    },
                    {
                      "field": "hasRegistrations",
                      "datatype": "integer",
                      "primary_key": 0
                    },
                    {
                      "field": "homeworks",
                      "datatype": "text",
                      "primary_key": 0
                    },
                    {
                      "field": "lastSyncedDate",
                      "datatype": "text",
                      "primary_key": 0
                    },
                    {
                      "field": "reminder",
                      "datatype": "text",
                      "primary_key": 0
                    },
                    {
                      "field": "feedbackMessage",
                      "datatype": "text",
                      "primary_key": 0
                    }
                    
                ]
            };
          sqlhelper.CreateTable(tx,'tbl_user',fields);




          var tabledata = {
                'tablename':'tbl_tasks',
                'fielddatas': [
                    {
                      "field": "task_id",
                      "datatype": "integer",
                      "primary_key": 1
                    },
                     {
                      "field": "task_tag",
                      "datatype": "text",
                      "primary_key": 0
                    },
                     {
                      "field": "task_heading",
                      "datatype": "text",
                      "primary_key":0
                    },
                     {
                      "field": "task_hide_graph",
                      "datatype": "integer",
                      "primary_key": 0
                    }
                    
                ]
            };
          
          sqlhelper.CreateTable(tx,'tbl_user',fields);
          
          var tabledata = {
                'tablename':'tbl_training',
                'fielddatas': [
                   
                     {
                      "field": "task_id",
                      "datatype": "text",
                      "primary_key": 0
                    },
                     {
                      "field": "trainingDateTime",
                      "datatype": "text",
                      "primary_key": 0
                    },
                     {
                      "field": "estimatedValue",
                      "datatype": "text",
                      "primary_key": 0
                    },,
                     {
                      "field": "estimatedValueEnd",
                      "datatype": "integer",
                      "primary_key": 0
                    },
                     {
                      "field": "trainingDuration",
                      "datatype": "text",
                      "primary_key": 0
                    },
                     {
                      "field": "type",
                      "datatype": "text",
                      "primary_key": 0
                    },
                     {
                      "field": "comment",
                      "datatype": "text",
                      "primary_key": 0
                    },
                     {
                      "field": "edited",
                      "datatype": "integer",
                      "primary_key": 0
                    }
                    
                ]
            };
          
          sqlhelper.CreateTable(tx,'tbl_training',fields);

           //tx.executeSql('CREATE TABLE IF NOT EXISTS tbl_user (user_id integer primary key,user_server_id integer, username text, password text)');
      });

    },
    CreateTable:function(tabledata,success) {
     var fields=sqlhelper.generateFields(tabledata.fielddatas);
   //  console.log(fields);
      sqlhelper.db.transaction(function(tx) {
          tx.executeSql('CREATE TABLE IF NOT EXISTS '+tabledata.tablename+fields);
          success("true");
      });
     
    },
      insertData:function(tablename,field,data){
        //  console.log(tablename+" " +field)
          sqlhelper.db.transaction(function(tx) {
            var sqlquery='INSERT INTO '+tablename+' ('+field+') values('+data+')';
             
             tx.executeSql(sqlquery,[],function(){ console.log('Success'); },sqlhelper.errorCB);
              
          });

    },
    insertJSONDataFirstSync:function(tablename,data,callback, errorCallback){ //method added by sabin 
          var dfrd = $.Deferred();
          var fields=sqlhelper.separateFieldData(data,"field");
          var dataval=sqlhelper.separateFieldData(data,"value");
          sqlhelper.db.transaction(function(tx) {
            var sqlquery='INSERT INTO '+tablename+' ('+fields+') values('+dataval+')';
           
             tx.executeSql(sqlquery,[],function(tx, result){
              dfrd.resolve(result);
              
              if(callback!=undefined){
                  callback(result);
              }
              
            },function(tx,e){
                if(errorCallback!=undefined){
                    errorCallback();
                }
            });
             
          });
          return dfrd.promise();
    },
    insertJSONData:function(tablename,data,callback){
          var fields=sqlhelper.separateFieldData(data,"field");
          var dataval=sqlhelper.separateFieldData(data,"value");
          sqlhelper.db.transaction(function(tx) {
            var sqlquery='INSERT INTO '+tablename+' ('+fields+') values('+dataval+')';
           // console.log(sqlquery);
             tx.executeSql(sqlquery,[],function(){ 
              console.log('Success'); 
            },sqlhelper.errorCB);
              if(callback!=undefined){
                callback();
              }
          });

    }, 
    separateFieldData:function(data,rettype){

        var f="";
        var d="";

        for(fields in data){
            if(f!="")
              f+=",";
            if(d!="")
              d+=",";

            f+=fields;
            d+="'"+data[fields]+"'";

        }

        if(rettype=='field')
          return f;
        else
          return d;
    },
    updateData:function(tablename,data,callback){
           // console.log(tablename+" " +field)
            var dataval=sqlhelper.generateUpdateFields(data.fields);
            var where=sqlhelper.generateUpdateFields(data.where);
            //console.log(dataval);
            sqlhelper.db.transaction(function(tx) {
                var sqlquery="Update "+tablename+" set "+dataval+" where "+where;
                //console.log(sqlquery);
                 tx.executeSql(sqlquery,[],function(){ 
                     if(callback!=undefined){
                        callback();
                      }
                  },sqlhelper.errorCB);
                 
            }); 
           

    },
    updateDatabase:function(tablename,data,callback){
           // console.log(tablename+" " +field)
            var dataval=sqlhelper.generateUpdateFields(data.fields);
            var where=sqlhelper.generateUpdateCondition(data.where);
            console.log(dataval);
            sqlhelper.db.transaction(function(tx) {
                var sqlquery="Update "+tablename+" set "+dataval+" where "+where;
               // console.log(sqlquery);
                 tx.executeSql(sqlquery,[],function(){ 
                     if(callback!=undefined){
                        callback();
                      }
                  },sqlhelper.errorCB);
                 
            }); 
           

    },
    deleteData:function(tablename,data,callback){
           // console.log(tablename+" " +field)
            var where=sqlhelper.generateUpdateFields(data.where);
            sqlhelper.db.transaction(function(tx) {
                var sqlquery="DELETE FROM "+tablename+" where "+where;
                if(tablename=="tbl_patient_assignment_details"){
                  //console.warn(sqlquery);
                }
                 tx.executeSql(sqlquery,[],function(){ 
                     if(callback!=undefined){
                        callback();
                      }
                  },sqlhelper.errorCB);
                 
            }); 
           

    },
    generateUpdateFields:function(data){

        var d="";

        for(fields in data){
            if(d!="")
              d+=",";

           
            d+=fields+"='"+data[fields]+"'";

        }
        
        return d;

    },
    generateUpdateCondition:function(data){

        var d="";

        for(fields in data){
            if(d!="")
              d+=" AND ";

           
            d+=fields+"='"+data[fields]+"'";

        }
        
        return d;

    }, 
    generateFields:function(fields){

        var fieldstr="(";

          for(i=0;i<fields.length;i++){
            if(i!=0)
                fieldstr+=",";
              if(fields[i].primary_key==1)
                fieldstr+=fields[i].field+" "+fields[i].datatype +" primary key autoincrement";
              else
                fieldstr+=fields[i].field+" "+fields[i].datatype;
          }
          fieldstr+=")";
        return  fieldstr;

    },
     getFromDB:function(tablename,success) {
        
        sqlhelper.db.transaction(function(tx) {
            tx.executeSql("SELECT * from "+tablename+";", [], success, sqlhelper.errorCB);
            
        });
      
    },
    // form the query
    queryDB:function(tx,tablename) {
      tx.executeSql("SELECT * from "+tablename+";", [], querySuccess, errorCB);
    },
    // Display the results
    querySuccess:function(tx, results) {
       var len = results.rows.length; 
        console.log(results);
    },
    // Transaction error callback
    errorCB:function (err) {
      //console.warn(err);
      //console.warn("Error processing SQL: " + err.code);
    },
    // Success error callback
    successCB:function () {
    },
    dropTables:function(tables){
        if(tables.length>0){
            sqlhelper.db.transaction(function(tx) {
                for(var k=0; k<tables.length;k++){
                    tx.executeSql("DROP TABLE "+tables[k]+";", [], function(){}, sqlhelper.errorCB);
                }
                
            });
        }
    },
    clearAllData: function(tables){
       if(window.sqlitePlugin!=undefined){
          localStorage.clear();
          $.jStorage.flush();
          // window.sqlitePlugin.deleteDatabase("bip.sqlite",location: 'default');
          window.sqlitePlugin.deleteDatabase({name: 'bip.sqlite', location: 'default'});
          msgBox("Appen är tömd på data..");
          var rel = setTimeout(function(){
              location.reload();
              clearTimeout(rel);
          },1000);

       }else{
           if(tables.length>0){
                var d = 1;
                try{
                    sqlhelper.db.transaction(function(tx) {
                        var k;
                        for(k=0; k<tables.length;k++){

                            tx.executeSql("DROP TABLE "+tables[k]+";", [], function(){
                                //console.warn("Value of d = "+d+", total tables = "+tables.length);
                                if(d==tables.length){
                                    localStorage.clear();
                                    $.jStorage.flush();
                                    location.reload();
                                    msgBox("Appen är tömd på data");
                                }
                                d++;
                            }, sqlhelper.errorCB);
                        }
                        //console.warn("value of d outside executesql = "+d);
                        if(d==tables.length){
                            //console.warn("Matches total");
                        }
                    });
                }catch(e){
                    localStorage.clear();
                    $.jStorage.flush();
                    location.reload();
                    msgBox("Appen är tömd på data");
                }
            }else{
                localStorage.clear();
                $.jStorage.flush();
                location.reload();
                msgBox("Appen är tömd på data.");
            }
      }
    },
    silentClear: function(){
       if(window.sqlitePlugin!=undefined){
          localStorage.clear();
          $.jStorage.flush();
          // window.sqlitePlugin.deleteDatabase("bip.sqlite",location: 'default');
          window.sqlitePlugin.deleteDatabase({name: 'bip.sqlite', location: 'default'});
       }
    }
};



// $('#review_rating_1_1 select').on('focus', valueReviewFocused);
// $('#review_rating_2_2 select').on('focus', valueReviewFocused);
// $('#review_rating_2_4 select').on('focus', valueReviewFocused);

// $('#review_rating_1_1 select').on('blur', valueReviewBlurred);
// $('#review_rating_2_2 select').on('blur', valueReviewBlurred);
// $('#review_rating_2_4 select').on('blur', valueReviewBlurred);


//$('#tidigare_review').off('click', '.bip_edit', onBipReviewEdit);
