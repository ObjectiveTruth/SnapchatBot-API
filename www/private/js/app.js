(function(){
    var app = angular.module('mod-panel', [
        "ngSanitize",
        "com.2fdevs.videogular",
        "com.2fdevs.videogular.plugins.controls",
        "com.2fdevs.videogular.plugins.overlayplay",
        "com.2fdevs.videogular.plugins.poster" 
        ]);

    app.controller('mod-panelController', ['$http', '$sce', function($http, $sce){
        var DEFAULT_JSON_SNAP = {username: "N/A", 
            permissionCode: 0,
            webpicture: "images/blankImage.jpg"};
        //this is used to create a local object instead of using this everywhere
        //which is ambiguous
        var panel = this;
        //initial values when there is nothing returned when looking for new snaps
        panel.currentSnap = DEFAULT_JSON_SNAP;
        //used to figure out if buttons should be disabled
        panel.snapsInQueue = false;
        //Initial value is false until getNextSuccess is called
        panel.isVideo = false;

        this.config = {
            sources: [
                {src: $sce.trustAsResourceUrl("http://static.videogular.com/assets/videos/videogular.webm"), type: "video/webm"}
                ],
            loop: false,
            preload : true,
            tracks : undefined,
            theme: "bower_components/videogular-themes-default/videogular.css",
            plugins: {
            }
        };

        this.changeSource = function (newSource) {
            this.config.sources = [{src: $sce.trustAsResourceUrl(newSource), type: "video/webm"}];
        };

        //Called when the ThumbsDown button is pressed
        this.notApproved = function(){
            //Send a request to pop the next in the list
            $http.post('/popnext/false', panel.currentSnap).
                success(function(){
                    $http.get('/getnext').success(panel.getNextSuccess).
                        error(function(data, status, headers, config){
                            //Error Handling for getnext
                        });
                }).
                error(function(){
                    //Error Handling for initial post


                });
        };


        //Called when the thumbs up button is pressed
        this.approved = function(){
            //Send a request to pop the next in the list
            $http.post('/popnext/true', panel.currentSnap).
                success(function(){
                    $http.get('/getnext').success(panel.getNextSuccess).
                        error(function(data, status, headers, config){
                            //Error Handling for get next
                        });
                }).
                error(function(){
                    //Error Handling for initial post


                });
        };

        //if 200 code returned look for next snap
        panel.getNextSuccess = function(data){
                        if(_.isEmpty(data)){
                            panel.currentSnap = DEFAULT_JSON_SNAP;
                            panel.snapsInQueue = false;
                            panel.isVideo = false;
                        }else{
                            if(data.type == 1){
                                panel.isVideo = true;
                                data.webpicture = "";
                            }else{
                                panel.isVideo = false;
                                data.webpicture = "/getwebmedia/" +
                                    data.filename;
                            }
                            //If its a video update the player
                            if(panel.isVideo){
                                panel.changeSource("/getwebmedia/" + 
                                    data.filename);
                            }
                            panel.currentSnap = data;
                            panel.snapsInQueue = true;
                        }
                    }

        //Initial Search for the next snap awaiting approval from server
        //Run when the page first loads and only once
        $http.get('/getnext').
                success(panel.getNextSuccess).
                error(function(data, status, headers, config){
                    //Error Handling
                });

        //Called when drop down box button is called saying "don't show snaps"
        this.banUser = function(){
            console.log(JSON.stringify(panel.currentSnap));
            $http.post('/banuser', panel.currentSnap).
                success(function(){
                    //Just update the permissionCode as per below
                }).
                error(function(){
                    //Error Handling for initial post
            });
            panel.currentSnap.permissionCode = 0;
        };

    }]);

})();
