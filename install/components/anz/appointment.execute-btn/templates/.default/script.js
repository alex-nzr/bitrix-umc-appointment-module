
class ExecuteBtn
{
    constructor(buttonId, nextDateInputName, successMess)
    {
        const executeBtn = document.getElementById(buttonId);
        const resultNode = executeBtn?.closest("div")?.querySelector(`span`);
        executeBtn?.addEventListener(`click`, () => {
            executeBtn.setAttribute(`disabled`, `true`);
            BX.ajax.runAction(`anz:appointment.oneCController.loadData`, {
                data: {
                    sessid: BX.bitrix_sessid(),
                }
            })
                .then(response => {
                    if (response.status === `success`)
                    {
                        const date = new Date();

                        resultNode && (resultNode.textContent = `${successMess} ${date.toLocaleDateString()} ${date.toLocaleTimeString()}`);
                        resultNode && (resultNode.style.color = `green`);

                        const newDateTime = response.data?.[nextDateInputName]
                        if (newDateTime)
                        {
                            const dateTimeInput = document.getElementById(nextDateInputName);
                            dateTimeInput && (dateTimeInput.value = `${newDateTime}`);
                        }
                    }
                    executeBtn.removeAttribute(`disabled`);
                })
                .catch(res => {
                    resultNode && (resultNode.style.color = `red`);
                    if (res.errors && Array.isArray(res.errors) && res.errors.length > 0)
                    {
                        resultNode && (resultNode.textContent = `${res.errors[0]?.message}`);
                        /*res.errors.forEach(error => {
                            console.log(`${error.message}`)
                        })*/
                    }
                    else
                    {
                        resultNode && (resultNode.textContent = `${res.message ?? res}`);
                        //console.log(res.message ?? res);
                    }
                    executeBtn.removeAttribute(`disabled`);
                });
        });
    }
}