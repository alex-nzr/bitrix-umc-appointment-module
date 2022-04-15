'use strict';

BX.ready(function () {
    const fBitAdmin = BX.namespace("FirstBit.Appointment.Admin");

    fBitAdmin.deleteRecord = function (id, gridId, orderUid) {
        this.runAction(id, gridId, orderUid, 'deleteOrder')
    }

    fBitAdmin.updateRecord = function (id, gridId, orderUid) {
        this.runAction(id, gridId, orderUid, 'getOrderStatus')
    }

    fBitAdmin.runAction = function (id, gridId, orderUid, actionToCall) {
        const grid = BX.Main.gridManager.getInstanceById(gridId);
        grid && grid.tableFade();

        const ajaxUrl = '/bitrix/services/main/ajax.php';
        const action = `firstbit:appointment.oneCController.${actionToCall}`;

        const formData = new FormData();
        formData.set('id', id);
        formData.set('orderUid', orderUid);
        formData.set('sessid', BX.bitrix_sessid());

        this.requestParams = {
            method: 'POST',
            body: formData,
        }

        fetch(`${ajaxUrl}?action=${action}`, this.requestParams)
            .then(response => {
                if (response.ok) {
                    return response.json();
                }else{
                    console.log(`Error. Status code ${response.status}`);
                }
            })
            .then(json => void(0)/*console.log(json)*/)
            .catch(e => console.log(e))
            .finally(() => (grid && grid.reloadTable()))
    }
});