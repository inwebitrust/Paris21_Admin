var Utils = Backbone.View.extend({

    initialize:function(){
        var self = this;

        this.newExtractionTable = "";

        this.extractionStartYear = 2004;

        var d = new Date();
        this.extractionLastYear = d.getFullYear() - 1;

        this.indicators = {};
        this.geography = {};
        this.extractions = {};
    },

    gatherData:function(callback){
        var self = this;
        this.gatherIndicators(function(){
            self.gatherGeo(function () {
                self.gatherExtractions(function () {
                    return callback();
                })
            })
        });
    },

    gatherIndicators: function (callback) {
        var self = this;
        $.getJSON("API/getIndicators.php", function(data){
            console.log("getIndicators", data);
            self.indicators = data.indicators;
            return callback();
        })
    },

    gatherGeo: function (callback) {
        var self = this;
        $.getJSON("API/getGeography.php", function(data){
            console.log("getGeography", data);
            self.geography = data.geography;
            return callback();
        })
    },

    gatherExtractions: function (callback) {
        var self = this;
        $.getJSON("API/getExtractions.php", function(data){
            console.log("getExtractions", data);
            self.extractions = data.extractions;
            return callback();
        })
    },

    createExtraction: function (datasource, callback) {
        $.post( "API/insertExtraction.php", { source: datasource })
          .done(function( newTableID ) {
            console.log("insert done", newTableID)
            App.utils.newExtractionTable = "datavalues_"+datasource+"_"+newTableID;
            return callback();
          });
    },

    insertDatavalues: function (objToInsert) {
        if(this.newExtractionTable !== "") {
            $.post( "API/insertDatavalues.php", { objToInsert: objToInsert, sqltable:this.newExtractionTable })
              .done(function( data ) {
                console.log("insert datavalues done")
              });
        }
    },

    insertDatavaluesBy100: function (allObjsToInsert) {
        if(this.newExtractionTable !== "") {
            $.post( "API/insertDatavaluesBy100.php", { allObjsToInsert: allObjsToInsert, sqltable:this.newExtractionTable })
              .done(function( data ) {
                console.log("insert datavalues done")
              });
        }
    },

    activateExtraction: function (datasource, activeID, callback) {
        $.post( "API/activateExtraction.php", { source:datasource, activeID: activeID })
          .done(function( data ) {
            console.log("activate extraction done", data)
            return callback();
          });
    },

    deleteExtraction: function (datasource, extractionID, callback) {
        $.post( "API/deleteExtraction.php", { source:datasource, extractionID: extractionID })
          .done(function( data ) {
            console.log("delete extraction done", data)
            return callback();
          });
    },

    computeRegionalValues: function (callback) {
        $.post( "API/computeRegionalValues.php")
          .done(function( data ) {
            console.log("done computeRegionalValues")
            return callback();
          });
    },

    generateCSVFile: function (callback) {
        $.post( "API/generateCSVFile.php")
          .done(function( data ) {
            console.log("done generateCSVFile")
            return callback();
          });
    },

    insertCSVFile: function (csvFile, callback) {
        var self = this;
        
        if(csvFile !== "") {
            console.log("insertCSVFile", csvFile)
            $.post( "API/insertCSVFile.php", { csvFile: csvFile, table:self.newExtractionTable })
              .done(function( data ) {
                console.log("insertCSVFile done")
              });
        }
    }
});

module.exports = Utils;