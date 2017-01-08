/*
       Licensed to the Apache Software Foundation (ASF) under one
       or more contributor license agreements.  See the NOTICE file
       distributed with this work for additional information
       regarding copyright ownership.  The ASF licenses this file
       to you under the Apache License, Version 2.0 (the
       "License"); you may not use this file except in compliance
       with the License.  You may obtain a copy of the License at

         http://www.apache.org/licenses/LICENSE-2.0

       Unless required by applicable law or agreed to in writing,
       software distributed under the License is distributed on an
       "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
       KIND, either express or implied.  See the License for the
       specific language governing permissions and limitations
       under the License.
 */
package com.bupsll.bip;




import android.app.Application;
import android.content.Context;
import android.os.Bundle;

import com.parse.*;

public class bipapp extends Application 
{
	private static bipapp instance = new bipapp();
    @Override
    public void onCreate() { 
        super.onCreate();

        try {
          Parse.initialize(this, "xijLovXRp4oCdtmSVTeYyEzDpPg4cQsOIylhJfJw","VVognXXYVIRVnhnVz6PeLAM4SY8lThDguZNGWWXO");
          //PushService.setDefaultPushCallback(this, MainActivity.class); //New version of parse (1.10) doesnot require this line 
          ParseInstallation.getCurrentInstallation().saveInBackground();
         // PushService.subscribe(this, "", MainActivity.class);
       }catch(Exception e){
         //  e.printStackTrace();
       }
    }
    

    public bipapp() {
        instance = this;
    }

    public static Context getContext() {
        return instance;
    }
    
}
