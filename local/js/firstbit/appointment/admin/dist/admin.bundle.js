'use strict';

BX.ready(function () {
    const fbAdmin = BX.namespace("FirstBit.Appointment.Admin");

    fbAdmin.deleteRecord = function (id, gridId) {
        console.log(`deleteRecord ${id}`);
        this.reloadGrid(gridId)
    }

    fbAdmin.updateRecord = function (id, gridId) {
        console.log(`updateRecord ${id}`);
        this.reloadGrid(gridId)
    }

    fbAdmin.reloadGrid = function (gridId) {
        const grid = BX.Main.gridManager.getInstanceById(gridId);
        if (grid) {
            grid.reloadTable();
        }
    }
});