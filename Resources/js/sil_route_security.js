const SilRouteSecurity = {
    allSecuredRoutesWithCurrentUserIsAccess: {},

    /**
     * @param {String} route
     * @param {Boolean} hasAccess
     * @return {Object} SilRouteSecurity
     */
    addSecuredRoutes: function(route, hasAccess) {
        this.allSecuredRoutesWithCurrentUserIsAccess[route] = hasAccess
    },

    /**
     * @param {String} route
     * @return {Boolean}
     */
    hasUserAccessToRoute: function(route) {
        return this.allSecuredRoutesWithCurrentUserIsAccess.hasOwnProperty(route)
            ? this.allSecuredRoutesWithCurrentUserIsAccess[route]
            : true
    },

    /**
     * @param {Array} route
     * @return {Boolean}
     */
    hasUserAccessAtLeastOneRoute: function(routes) {
        for(let x = 0; x < routes.length; x++) {
            if (this.hasUserAccessToRoute(routes[x])) {
                return true
            }
        }

        return false
    },

    /**
     * @param {Array} route
     * @return {Boolean}
     */
    hasUserAccessToRoutes: function(routes) {
        for(let x = 0; x < routes.length; x++) {
            if (!this.hasUserAccessToRoute(routes[x])) {
                return false
            }
        }

        return true
    }
}
