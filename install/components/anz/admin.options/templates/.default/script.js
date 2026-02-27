
class ModuleOptions
{
    constructor(params = {})
    {
        this.params = params;

        this.init();
    }

    init()
    {
        BX.UI?.Hint?.init(BX(this.params.formId));

        this.initTemplateOptions();
        this.initTabs();
        this.initBtnInputs();
    }

    initBtnInputs(){
        const activateInputs = () => {
            const inputs = {
                customMainBtnCheckbox: BX(this.params['useCustomBtnOptionKey'])
            };

            for (let key in inputs){
                if (inputs.hasOwnProperty(key))
                {
                    switch (key) {
                        case "customMainBtnCheckbox":
                            if(inputs[key]){
                                changeInputsState(inputs[key]);
                                inputs[key].addEventListener('change', () => changeInputsState(inputs[key]))
                            }
                            break;
                        default:
                            break;
                    }
                }

            }
        }

        const changeInputsState = (checkbox) => {
            const textInput = BX(this.params['customBtnSelectorOptionKey']);
            const bgColorInput = BX(this.params['mainBtnBgOptionKey']);
            const textColorInput = BX(this.params['mainBtnTextColorOptionKey']);
            if (checkbox.checked)
            {
                textInput.removeAttribute('disabled');
                bgColorInput.setAttribute('disabled', true);
                textColorInput.setAttribute('disabled', true);
            }
            else
            {
                textInput.setAttribute('disabled', true);
                bgColorInput.removeAttribute('disabled');
                textColorInput.removeAttribute('disabled');
            }
        }

        activateInputs();
    }

    initTabs()
    {
        window.addEventListener('load', () => {
            const updateFormAction = (tabCode)=> {
                const form = BX(this.params.formId);
                if (form)
                {
                    const url = new URL(form.action, window.location.origin);
                    url.searchParams.set('tabControl_active_tab', tabCode);
                    form.action = url.toString();
                }
            }

            const activeTab = (new URL(window.location.href)).searchParams.get('tabControl_active_tab');
            if (activeTab) {
                updateFormAction(activeTab);
            }

            const tabs = document.querySelectorAll('.adm-detail-tab');
            tabs.length && tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const tabId = tab.getAttribute('id');
                    if(tabId)
                    {
                        const tabParsedId = tabId.replace('tab_cont_', '');
                        updateFormAction(tabParsedId);

                        const url = new URL(window.location.href);
                        url.searchParams.set('tabControl_active_tab', tabParsedId);
                        window.history.replaceState({}, '', url);
                    }
                });
            });
        });
    }

    initTemplateOptions()
    {
        const templateSelector = BX(this.params['jsExtOptionKey']);
        const templateOptions = document.querySelectorAll(`[data-extension]`)
        templateSelector && templateSelector.addEventListener('change', () => prepareTemplateOptions());
        const prepareTemplateOptions = () => {
            templateOptions.length && templateOptions.forEach(option => {
                if (templateSelector.value === option.dataset.extension)
                {
                    option.closest('tr')?.classList.remove('disabled');
                }
                else
                {
                    option.closest('tr')?.classList.add('disabled');
                }
            })
        }
        prepareTemplateOptions();
    }
}