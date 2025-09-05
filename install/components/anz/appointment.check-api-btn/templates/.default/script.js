
class CheckApiBtn
{
    constructor(
        buttonId, exchangeModeSelectId, urlInputId, loginInputId, passwordInputId, tokenInputId, successMess, errPrefix
    )
    {
        const checkBtn = document.getElementById(buttonId);
        const resultNode = checkBtn?.closest("div")?.querySelector(`span`);
        checkBtn?.addEventListener(`click`, () => {
            const mode = document.getElementById(exchangeModeSelectId)?.value;
            const url = document.getElementById(urlInputId)?.value;
            const login = document.getElementById(loginInputId)?.value;
            const password = document.getElementById(passwordInputId)?.value;
            const token = document.getElementById(tokenInputId)?.value;

            checkBtn.setAttribute(`disabled`, `true`);

            BX.ajax.runAction(`anz:appointment.oneCController.checkConnection`, {
                data: {
                    mode,
                    url,
                    login,
                    password,
                    token,
                    sessid: BX.bitrix_sessid()
                }
            })
                .then(response => {
                    if (response.status === `success`)
                    {
                        resultNode && (resultNode.textContent = `${successMess}`);
                        resultNode && (resultNode.style.color = `green`);
                    }
                    checkBtn.removeAttribute(`disabled`);
                })
                .catch(res => {
                    resultNode && (resultNode.style.color = `red`);
                    if (res.errors && Array.isArray(res.errors) && res.errors.length > 0)
                    {
                        resultNode && (resultNode.textContent = `${errPrefix} ${res.errors[0]?.message}`);
                        /*res.errors.forEach(error => {
                            console.log(`${error.message}`)
                        })*/
                    }
                    else
                    {
                        resultNode && (resultNode.textContent = `${errPrefix} ${res.message ?? res}`);
                        //console.log(res.message ?? res);
                    }
                    checkBtn.removeAttribute(`disabled`);
                });
        });
    }
}