'use strict';

BX.ready(function () {
    const fBitAdmin = BX.namespace("FirstBit.Appointment.Admin");

    fBitAdmin.deleteRecord = function (id, gridId) {
        console.log(`deleteRecord ${id}`);
        this.reloadGrid(gridId)
    }

    fBitAdmin.updateRecord = function (id, gridId) {
        console.log(`updateRecord ${id}`);
        this.reloadGrid(gridId)
    }

    fBitAdmin.reloadGrid = function (gridId) {
        const grid = BX.Main.gridManager.getInstanceById(gridId);
        if (grid) {
            grid.reloadTable();
        }
    }
});