var App = {
    $body:$("body"),

    init: function init() {
        this.$App = $("#App");
        this.$tableBody = $(".table_body");
        this.$barFilling = $(".bar_filling");
        this.$loadingbarNB = $(".modal_loadingbar_nb");
        this.$UploadCSVBt = $("#UploadCSVBt");
        this.$LaunchCSVBt = $("#LaunchCSVBt");
        this.$UploadIndicatorsBt = $("#UploadIndicatorsBt");
        this.$LaunchIndicatorsBt = $("#LaunchIndicatorsBt");

        this.$myIframe = $("#my_iframe");
        this.$myIframeIndicators = $("#my_iframe_indicators");
        this.notFoundCountries = [];

        this.deletingRowID = "";
        this.localCSVFilePath = "";
        this.localIndicatorsFilePath = "";

        this.indicator = "";
        this.selectedDatasource = "worldbank";

        var Utils = require("utils");
        this.utils = new Utils();

        var HeaderView = require("views/headerView");
        this.headerView = new HeaderView();

        var FooterView = require("views/footerView");
        this.footerView = new FooterView();

        var Router = require("router");
        this.router = new Router();
        Backbone.history.start();
    },

    updateFromRouter:function(){
        var self = this;
        this.utils.gatherData(function(){
            self.render();
        });
    },

    render:function(){
        console.log("render");
    	this.headerView.render();
        this.footerView.render();
    	this.bindEvents();

        this.updateApp();
    },

    updateApp: function () {
        this.$App.attr("data-source", this.selectedDatasource);
        $(".source_item").removeClass("selected");
        $(".source_item[data-source='"+this.selectedDatasource+"']").addClass("selected");
        $("#UploadBlock #source").val(this.selectedDatasource);

        if(this.localCSVFilePath == "") {
            this.$LaunchCSVBt.removeClass("enabled");
        } else {
            this.$LaunchCSVBt.addClass("enabled");
        }

        if(this.localIndicatorsFilePath == "") {
            this.$LaunchIndicatorsBt.removeClass("enabled");
        } else {
            this.$LaunchIndicatorsBt.addClass("enabled");
        }

        this.updateTable();

        this.router.updateRoute();
    },

    updateTable: function () {
        var self = this;

        this.sourceExtractions = _.filter(App.utils.extractions, function (ex){
            return ex.source == self.selectedDatasource
        });

        self.$tableBody.html("");
        _.each(this.sourceExtractions, function (exData) {

            var htmlDelete = "<div class='cell' data-cell='delete'></div>";
            if(exData.active !== "1") htmlDelete = "<div class='cell' data-cell='delete'><a class='row_deletebt' data-id='"+exData.id+"'></a></div>";

            var htmlDownload = "";
            if(self.selectedDatasource == "csv") htmlDownload = "<div class='cell' data-cell='download'><a class='row_downloadbt' data-id='"+exData.id+"' href='API/downloadCSVTable.php?id="+exData.id+"&source="+self.selectedDatasource+"' target='_blank'></a></div>";

            var $extractionRow = $("<div class='extraction_row' data-active='"+exData.active+"'><div class='cell' data-cell='extraction'>"+exData.date+"</div><div class='cell cell_active' data-cell='active'><div class='active_radiobt' data-id='"+exData.id+"' data-active='"+exData.active+"'></div></div>"+htmlDelete+""+htmlDownload+"</div>");
            self.$tableBody.append($extractionRow);
        });
    },

    bindEvents:function(){
        var self = this;

        console.log("bindEvents ???");

        $("#ExtractionBt").on("click", function (){
            self.launchExtraction();
        })

        $(".source_item").on("click", function (){
            self.selectedDatasource = $(this).attr("data-source");
            self.updateApp();
        });

        this.$UploadCSVBt.on("change", function (){
            self.localCSVFilePath = $(this).val();
            $("#filename").html(self.localCSVFilePath);
            $("#UploadBlock .my_form_line").addClass("displayed");
            setTimeout(function(){
                self.updateApp();
            }, 10);
        });

        this.$UploadIndicatorsBt.on("change", function (){
            self.localIndicatorsFilePath = $(this).val();
            $("#filenameIndicators").html(self.localIndicatorsFilePath);
            $("#UploadBlockIndicators .my_form_line").addClass("displayed");
            setTimeout(function(){
                self.updateApp();
            }, 10);
        });

        this.$LaunchCSVBt.on("click", function (){
            document.getElementById('my_form').target = 'my_iframe';
            document.getElementById('my_form').submit();
            self.$App.attr("data-uploadingcsv", "true");

            self.CSVUploadingTest();
        });

        this.$LaunchIndicatorsBt.on("click", function (){
            document.getElementById('my_form_codebook').target = 'my_iframe_indicators';
            document.getElementById('my_form_codebook').submit();
            self.$App.attr("data-uploadingindicators", "true");

            self.CSVUploadingIndicatorsTest();
        });
        

        this.$tableBody.on("click", ".row_deletebt", function (){
            self.deletingRowID = $(this).attr("data-id");
            self.$App.attr("data-deleting", "true");
        });

        this.$tableBody.on("click", ".active_radiobt", function (){
            self.activeID = $(this).attr("data-id");
            App.utils.activateExtraction(App.selectedDatasource, self.activeID, function(){
                self.$App.attr("data-computingregions", "true");
                App.utils.computeRegionalValues(function (){
                    App.utils.generateCSVFile(function (){
                        App.utils.gatherExtractions(function (){
                            self.$App.attr("data-computingregions", "false");
                            App.updateApp();
                        });
                    })
                })
            })
        });

        $(".modal_bt[data-bt='no']").on("click", function (){
            self.$App.attr("data-deleting", "false");
        })

        $(".modal_bt[data-bt='yes']").on("click", function (){
            App.utils.deleteExtraction(App.selectedDatasource, App.deletingRowID, function(){
                App.utils.gatherExtractions(function (){
                    App.updateApp();
                });
                App.$App.attr("data-deleting", "false");
            })
        })
    },

    //EXTRACTION
    launchExtraction: function () {

        this.sourceIndicators = _.filter(App.utils.indicators, function (indicator) {
            return indicator.datasource == "worldbank";
        });
        //this.sourceIndicators = this.sourceIndicators.slice(0,1);

        this.createExtraction();
        
    },

    createExtraction: function () {
        var self = this;

        this.completedExtraction = false;
        this.currentExtractionIndicatorInc = 0;
        this.currentExtractionPage = 1;

        this.$App.attr("data-extracting", "true");

        App.utils.createExtraction(this.selectedDatasource ,function () {
            if(self.selectedDatasource == "worldbank"){
                self.performIndicatorExtraction()
            }
        });
    },

    performIndicatorExtraction: function () {
        var self = this;

        var fillingPct = ((this.currentExtractionIndicatorInc + 1) / this.sourceIndicators.length) * 100;
        this.$barFilling.css("width", fillingPct + "%");
        this.$loadingbarNB.html( (this.currentExtractionIndicatorInc + 1) + "/" + this.sourceIndicators.length);

        var indicatorData = this.sourceIndicators[this.currentExtractionIndicatorInc];

        if(this.selectedDatasource == "worldbank") {
            $.getJSON("https://api.worldbank.org/v2/country/all/indicator/"+indicatorData.datasource_id+"?date="+App.utils.extractionStartYear+":"+App.utils.extractionLastYear+"&page="+this.currentExtractionPage+"&per_page=1000&format=json", function(jsonData){
                var metadata = jsonData[0];
                var indicatorValues = jsonData[1];
                var allObjsToInsert = [];

                _.each(indicatorValues, function (objValues) {
                    var foundCountry = _.find(App.utils.geography, function (geo) {
                        return geo.iso == objValues.countryiso3code;
                    });

                    if(foundCountry !== undefined) {
                        var objToInsert = {
                            m49: foundCountry.m49,
                            iso: objValues.countryiso3code,
                            year: objValues.date,
                            indicator_id: indicatorData.id,
                            value: objValues.value
                        }
                        allObjsToInsert.push(objToInsert);
                    } else {
                        if(_.indexOf(App.notFoundCountries, objValues.countryiso3code) == -1) {
                            console.log("foundCountry", objValues.countryiso3code, objValues);
                            App.notFoundCountries.push(objValues.countryiso3code)
                        }
                    }

                    if(allObjsToInsert.length >= 100) {
                        App.utils.insertDatavaluesBy100(allObjsToInsert);
                        allObjsToInsert = [];
                    }
                });

                App.utils.insertDatavaluesBy100(allObjsToInsert)

                if(metadata.page < metadata.pages) {
                    self.currentExtractionPage += 1;
                    self.performIndicatorExtraction();
                } else {
                    self.currentExtractionIndicatorInc += 1;
                    self.currentExtractionPage = 1;
                    if(self.sourceIndicators[self.currentExtractionIndicatorInc] === undefined) {
                        self.completedExtraction = true;
                        self.finishExtraction();
                    } else {
                        self.performIndicatorExtraction();
                    }
                }
            }).fail(function (){
                self.performIndicatorExtraction();
                console.log("error handling ?");
            })
        }
    },

    finishExtraction: function () {
        var self = this;
        self.$App.attr("data-extracting", "false");
        self.utils.gatherExtractions(function (){
            App.updateApp();
        });
    },

    CSVUploadingTest: function () {
        console.log("CSVUploadingTest");
        if(this.$myIframe.contents().find("body").html().length > 10) {
            this.$App.attr("data-uploadingcsv", "false");
            this.$UploadCSVBt.val("");
            this.localCSVFilePath = "";
            $("#filename").html("");
            $("#UploadBlock .my_form_line").removeClass("displayed");
            this.$myIframe.contents().find("body").html('');
            this.finishExtraction();
        } else {
            setTimeout(function (){
                App.CSVUploadingTest();
            }, 1000);
        }
    },

    CSVUploadingIndicatorsTest: function () {
        console.log("CSVUploadingIndicatorsTest");
        if(this.$myIframeIndicators.contents().find("body").html().length > 10) {
            this.$App.attr("data-uploadingindicators", "false");
            this.$UploadIndicatorsBt.val("");
            this.localIndicatorsFilePath = "";
            $("#filenameIndicators").html("");
            $("#UploadBlockIndicators .my_form_line").removeClass("displayed");
            this.$myIframeIndicators.contents().find("body").html('');
            this.finishExtraction();
        } else {
            setTimeout(function (){
                App.CSVUploadingIndicatorsTest();
            }, 1000);
        }
    }

};

module.exports = App;
window.App = App;