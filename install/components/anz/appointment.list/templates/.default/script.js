class AppointmentList
{
    constructor()
    {
        this.ajaxUrl = '/bitrix/services/main/ajax.php';
        this.controller = 'anz:appointment.Appointment';
        this.requestParams = {
            method: 'POST',
                body: '',
        }
    }

    deleteAppointment (id, gridId, appointmentUid) {
        this.runAction(id, gridId, appointmentUid, 'deleteAppointment')
    }

    updateAppointmentStatus (id, gridId, appointmentUid) {
        this.runAction(id, gridId, appointmentUid, 'updateAppointmentStatus')
    }

    runAction(id, gridId, uid, actionToCall) {
        const grid = BX.Main.gridManager.getInstanceById(gridId);
        grid && grid.tableFade();

        const action = `${this.controller}.${actionToCall}`;

        this.requestParams.body = this.createFormData({id, uid});

        fetch(`${this.ajaxUrl}?action=${action}`, this.requestParams)
            .then(response => {
                if (response.ok) {
                    return response.json();
                }else{
                    console.log(`Error. Status code ${response.status}`);
                }
            })
            .then(json => {
                if (json.status === 'error'){
                    //console.log(json);
                }
            })
            .catch(e => console.log(e))
            .finally(() => {
                if (grid)
                {
                    const reloadParams = { apply_filter: 'Y', clear_nav: 'N' };
                    const pageParams = {[gridId]: `page-${this.getGridCurrentPage(grid)}`};
                    grid.baseUrl = BX.Grid.Utils.addUrlParams(grid.baseUrl, pageParams);
                    grid.reloadTable('POST', reloadParams);
                }
            })
    }

    createFormData(argsObject) {
        const formData = new FormData();

        for (let key in argsObject)
        {
            if (argsObject.hasOwnProperty(key))
            {
                formData.set(key, argsObject[key]);
            }
        }
        formData.set('sessid', BX.bitrix_sessid());

        return formData;
    }

    getGridCurrentPage(gridInstance) {
        let curPage = 0;
        if (BX.type.isDomNode(gridInstance?.data?.pagination))
        {
            const curPageNode = gridInstance.data.pagination.querySelector('.main-ui-pagination-active');
            if (curPageNode){
                curPage = !isNaN(parseInt(curPageNode.textContent)) ? parseInt(curPageNode.textContent) : 0;
            }
        }
        return curPage;
    }
}