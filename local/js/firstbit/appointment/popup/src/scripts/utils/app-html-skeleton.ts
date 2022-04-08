import {ISettings} from "../../types/settings";

export default function buildAppointmentSkeleton(styles: any, settings: ISettings){
    let html = `
    <div class="${styles['appointment-button-wrapper']} ${styles['loading']}" id="${settings.widgetBtnWrapId}">
            <button id="${settings.widgetBtnId}"></button>
            <div class="${styles['appointment-loader']}">
                <div class="${styles['wBall']}" id="${styles['wBall_1']}"><div class="${styles['wInnerBall']}"></div></div>
                <div class="${styles['wBall']}" id="${styles['wBall_2']}"><div class="${styles['wInnerBall']}"></div></div>
                <div class="${styles['wBall']}" id="${styles['wBall_3']}"><div class="${styles['wInnerBall']}"></div></div>
                <div class="${styles['wBall']}" id="${styles['wBall_4']}"><div class="${styles['wInnerBall']}"></div></div>
                <div class="${styles['wBall']}" id="${styles['wBall_5']}"><div class="${styles['wInnerBall']}"></div></div>
            </div>
    </div>
    <div class="${styles['widget-wrapper']}" id="${settings.wrapperId}">
        <form id="${settings.formId}" class="${styles['appointment-form']}">
    `;
        for(const key in settings.selectionNodes)
        {
            if (settings.selectionNodes.hasOwnProperty(key)){
                html = html+`
                <div class="${styles['selection-block']} ${key===settings.dataKeys.clinicsKey ? '' : styles['hidden']}" id="${key}_block">
                    <p class="${styles['selection-item-selected']}" id="${key}_selected">${settings.defaultText[key]}</p>
                    <ul class="${styles['appointment-form_head_list']} ${styles['selection-item-list']}" id="${key}_list"></ul>
                    <input type="hidden" name="${key}" id="${key}_value">
                </div>
            `;
            }
        }
        for(const key in settings.textBlocks)
        {
            if (settings.textBlocks.hasOwnProperty(key)){
                html = html+`<label class="${styles['appointment-form_input-wrapper']}">
            <${settings.textBlocks[key]["type"] ? 'input' : 'textarea'}`;
                for(const attr in settings.textBlocks[key])
                {
                    if (settings.textBlocks[key].hasOwnProperty(attr) && attr !== "tag"){
                        if (attr === "class"){
                            html = html+`
                        ${attr}="${styles[settings.textBlocks[key][attr]]}"	
                    `;
                        }
                        else
                        {
                            html = html+`
                        ${attr}="${settings.textBlocks[key][attr]}"	
                    `;
                        }
                    }
                }
                html = html+`>${settings.textBlocks[key]["type"] ? '' : '</textarea>'}</label>`;
            }
        }

        html = html + `<p id="${settings.messageNodeId}"></p>
    
            <div class="${styles['appointment-form_submit-wrapper']}">
                <button type="submit" id="${settings.submitBtnId}" class="${styles['appointment-form_button']}">Записаться на приём</button>
            </div>
    
            <div id="${settings.appResultBlockId}"><p></p></div>
        </form>
    </div> 
    `;

    return html;
}