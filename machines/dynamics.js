// GLOBAL VARIABLES
var $ = jQuery;
var pageDir;
var pageID;
var defaultPage = 'home';
var RewriteBase = "http://www.ihmmanniversary.org/"; // if url is http://171.321.43.24/~foobar/ then the value should equal /~foobar/
var urlArray = window.location.pathname.replace(RewriteBase, '');
urlArray = urlArray.split("/");
var urlQueryString = "?" + Math.floor((Math.random()*10000)+1);
var defaultPageDir = "http://www.ihmmanniversary.org/wp-content/themes/ihmm";
var filesToVariablesArray = [
    {'text_input': 'views/input_text.php'},
    {'mainView': 'views/mainView.php'},
    {'page_section': 'views/page_section.php'},
    {'group_wrapper': 'views/output_group_wrapper.php'},
    {'output_event': 'views/output_event.php'},
    {'output_sponsor': 'views/output_sponsor.php'},
    {'output_speaker': 'views/output_speaker.php'},
    {'guest_post': 'views/output_guestpost.php'},
    {'contact_form': 'views/output_contact_form.php'},
    {'homevideo': 'views/output_homevideo.php'},
    {'homeintro': 'views/output_homeintro.php'},
    {'slide_page': 'views/output_slide_page.php'}
];
var pageOrder;
var pagesCollection;
var startUpRan = false;
var zIndexMax = 300;

// DOCUMENT READY
$(document).ready(function() {
    if(typeof $('body').data('tempdir') === "undefined"){
        pageDir = defaultPageDir;
    } else {
        pageDir = $('body').data('tempdir');
    }
    // loadFilesToVariables(filesToVariablesArray);
        var config = {
         kitId: 'kkw3dmu',
         scriptTimeout: 1000,
         loading: function() {
         // JavaScript to execute when fonts start loading
         },
         active: function() {
             loadFilesToVariables(filesToVariablesArray);
         },
         inactive: function() {
             loadFilesToVariables(filesToVariablesArray);
         }
        };
        var h=document.getElementsByTagName("html")[0];h.className+=" wf-loading";var t=setTimeout(function(){h.className=h.className.replace(/(\s|^)wf-loading(\s|$)/g," ");h.className+=" wf-inactive"},config.scriptTimeout);var tk=document.createElement("script"),d=false;tk.src='//use.typekit.net/'+config.kitId+'.js';tk.type="text/javascript";tk.async="true";tk.onload=tk.onreadystatechange=function(){var a=this.readyState;if(d||a&&a!="complete"&&a!="loaded")return;d=true;clearTimeout(t);try{Typekit.load(config)}catch(b){}};var s=document.getElementsByTagName("script")[0];s.parentNode.insertBefore(tk,s)
    });

// LOAD FILES TO VARIABLES
function loadFilesToVariables(fileArray, ignoreStartUp){
    fileTracker = {};
    $.each(fileArray, function(index, value){
        fileKey = Object.keys(fileArray[index]);
        fileValue = "/" + fileArray[index][Object.keys(fileArray[index])];
        fileType = fileValue.split(".").slice(-1)[0];
        fileTracker[fileKey] = {};
        fileTracker[fileKey]['fileType'] = fileType;
        fileTracker[fileKey]['fileKey'] = fileKey;

        switch(fileType){
            case "json":
                fileTracker[fileKey]['pageData'] = $.getJSON(pageDir + fileValue + urlQueryString, function() {});
            break;

            case "html":
                fileTracker[fileKey]['pageData'] = $.get(pageDir + fileValue + urlQueryString, function() {});
            break;

            case "php":
                fileTracker[fileKey]['pageData'] = $.get(pageDir + fileValue + urlQueryString, function() {});
            break;
        }

        fileTracker[fileKey]['pageData'].complete(function(data){
            thisURL = this.url.replace(defaultPageDir + "/", '');
            thisURL = thisURL.split("?");
            thisURL = thisURL[0];
            $.each(filesToVariablesArray, function(index1, value1){
                if(thisURL.indexOf(filesToVariablesArray[index1][Object.keys(filesToVariablesArray[index1])]) > -1){
                    window[thisURL.split(".")[1] + "_" + Object.keys(filesToVariablesArray[index1])] = data.responseText;
                    fileLoaded();
                }
            });
        });
    });
}

repTracker = 0;
function fileLoaded(){
    repTracker++;
    if(repTracker == filesToVariablesArray.length){
       startUp();
    }
}

// SET FRONT END OR BACK END MODE
function startUp(){
    setPageID();
    if(pageID == "admin"){
        loadDatePicker($('.date'));
        loadSortable($(".sortable"));
        if($('#people_sample_hidden_meta').size() != 0){
            $('#people_sample_hidden_meta').find('.hidden_meta').val('I was generated dynamically')
        }
        $('head').append('<link rel="stylesheet" id="jquery-style-css" href="' + pageDir + '/styles/admin-styles.min.css" type="text/css" media="all">');
    } else {
        // prependUrl = returnPrependUrl();
        // fixLinks();
        // $('#menu-main-menu').superfish({
        //  delay: 600,
        //  speed: 300
        // });
        // loadEvents("menuClicker");
        // loadEvents("logoClicker");
        // loadEvents("subNavClicker");

        // loadEvents("footerClicker");
        // $('#menu-main-menu-1').easyListSplitter({ colNumber: 2 });

        // loadView(pageID, postID);
        startTime = new Date().getTime();
        startUpRan = true;

        runApplication();
    }
}

// PAGE SPECIFIC FUNCTIONS
    function checkPagesCollection(){
        if((Object.keys(pagesCollection['pageData']).length) == pagesCollection.size){
            // first page gets special treatment
            if(pagesCollection.hideDefault){
                jsononArgs1={
                    idArray: []
                }
                pagesCollection['pageData'][defaultPage].attr('data-stellar-background-ratio', '0.07');
                pagesCollection['pageData'][defaultPage].css('z-index', zIndexMax--);
                $('.mainView').append(pagesCollection['pageData'][defaultPage]);
                
                returnPageData(defaultPage).done(function(data) {
                    // define object
                    var returnObject = $(php_page_section);

                    // build object
                    returnObject.find('.pageInfo h2').remove();
                    returnObject.find('.all-content').addClass(defaultPage);

                    // append home intro
                    returnIntro = $(php_homeintro);
                    imageURL = returnIntro.find('.logo-big img').attr('src');
                    returnIntro.find('.logo-big img').attr('src', defaultPageDir + imageURL);
                    returnIntro.find('.intro-content').html(_.unescape(data));
                    returnObject.find('.all-content').append(returnIntro)

                    // Append return object to DOM
                    $('#' + defaultPage).find('.container').html(returnObject);

                    // Initiate FlowType
                    $('#' + defaultPage).flowtype({
                        minFont : 28,
                        maxFont : 120
                    });
                });
            }
            // subsequent pages are then dealt with
            _.each(pageOrder, function(value, index){
                switch(index){
                    case "guestbook":
                        pagesCollection['pageData'][index].attr('data-stellar-background-ratio', Math.random())
                        pagesCollection['pageData'][index].css('z-index', zIndexMax--);
                        $('.mainView').append(pagesCollection['pageData'][index]);

                        returnJsonData('listGuestposts').done(function(guestData) {
                            // define object
                            var returnObject = $(php_page_section);

                            // build object
                            returnObject.find('.pageInfo h2').html(index);
                            returnObject.find('.all-content').addClass(index);
                            
                            returnGuestbook = $(php_group_wrapper);
                            returnGuestbook.find('h3').remove();

                            // loop guest posts
                            _.each(guestData, function(value, key) {
                                returnGuestPost = $(php_guest_post);

                                _.each(value, function(val, k) {
                                    switch(k) {
                                        default:
                                            returnGuestPost.find('.' + k).html(_.unescape(val));
                                            break;
                                    }
                                });
                                returnGuestbook.find('ul').append(returnGuestPost);
                            });

                            // append guestbook
                            returnObject.find('.all-content').append(returnGuestbook);

                            // append Homevideo
                            returnObject.find('.all-content').append(php_homevideo);

                            // Call jPlayer
                            initJplayer('#jquery_jplayer_1');

                            // Append return object to DOM
                            $('#' + index).find('.container').html(returnObject);

                            // Initiate FlowType
                            $('#' + index).flowtype({
                                minFont : 28,
                                maxFont : 36
                            });
                        });
                        break;
                    case "sponsors":
                        pagesCollection['pageData'][index].attr('data-stellar-background-ratio', Math.random())
                        pagesCollection['pageData'][index].css('z-index', zIndexMax--);
                        $('.mainView').append(pagesCollection['pageData'][index]);

                        returnJsonData('listSponsor_categories').done(function(data){
                            // define object
                            var returnObject = $(php_page_section);

                            // build object
                            returnObject.find('.pageInfo h2').html(index);
                            returnObject.find('.all-content').addClass(index);

                            returnJsonData('listSponsors').done(function(sponsorsData) {
                                // debug0 = sponsorsData;

                                // Loop Sponsor categories
                                _.each(data, function(value, key) {
                                    returnSponsorWrapper = $(php_group_wrapper)

                                    returnSponsorWrapper.addClass(slugify(value.the_title));
                                    returnSponsorWrapper.find('h3').html(value.the_title);

                                    // Loop sponsors
                                    _.each(sponsorsData, function(value1, key1) {
                                        returnSponsor = $(php_output_sponsor);

                                        _.each(value1, function(val, k) {
                                            switch(k) {
                                                case "featuredImage":
                                                    returnSponsor.find('.' + k).find('img').attr('src', val);
                                                    break;

                                                case "sponsor_website":
                                                    returnSponsor.find('.' + k).find('a').html('Visit Sponsor');
                                                    returnSponsor.find('.' + k).find('a').attr('href', val);
                                                    break;

                                                default:
                                                    returnSponsor.find('.' + k).html(val);
                                                    break;
                                            }
                                        });

                                        if (value.post_id == value1.sponsor_type) {
                                            returnSponsorWrapper.find('ul').append(returnSponsor);
                                        }

                                    });

                                    returnObject.find('.all-content').append(returnSponsorWrapper);
                                });
                            });

                            // Append return object to DOM
                            $('#' + index).find('.container').html(returnObject);

                            // Initiate FlowType
                            $('#' + index).flowtype({
                                minFont : 28,
                                maxFont : 36
                            });
                        });

                        break;

                    case "events":
                        pagesCollection['pageData'][index].attr('data-stellar-background-ratio', Math.random())
                        pagesCollection['pageData'][index].css('z-index', zIndexMax--);
                        $('.mainView').append(pagesCollection['pageData'][index]);

                        returnJsonData('listEvents').done(function(data){
                            // define object
                            var returnObject = $(php_page_section);

                            // build object
                            returnObject.find('.pageInfo h2').html(index);
                            returnObject.find('.all-content').addClass(index);

                            returnJsonData('listPeople').done(function(speakersData) {
                                // Loop events
                                _.each(data, function(value, key) {
                                    returnEvent = $(php_output_event);

                                    _.each(value, function(val, k) {
                                        switch(k) {
                                            case "start_date":
                                                startDate = moment.unix(val).format('MMM DD, YY : hh:mm A');
                                                returnEvent.find('.' + k).html(startDate);
                                                break
                                            case "end_date":
                                                endDate = moment.unix(val).format('MMM DD, YY : hh:mm A');
                                                returnEvent.find('.' + k).html(endDate);
                                                break
                                            case "speakers":
                                                _.each(val, function(speakerID) {
                                                    _.each(speakersData, function(l, m) {
                                                        if (l.post_id == speakerID) {
                                                            eventSpeaker = $('<li />');
                                                            eventSpeaker.append(l.first_name + ' ' + l. last_name);
                                                            returnEvent.find('.' + k).append(eventSpeaker);
                                                            // returnSpeaker = $(php_output_speaker);

                                                            // _.each(l, function(n, o) {
                                                            //     switch(o) {
                                                            //         case "featuredImage":
                                                            //             returnSpeaker.find('.' + o).find('img').attr('src', n);
                                                            //             break;
                                                            //         default:
                                                            //             returnSpeaker.find('.' + o).html(n);
                                                            //             break;
                                                            //     }
                                                            // });
                                                            // returnEvent.find('.' + k).append(returnSpeaker);
                                                        }
                                                    });
                                                });
                                                break;
                                            case "the_content":
                                                returnEvent.find('.' + k).html(_.unescape(val))
                                                break;
                                            default:
                                                returnEvent.find('.' + k).html(val);
                                                break;
                                        }
                                    });

                                    returnObject.find('.all-content').append(returnEvent);

                                });

                                // Build Form
                                formObject = $(php_contact_form);
                                formObject.attr('id', 'rsvpUsForm');

                                // Build select options
                                _.each(data, function(value, key) {
                                    formOption = $('<option />');
                                    formOption.val(value.the_title);
                                    formOption.html(value.the_title);
                                    formObject.find('.selected_event').append(formOption);
                                })

                                // append form
                                returnObject.find('.all-content').append(formObject);

                                // Validate with capcha
                                // Recaptcha.create("6LfGP-8SAAAAAIpRsYEJB_ILcRUPTuF7fQzd2jC7", 'contactCaptcha', {
                                //     tabindex: 1,
                                //     theme: "clean",
                                //     callback: Recaptcha.focus_response_field
                                // });

                                // Append return object to DOM
                                $('#' + index).find('.container').append(returnObject);

                                // Process form
                                processForm($('#rsvpUsForm'));

                                // Initiate FlowType
                                $('#' + index).flowtype({
                                    minFont : 28,
                                    maxFont : 36
                                });
                            });
                        });

                        break;

                    case "speakers":
                        pagesCollection['pageData'][index].attr('data-stellar-background-ratio', Math.random())
                        pagesCollection['pageData'][index].css('z-index', zIndexMax--);
                        $('.mainView').append(pagesCollection['pageData'][index]);

                        returnJsonData('listPeople_category').done(function(data) {
                            // define object
                            var returnObject = $(php_page_section);

                            // build object
                            returnObject.find('.pageInfo h2').html(index);
                            returnObject.find('.all-content').addClass(index);

                            returnJsonData('listPeople').done(function(peopleData) {

                                // loop speaker types
                                _.each(data, function(value, key) {
                                    returnSpeakersWrapper = $(php_group_wrapper);

                                    returnSpeakersWrapper.addClass(slugify(value.the_title));
                                    returnSpeakersWrapper.find('h3').html(value.the_title);

                                    // Loop speakers
                                    _.each(peopleData, function(value1, key1) {
                                        returnSpeaker = $(php_output_speaker);

                                        _.each(value1, function(val, k) {
                                            switch(k) {
                                                case "featuredImage":
                                                    returnSpeaker.find('.' + k).find('img').attr('src', val);
                                                    break;
                                                default:
                                                    returnSpeaker.find('.' + k).html(val);
                                                    break;
                                            }
                                        });

                                        if (value.post_id == value1.speaker_category) {
                                            returnSpeakersWrapper.find('ul').append(returnSpeaker);
                                            returnObject.find('.all-content').append(returnSpeakersWrapper);
                                        }
                                    });

                                });

                            });

                            // Append return object to DOM
                            $('#' + index).find('.container').html(returnObject);

                            // Initiate FlowType
                            $('#' + index).flowtype({
                                minFont : 28,
                                maxFont : 36
                            });
                        });
                        break;

                    case "contact":
                        pagesCollection['pageData'][index].attr('data-stellar-background-ratio', Math.random())
                        pagesCollection['pageData'][index].css('z-index', zIndexMax--);
                        $('.mainView').append(pagesCollection['pageData'][index]);

                        returnPageData(index).done(function(data) {
                            // define object
                            var returnObject = $(php_page_section);

                            // build object
                            returnObject.find('.pageInfo h2').html(index);
                            returnObject.find('.all-content').addClass(index);

                            formObject = $(php_contact_form);
                            formObject.attr('id', 'contactUsForm');
                            formObject.find('.selected_event').parent().parent().remove();
                            returnObject.find('.all-content').html(formObject);

                            // Append returned object to DOM
                            $('#' + index).find('.container').html(returnObject);

                            // Validate with capcha
                            // Recaptcha.create("6LfGP-8SAAAAAIpRsYEJB_ILcRUPTuF7fQzd2jC7", 'contactCaptcha', {
                            //     tabindex: 1,
                            //     theme: "clean",
                            //     callback: Recaptcha.focus_response_field
                            // });

                            // Process form
                            processForm($('#contactUsForm'));

                            // Initiate FlowType
                            $('#' + index).flowtype({
                                minFont : 28,
                                maxFont : 36
                            });
                        });

                        break;

                    default:
                        // pagesCollection['pageData'][index].attr('data-stellar-background-ratio', Math.random())
                        // pagesCollection['pageData'][index].css('z-index', zIndexMax--);
                        // $('.mainView').append(pagesCollection['pageData'][index]);

                        // returnJsonData('listPeople').done(function(data){
                        //     // define object
                        //     var returnObject = $(php_page_section);

                        //     // build object
                        //     returnObject.find('.pageInfo h2').html(index);
                        //     returnObject.find('.all-content').addClass(index);

                        //     // Do Work here

                        //     // Append return object to DOM
                        //     $('#' + index).find('.container').html(returnObject);

                        //     // Initiate FlowType
                        //     $('#' + index).flowtype({
                        //         minFont : 28,
                        //         maxFont : 36
                        //     });
                        // });
                        break;
                }
            })
            $(window).stellar();
            if(urlArray[0] != ""){
                if(!(urlArray[0] == defaultPage) && (window.pageYOffset == 0)){
                    goToByScroll(urlArray[0]);
                }
            }
        }
    }

// RETURN JSON DATA
    function returnJsonData(jsonRequest, args){
        if(typeof args !== 'undefined'){
            args['idArray'] = (typeof args['idArray'] === "undefined") ? null : args['idArray'];
        } else {
            args = {};
            args['idArray'] = null
        }
        returnedJsonData = $.post(pageDir + "/machines/handlers/loadJSON.php", { jsonRequest: jsonRequest, args: args }, function() {}, 'json');
        return returnedJsonData;
    }

// SET PAGE ID
    function setPageID(pageIDrequest){
        if($('body').hasClass("wp-admin")){
            pageID = "admin";
        } else {
            pageID = defaultPage;
            _.each(json_pages, function(value, index){
                if(urlArray[0] == value.pageID){
                    pageID = urlArray[0];
                }
            });
        }
    }

// RETURN PAGE DATA
    function returnPageData(pageRequest){
        $.each(json_pages, function(index, value){
            if(pageRequest == value.pageID){
                returnedPageData = $.post(pageDir + "/machines/handlers/loadPage.php", { pageID: value.wp_page_id}, function() {});
            }
        });
        return returnedPageData;
    }

// SCROLL TO PAGE
    function goToByScroll(dataslide) {
        $('html,body').animate({
            scrollTop: $('.slide[data-slide="' + dataslide + '"]').offset().top,
            complete: scrollEnded()
        }, 2000, 'easeInOutQuint');
    }

// PAGE SCROLL CALLBACK
    function scrollEnded(){
    }

// TURN SLUG INTO STRING
    function slugify(text){
        return text.toString().toLowerCase()
        .replace(/\+/g, '')           // Replace spaces with 
        .replace(/\s+/g, '-')           // Replace spaces with -
        .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
        .replace(/\-\-+/g, '-')         // Replace multiple - with single -
        .replace(/^-+/, '')             // Trim - from start of text
        .replace(/-+$/, '');            // Trim - from end of text
    }

// HISTORY PUSH STATE
    function pushHistory(){
        if (Modernizr.history){
            prependPushStateUrl = returnPrependUrl();
            var postIDurl = "";
            newPage = prependPushStateUrl + pageID + "/" + postIDurl;
            var stateObj = { pageID: newPage};
            history.pushState(stateObj, null, newPage);
        } 
    }

// HISTORY POP STATE
    $(window).on('popstate',function(e){
        if(startUpRan){
            setURLarray();
            popPage = (urlArray[0] == "") ? defaultPage : urlArray[0];
            setPageID(popPage);
            goToByScroll(popPage);
        }
    });

// GET WINDOW'S URL AND SET ARRAY
    function setURLarray(){
        tempUrlArray = window.location.pathname.replace(RewriteBase, '');
        urlArray = tempUrlArray.split("/");
    }

// RETURN BUILT URL PREPEND STRING
    function returnPrependUrl(){
        pageLevel = window.location.pathname.replace(RewriteBase, "").split("/");
        if(pageLevel[pageLevel.length-1] == ""){
            pageLevel.pop();
        }
        prependUrl = "";
        for (var i = 0; i < pageLevel.length; i++) {
            prependUrl += "../";
        };
        return prependUrl;
    }

// RUN APPLICATION
    function runApplication(){
        mainViewObject = $(php_mainView);
        pageOrder = {};
        pagesCollection = {
            size: 0,
            pageData: {},
            hideDefault: true
        };
        mainViewObject.find('.menu-item').each(function(index){
            // if((pagesCollection.hideDefault) && (slugify($(this).children('a').text()) == defaultPage)){
            //     $(this).remove();
            // } else {
            //     pageOrder[slugify($(this).children('a').text())] = index;
            //     $(this).children('a').attr('href', slugify($(this).children('a').text()));
            //     $(this).children('a').data('slide', slugify($(this).children('a').text()));
            // }
            if ( slugify($(this).children('a').text()) == defaultPage ) {
                $(this).remove();
            }
            pageOrder[slugify($(this).children('a').text())] = index;
            $(this).children('a').attr('href', slugify($(this).children('a').text()));
            $(this).children('a').data('slide', slugify($(this).children('a').text()));
            pagesCollection.size++
        });
        mainViewObject.find('.logoLink').attr('href', defaultPage);
        mainViewObject.find('.logoLink').data('slide', defaultPage);


        $('section').append(mainViewObject);

        $('.menu-main-menu-container, #logo').on('click', 'a', function(e){
            e.preventDefault();
            goToByScroll($(this).data('slide'));
            pageID = $(this).data('slide');
            pushHistory();
        });

        $('.menuButton').on('click', function(){
            if($('.headerMenu').hasClass('showMenu')){
                $('.headerMenu').removeClass('showMenu');
            } else {
                $('.headerMenu').addClass('showMenu');
            }
        });

        prevPage = "";
        _.each(json_pages, function(value, index1){
            json_pages[index1]['pageOrder'] = pageOrder[value.pageID];
            returnPageData(value.pageID).done(function(data){
                        // POPULATE WITH PAGE DATA
                        if(value.pageID != "auto-draft"){
                            returnObject = $(php_slide_page);
                            returnObject.attr('id', value.pageID);
                            returnObject.attr('data-slide', value.pageID);
                            // returnObject.find('.container').append(data);
                            pagesCollection['pageData'][value.pageID] = returnObject;
                            checkPagesCollection();

                        }
                    });
        });


    }

// PROCESS FORM
function processForm(theForm){
    theForm.validate({
        errorClass: "formError",
        submitHandler: function(thisForm) {
            formData = theForm.serialize();
            postArgs = {
                challenge: $('#recaptcha_challenge_field').val(),
                response: $('#recaptcha_response_field').val()              
            }

//            $.post(pageDir + "/machines/libraries/recaptcha/recaptchaResponse.php", postArgs, function(data){
                if(1 == 1){
//                if(data.substring(0,4) == "true"){

                    $.post(pageDir + "/machines/handlers/contactForm.php", { formData: formData }, function(data) {
                        if(data == "success"){

                            $(".formResponse").html("Form sent!");
                            $(".formResponse").css('color', 'green');
                            $('#contactCaptcha').find('.fieldResponse').html('');
                        } else {
                            $(".formResponse").html("Form not sent. Please refresh the page and try again");
                            $(".formResponse").css('color', 'red');
                        }
                        
                    });

                } else {
                    Recaptcha.reload()
                    returnObject = $('<div class="fieldResponse">Incorrect Captcha, please try again</div>');
                    $('#contactCaptcha').append(returnObject)
                }
//            })

        },
        invalidHandler: function(event, validator) {
            errors = validator.numberOfInvalids();
            if (errors) {
                message = errors == 1 ? 'You missed 1 field.' : 'You missed ' + errors + ' fields.';
                $(".formResponse").html(message);
                $(".formResponse").show();
            } else {
                $(".formResponse").hide();
            }
        },
        errorPlacement: function(error, element) {
            switch(element.attr("name")){
                case "spam":
                    $("input[name='spam']").parent().after("<div class='fieldResponse'>" + error[0].outerHTML + "</div>");
                break;
                case "transportation":
                    $("input[name='transportation']").parent().after("<div class='fieldResponse'>" + error[0].outerHTML + "</div>");
                break;
                default:
                    element.parent().after("<div class='fieldResponse'>" + error[0].outerHTML + "</div>");
            }
        },
        messages: {
            email: {
                email: "Your email address must be in the format of name@domain.com"
            },
            telephone: {
                phoneUS: "Your phone number must be in the format of 212-555-1000"
            },
        },
        rules: {
            email: {
                required: true,
                email: true
            },
            primary_contact_name:{
                required: true
            },
            company:{
                required: true
            },
            telephone:{
                required: true,
                phoneUS: true
            },
            company:{
                required: true
            },
            address:{
                required: true
            },
            selected_event: {
                required: true
            }
        }
    });
}

// jPlayer
function initJplayer(target) {
    $(target).jPlayer({
        ready: function () {
            $(this).jPlayer("setMedia", {
                m4v: "http://www.jplayer.org/video/m4v/Big_Buck_Bunny_Trailer.m4v",
                ogv: "http://www.jplayer.org/video/ogv/Big_Buck_Bunny_Trailer.ogv",
                webmv: "http://www.jplayer.org/video/webm/Big_Buck_Bunny_Trailer.webm",
                poster: "http://www.jplayer.org/video/poster/Big_Buck_Bunny_Trailer_480x270.png"
            });
        },
        swfPath: defaultPageDir + "/machines/libraries/jplayer/",
        supplied: "webmv, ogv, m4v",
        size: {
            width: "100%",
            height: "100%",
            cssClass: "jp-video-360p"
        },
        smoothPlayBar: true,
        keyEnabled: true
    });
}

// ADMIN
    // LOAD JQUERY UI DATE PICKER
    function loadDatePicker(target, changeCallback){
        hiddenDate = target.siblings('input');
        target.datetimepicker();
        if(hiddenDate.val() == ""){
            var myDate = new Date();
            var prettyDate =(myDate.getMonth()+1) + '/' + myDate.getDate() + '/' + myDate.getFullYear() + " " + myDate.getHours() + ":" + myDate.getMinutes();
            target.val(prettyDate);
            hiddenDate.val(Date.parse(prettyDate)/1000);
        }
        target.change(function() {
            $(this).siblings('input').val(Date.parse($(this).val())/1000);
            dateArray = [{event_start: $('input[name="event_start"]').val(), event_end: $('input[name="event_end"]').val()}];
            $('#event_date_array_meta').find('.hidden_meta').val(JSON.stringify(dateArray));
            if(changeCallback){
                updateRepeatConfig();
            }
        });
    }

    // LOAD JQUERY UI SORTABLE
        function loadSortable(target){
            target.sortable();
            target.disableSelection();
            target.on( "sortstop", function( event, ui ) {
                sortData = {};
                $(this).children('li').each(function(){
                    sortData[$(this).data('id')] = $(this).index();
                });
                var data = {
                    action: 'update_sort',
                    sort_data: sortData
                };
                $.post(ajaxurl, data, function(response) {
                    console.log('Got this from the server: ' + response);
                });                 
            });
        }

    // ADD CONSOLE SUPPORT TO IE8
        var method;
        var noop = function () {};
        var methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeStamp', 'trace', 'warn'
        ];
        var length = methods.length;
        var console = (window.console = window.console || {});
        while (length--) {
            method = methods[length];
            if (!console[method]) {
                console[method] = noop;
            }
        }


    // ADD OBJECT.KEYS SUPPORT TO IE8
        if (!Object.keys) {
            Object.keys = (function () {
                'use strict';
                var hasOwnProperty = Object.prototype.hasOwnProperty,
                hasDontEnumBug = !({toString: null}).propertyIsEnumerable('toString'),
                dontEnums = [
                'toString',
                'toLocaleString',
                'valueOf',
                'hasOwnProperty',
                'isPrototypeOf',
                'propertyIsEnumerable',
                'constructor'
                ],
                dontEnumsLength = dontEnums.length;
                return function (obj) {
                    if (typeof obj !== 'object' && (typeof obj !== 'function' || obj === null)) {
                        throw new TypeError('Object.keys called on non-object');
                    }
                    var result = [], prop, i;
                    for (prop in obj) {
                        if (hasOwnProperty.call(obj, prop)) {
                            result.push(prop);
                        }
                    }
                    if (hasDontEnumBug) {
                        for (i = 0; i < dontEnumsLength; i++) {
                            if (hasOwnProperty.call(obj, dontEnums[i])) {
                                result.push(dontEnums[i]);
                            }
                        }
                    }
                    return result;
                };
            }());
        }
