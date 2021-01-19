var AppRouter = Backbone.Router.extend({

    routes: {
        "":"routeFromURL",
        ":datasource":"routeWithParams",
    },

    routeFromURL:function(params){ 
        App.updateFromRouter();
    },

    routeWithParams:function(datasource){
      App.selectedDatasource = datasource;
      App.updateFromRouter();
    },

    
    updateRoute:function(triggerize){
        this.navigate( App.selectedDatasource, {trigger: triggerize});
    },

});

module.exports = AppRouter;