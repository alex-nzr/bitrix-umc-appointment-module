this.BX = this.BX || {};
this.BX.FirstBit = this.BX.FirstBit || {};
(function (exports,date,main_core) {
  'use strict';

  function styleInject(css, ref) {
    if (ref === void 0) ref = {};
    var insertAt = ref.insertAt;

    if (!css || typeof document === 'undefined') {
      return;
    }

    var head = document.head || document.getElementsByTagName('head')[0];
    var style = document.createElement('style');
    style.type = 'text/css';

    if (insertAt === 'top') {
      if (head.firstChild) {
        head.insertBefore(style, head.firstChild);
      } else {
        head.appendChild(style);
      }
    } else {
      head.appendChild(style);
    }

    if (style.styleSheet) {
      style.styleSheet.cssText = css;
    } else {
      style.appendChild(document.createTextNode(css));
    }
  }

  var css_248z = "@import url(\"https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap\");body{margin:0;padding:0}.app_appointment-popup-overlay__Wno-g{--main-h:205;--main-s:98%;--main-l:32%;--field-h:217;--field-s:53%;--field-l:22%;--appointment-main-color:hsl(var(--main-h),var(--main-s),var(--main-l));--appointment-field-color:hsl(var(--field-h),var(--field-s),var(--field-l));--appointment-field-hover-color:hsl(var(--field-h),var(--field-s),calc(var(--field-l) + 10%));--appointment-plate-color:hsl(var(--main-h),calc(var(--main-s) - 50%),var(--main-l));--appointment-form-text-color:#f5f5f5;--appointment-btn-bg-color:#12b1e3;--appointment-btn-text-color:#fff;--appointment-start-btn-text-color:#fff;--appointment-start-btn-bg-color:#025ea1;align-items:flex-start;background:rgba(0,0,0,.5);bottom:0;display:flex;height:100vh;justify-content:center;left:0;opacity:0;overflow:auto;position:fixed;right:0;top:0;transition:opacity .3s;width:100vw}.app_appointment-popup-overlay__Wno-g,.app_appointment-popup-overlay__Wno-g *{box-sizing:border-box;color:var(--appointment-form-text-color);font-family:Roboto,sans-serif;font-size:12px;font-style:normal;font-weight:400;pointer-events:none;user-select:none}.app_appointment-popup-overlay__Wno-g ::-webkit-scrollbar,.app_appointment-popup-overlay__Wno-g::-webkit-scrollbar{background:transparent;border-radius:10px;width:2px}.app_appointment-popup-overlay__Wno-g ::-webkit-scrollbar-thumb,.app_appointment-popup-overlay__Wno-g::-webkit-scrollbar-thumb{background:var(--appointment-plate-color);border-radius:10px;width:2px}.app_appointment-popup-overlay__Wno-g li,.app_appointment-popup-overlay__Wno-g ol,.app_appointment-popup-overlay__Wno-g ul{list-style:none;padding:0}.app_appointment-popup-overlay__Wno-g button,.app_appointment-popup-overlay__Wno-g button:active,.app_appointment-popup-overlay__Wno-g button:focus,.app_appointment-popup-overlay__Wno-g input,.app_appointment-popup-overlay__Wno-g input:active,.app_appointment-popup-overlay__Wno-g input:focus,.app_appointment-popup-overlay__Wno-g textarea,.app_appointment-popup-overlay__Wno-g textarea:active,.app_appointment-popup-overlay__Wno-g textarea:focus{border:none;outline:none;resize:none}.app_appointment-popup-overlay__Wno-g a,.app_appointment-popup-overlay__Wno-g a:focus,.app_appointment-popup-overlay__Wno-g a:hover{outline:none;text-decoration:none}.app_appointment-popup-overlay__Wno-g h1,.app_appointment-popup-overlay__Wno-g h2,.app_appointment-popup-overlay__Wno-g h3,.app_appointment-popup-overlay__Wno-g h4,.app_appointment-popup-overlay__Wno-g h5,.app_appointment-popup-overlay__Wno-g h6,.app_appointment-popup-overlay__Wno-g label,.app_appointment-popup-overlay__Wno-g p{margin:0;padding:0}.app_appointment-popup-overlay__Wno-g input[type=number]::-webkit-inner-spin-button,.app_appointment-popup-overlay__Wno-g input[type=number]::-webkit-outer-spin-button{-webkit-appearance:none;margin:0}.app_appointment-popup-overlay__Wno-g .app_appointment-warning-text__s-Od7{color:#ff4500;text-align:center}.app_appointment-popup-overlay__Wno-g #app_appointment-form__rPVyI{align-items:center;background:var(--appointment-main-color);border-radius:6px;box-shadow:0 0 5px 1px var(--appointment-main-color);display:flex;flex-direction:column;margin-top:0;max-width:600px;min-height:500px;opacity:0;overflow:hidden;padding:20px 10px;position:relative;top:50%;transform:translateY(-110vh);transform-origin:left bottom;transition:.5s;width:100%;z-index:1000}.app_appointment-popup-overlay__Wno-g #app_appointment-form__rPVyI.app_loading__svaGP:before{background:hsla(0,0%,100%,.5);content:\"\";filter:blur(2px);height:100%;left:0;position:absolute;top:0;width:100%;z-index:2}.app_appointment-popup-overlay__Wno-g #app_appointment-form__rPVyI.app_loading__svaGP .app_default-loader-wrapper__U0L4b{height:100px;left:50%;position:absolute;top:50%;transform:translate(-50%,-50%);width:100px;z-index:3}.app_appointment-popup-overlay__Wno-g #app_appointment-form__rPVyI.app_loading__svaGP .app_default-loader-wrapper__U0L4b .app_default-loader-circular__vTOun{animation:app_loader-rotate__lk8Hp 2s linear infinite;bottom:0;display:block;left:0;position:absolute;right:0;top:0;transform-origin:center center;z-index:4}.app_appointment-popup-overlay__Wno-g #app_appointment-form__rPVyI.app_loading__svaGP .app_default-loader-wrapper__U0L4b .app_default-loader-path__Yksuc{stroke:#025ea1;stroke-width:2.5;stroke-dasharray:20,200;stroke-dashoffset:0;stroke-linecap:round;animation:app_loader-dash__BL3Xy 1.5s ease-in-out infinite}@keyframes app_loader-rotate__lk8Hp{to{transform:rotate(1turn)}}@keyframes app_loader-dash__BL3Xy{0%{stroke-dasharray:1,200;stroke-dashoffset:0}50%{stroke-dasharray:89,200;stroke-dashoffset:-35px}to{stroke-dasharray:89,200;stroke-dashoffset:-124px}}.app_appointment-popup-overlay__Wno-g #app_appointment-form__rPVyI.app_off__-q-J->:not(#app_appointment-result-block__CEjD5){opacity:0;pointer-events:none}.app_appointment-popup-overlay__Wno-g #app_appointment-form__rPVyI.app_appointment-form-confirmation-mode__4Y5Iq{min-height:auto}.app_appointment-popup-overlay__Wno-g #app_appointment-form__rPVyI.app_appointment-form-confirmation-mode__4Y5Iq>:not(#app_appointment-form-confirmation-wrapper__2dQj-):not(.app_appointment-form-head__22fv4){display:none}.app_appointment-popup-overlay__Wno-g #app_appointment-form__rPVyI ::placeholder{color:var(--appointment-form-text-color)}.app_appointment-popup-overlay__Wno-g #app_appointment-form__rPVyI #app_appointment-form-close__qzv0-,.app_appointment-popup-overlay__Wno-g #app_appointment-form__rPVyI .app_default-loader-circular__vTOun{display:none}.app_appointment-popup-overlay__Wno-g #app_appointment-form__rPVyI .app_appointment-form-head__22fv4{padding:0 20px 20px;position:relative}.app_appointment-popup-overlay__Wno-g #app_appointment-form__rPVyI .app_appointment-form-head-logo__5xu5o{font-size:45px;height:auto;max-height:150px;max-width:100%;min-height:50px;object-fit:cover;object-position:top center;width:auto}.app_appointment-popup-overlay__Wno-g #app_appointment-form__rPVyI .app_appointment-form-step__pZ9s0{align-items:stretch;display:flex;flex-direction:column;height:100%;justify-content:flex-start;min-height:200px;padding-top:30px;position:relative;transition:opacity .3s;width:100%;z-index:1}.app_appointment-popup-overlay__Wno-g #app_appointment-form__rPVyI .app_appointment-form-step__pZ9s0.app_hidden__MT-zb{left:0;opacity:0;pointer-events:none;position:absolute;top:0;transition:opacity 0s;z-index:0}.app_appointment-popup-overlay__Wno-g #app_appointment-form__rPVyI .app_appointment-form-step__pZ9s0.app_hidden__MT-zb *{pointer-events:none}.app_appointment-popup-overlay__Wno-g #app_appointment-form__rPVyI .app_appointment-form-step__pZ9s0~.app_appointment-form-step__pZ9s0{padding-top:0}.app_appointment-popup-overlay__Wno-g.app_active__vW0S6{opacity:1}.app_appointment-popup-overlay__Wno-g.app_active__vW0S6,.app_appointment-popup-overlay__Wno-g.app_active__vW0S6 *{pointer-events:auto}.app_appointment-popup-overlay__Wno-g.app_active__vW0S6 #app_appointment-form__rPVyI{opacity:1;transform:translateY(-50%)}.app_appointment-popup-overlay__Wno-g.app_active__vW0S6 #app_appointment-form__rPVyI.app_appointment-form-confirmation-mode__4Y5Iq{min-height:auto}.app_appointment-popup-overlay__Wno-g.app_active__vW0S6 #app_appointment-form__rPVyI.app_appointment-form-confirmation-mode__4Y5Iq>:not(#app_appointment-form-confirmation-wrapper__2dQj-):not(.app_appointment-form-head__22fv4){display:none}.app_appointment-popup-overlay__Wno-g .app_appointment-form_input-wrapper__gmnZQ,.app_appointment-popup-overlay__Wno-g .app_selection-block__jPMo8{background-color:var(--appointment-field-color);border-radius:6px;cursor:pointer;display:block;margin-bottom:15px;max-height:300px;position:relative;transition:.5s;width:100%}.app_appointment-popup-overlay__Wno-g .app_appointment-form_input-wrapper__gmnZQ.app_error__JtV2y,.app_appointment-popup-overlay__Wno-g .app_selection-block__jPMo8.app_error__JtV2y{box-shadow:inset 0 0 0 2px red}.app_appointment-popup-overlay__Wno-g .app_appointment-form_input-wrapper__gmnZQ.app_disabled__Ghk-k,.app_appointment-popup-overlay__Wno-g .app_selection-block__jPMo8.app_disabled__Ghk-k{cursor:not-allowed;opacity:.7;position:relative}.app_appointment-popup-overlay__Wno-g .app_appointment-form_input-wrapper__gmnZQ.app_disabled__Ghk-k:before,.app_appointment-popup-overlay__Wno-g .app_selection-block__jPMo8.app_disabled__Ghk-k:before{bottom:0;content:\"\";display:block;height:100%;left:0;position:absolute;right:0;top:0;width:100%;z-index:1}.app_appointment-popup-overlay__Wno-g .app_selection-item-selected__J4EWB{align-items:center;border-radius:6px;display:flex;height:36px;justify-content:flex-start;padding:0 25px 0 15px;position:relative;transition:.3s;width:100%}.app_appointment-popup-overlay__Wno-g .app_selection-item-selected__J4EWB:hover{background-color:var(--appointment-field-hover-color)}.app_appointment-popup-overlay__Wno-g .app_selection-item-selected__J4EWB:before{content:\"\\25BC\";cursor:pointer;height:10px;position:absolute;right:20px;top:50%;transform:translateY(-50%);width:10px}.app_appointment-popup-overlay__Wno-g .app_selection-item-selected__J4EWB span{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.app_appointment-popup-overlay__Wno-g .app_selection-item-list__MofBn{align-items:stretch;border-radius:0 0 6px 6px;display:flex;flex-wrap:wrap;justify-content:flex-start;margin:0;max-height:0;overflow:hidden;padding:0;transition:.5s}.app_appointment-popup-overlay__Wno-g .app_selection-item-list__MofBn.app_column-mode__hghAT{align-items:flex-start;display:flex;flex-wrap:nowrap;justify-content:flex-start;overflow:auto}.app_appointment-popup-overlay__Wno-g .app_selection-item-list__MofBn.app_column-mode__hghAT::-webkit-scrollbar{height:0}.app_appointment-popup-overlay__Wno-g .app_selection-item-list__MofBn.app_column-mode__hghAT.app_active__vW0S6{max-height:240px}.app_appointment-popup-overlay__Wno-g .app_selection-item-list__MofBn.app_column-mode__hghAT.app_active__vW0S6 .app_horizontal-scroll-buttons__WX-1p{opacity:1}.app_appointment-popup-overlay__Wno-g .app_selection-item-list__MofBn.app_column-mode__hghAT.app_active__vW0S6 .app_horizontal-scroll-buttons__WX-1p button{pointer-events:auto}.app_appointment-popup-overlay__Wno-g .app_selection-item-list__MofBn.app_column-mode__hghAT>li{background:transparent;flex:0 0 33.33333%;flex-direction:column;height:100%;justify-content:flex-start;margin:0;padding-top:0;text-align:center}.app_appointment-popup-overlay__Wno-g .app_selection-item-list__MofBn.app_column-mode__hghAT>li:hover{background-color:transparent}.app_appointment-popup-overlay__Wno-g .app_selection-item-list__MofBn.app_column-mode__hghAT>li p{background-color:var(--appointment-field-color);color:var(--appointment-form-text-color);font-weight:600;left:0;position:sticky;top:0;white-space:nowrap;width:100%}.app_appointment-popup-overlay__Wno-g .app_selection-item-list__MofBn.app_column-mode__hghAT>li p:last-of-type{margin-bottom:5px}.app_appointment-popup-overlay__Wno-g .app_selection-item-list__MofBn.app_column-mode__hghAT>li span{background:var(--appointment-plate-color);border-radius:5px;margin:3px auto;padding:5px;scroll-snap-align:start;transition:.3s;width:100%}.app_appointment-popup-overlay__Wno-g .app_selection-item-list__MofBn.app_column-mode__hghAT>li span:hover{background:var(--appointment-field-hover-color)}.app_appointment-popup-overlay__Wno-g .app_selection-item-list__MofBn.app_column-mode__hghAT .app_horizontal-scroll-buttons__WX-1p{bottom:calc(50% - 15px);display:flex;justify-content:space-between;left:-10px;opacity:0;pointer-events:none;position:absolute;transition:.3s;width:calc(100% + 20px)}.app_appointment-popup-overlay__Wno-g .app_selection-item-list__MofBn.app_column-mode__hghAT .app_horizontal-scroll-buttons__WX-1p button{align-items:center;background:var(--appointment-btn-bg-color);border-radius:5px;color:#000;cursor:pointer;display:flex;font-size:22px;font-weight:600;height:30px;justify-content:center;overflow:hidden;width:20px}.app_appointment-popup-overlay__Wno-g .app_selection-item-list__MofBn.app_active__vW0S6{max-height:170px;overflow:auto}.app_appointment-popup-overlay__Wno-g .app_selection-item-list__MofBn li{align-items:center;background:var(--appointment-plate-color);border-radius:6px;cursor:pointer;display:flex;flex:1 0 calc(33.33333% - 4px);height:auto;justify-content:space-between;margin:2px;min-height:36px;padding:5px 15px;transition:.3s;user-select:none;width:100%}.app_appointment-popup-overlay__Wno-g .app_selection-item-list__MofBn li:hover{background-color:var(--appointment-field-hover-color)}.app_appointment-popup-overlay__Wno-g .app_selection-item-list__MofBn li span{font-weight:600}.app_appointment-popup-overlay__Wno-g .app_appointment-form_input__KdGOL{align-items:center;background:transparent;border-radius:6px;cursor:auto;display:flex;height:36px;justify-content:space-between;padding:0 15px;transition:.3s;user-select:none;width:100%}.app_appointment-popup-overlay__Wno-g .app_empty-selection-message__M-Reh{display:block;padding:0 15px 5px;pointer-events:none}.app_appointment-popup-overlay__Wno-g .app_appointment-form_textarea__4--nv{background:transparent;border-radius:6px;height:100px;padding:5px 15px;transition:.3s;width:100%}.app_appointment-popup-overlay__Wno-g #app_appointment-form-message__CmKRf{color:#ff4500;font-size:14px;margin:10px auto 5px;text-align:center}.app_appointment-popup-overlay__Wno-g .app_appointment-form-button-wrapper__DOAlv{align-items:flex-end;display:flex;flex-grow:1;justify-content:center;margin-top:auto;padding-top:5px;position:relative}.app_appointment-popup-overlay__Wno-g .app_appointment-form-button-wrapper__DOAlv button{margin:0}.app_appointment-popup-overlay__Wno-g .app_appointment-form-button-wrapper__DOAlv button:nth-of-type(2){margin-left:20px}.app_appointment-popup-overlay__Wno-g .app_appointment-form-button-wrapper__DOAlv button[disabled]{cursor:not-allowed;opacity:.7}.app_appointment-popup-overlay__Wno-g .app_appointment-form-button__V605q{align-items:center;background:var(--appointment-btn-bg-color);border:1px solid var(--appointment-btn-bg-color);border-radius:6px;color:var(--appointment-btn-text-color);cursor:pointer;display:flex;font-weight:500;height:36px;justify-content:center;margin:0 auto;min-width:100px;padding:0 15px;position:relative;text-transform:none;transition:.3s;user-select:none}.app_appointment-popup-overlay__Wno-g .app_appointment-form-button__V605q:hover{color:var(--appointment-btn-text-color);opacity:.7}.app_appointment-popup-overlay__Wno-g .app_appointment-form-button__V605q:before{animation:app_rotating__ur-7t 1s linear infinite;background:url(\"data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='512' height='512' viewBox='0 0 399.389 399.389' style='enable-background:new 0 0 512 512' xml:space='preserve'%3E%3Cpath d='M340.896 58.489C303.18 20.773 253.031.001 199.693.001c-53.34 0-103.487 20.771-141.204 58.488C20.772 96.207 0 146.355 0 199.694c0 53.34 20.772 103.489 58.49 141.206 37.717 37.717 87.864 58.488 141.204 58.488 53.339 0 103.486-20.771 141.205-58.488 37.717-37.717 58.49-87.865 58.49-141.206-.002-53.339-20.776-103.487-58.493-141.205zm-12.835 12.837c34.289 34.289 53.172 79.878 53.172 128.368h-41.148c0-77.412-62.979-140.391-140.391-140.391-4.593 0-9.134.229-13.615.662v-41.31c4.508-.332 9.049-.5 13.615-.5 48.49 0 94.077 18.883 128.367 53.171zM199.693 321.931c-67.401 0-122.236-54.835-122.236-122.236S132.292 77.458 199.693 77.458 321.93 132.293 321.93 199.694s-54.836 122.237-122.237 122.237z' fill='%23fff' data-original='%23000000' xmlns='http://www.w3.org/2000/svg'/%3E%3C/svg%3E\") 50%/contain no-repeat;content:\"\";height:20px;left:50%;opacity:0;pointer-events:none;position:absolute;top:50%;transform:translate(-50%,-50%);transition:.2s;width:20px}.app_appointment-popup-overlay__Wno-g .app_appointment-form-button__V605q.app_loading__svaGP{color:transparent;pointer-events:none}.app_appointment-popup-overlay__Wno-g .app_appointment-form-button__V605q.app_loading__svaGP:before{opacity:1}.app_appointment-popup-overlay__Wno-g #app_appointment-form-confirmation-wrapper__2dQj-{width:100%}.app_appointment-popup-overlay__Wno-g .app_appointment-form-button-link__33Kwz{border-bottom:1px dashed var(--appointment-btn-bg-color);color:var(--appointment-btn-bg-color);display:block;margin:10px auto 0;max-width:max-content;text-decoration:none;transition:.3s}.app_appointment-popup-overlay__Wno-g .app_appointment-form-button-link__33Kwz:focus,.app_appointment-popup-overlay__Wno-g .app_appointment-form-button-link__33Kwz:hover,.app_appointment-popup-overlay__Wno-g .app_appointment-form-button-link__33Kwz:visited{opacity:.7}.app_appointment-popup-overlay__Wno-g .app_appointment-info-message__c-Afe{color:var(--appointment-btn-bg-color);padding-top:10px;text-align:center;user-select:none}.app_appointment-popup-overlay__Wno-g .app_appointment-info-message__c-Afe a{border-bottom:1px solid var(--appointment-btn-bg-color);color:var(--appointment-btn-bg-color);white-space:nowrap}.app_appointment-popup-overlay__Wno-g #app_appointment-result-block__CEjD5{align-items:center;background:var(--appointment-main-color);display:flex;height:100%;justify-content:center;left:0;opacity:0;padding:20px;pointer-events:none;position:absolute;top:0;transition:.3s;width:100%}.app_appointment-popup-overlay__Wno-g #app_appointment-result-block__CEjD5.app_active__vW0S6{opacity:1;pointer-events:auto}.app_appointment-popup-overlay__Wno-g #app_appointment-result-block__CEjD5 p{color:var(--appointment-form-text-color);position:relative;text-align:center;width:100%}.app_appointment-popup-overlay__Wno-g #app_appointment-result-block__CEjD5 p.app_error__JtV2y{font-size:17px;line-height:1.3}.app_appointment-popup-overlay__Wno-g #app_appointment-result-block__CEjD5 p.app_success__8u0SY{font-size:13px}.app_appointment-popup-overlay__Wno-g #app_appointment-result-block__CEjD5 p.app_success__8u0SY:before{align-items:center;border-radius:50%;color:var(--appointment-form-text-color);content:\"\\2714\";display:flex;font-size:40px;height:50px;justify-content:center;left:50%;line-height:1;pointer-events:none;position:absolute;text-align:center;top:-60px;transform:translate(-50%);transition:.2s;width:50px}.app_appointment-popup-overlay__Wno-g #app_appointment-result-block__CEjD5 p a{border-bottom:1px solid var(--appointment-btn-bg-color);color:var(--appointment-btn-bg-color)}@keyframes app_rotating__ur-7t{0%{transform:translate(-50%,-50%) rotate(0)}to{transform:translate(-50%,-50%) rotate(1turn)}}.app_appointment-button-wrapper__MilBW{--main-h:205;--main-s:98%;--main-l:32%;--field-h:217;--field-s:53%;--field-l:22%;--appointment-main-color:hsl(var(--main-h),var(--main-s),var(--main-l));--appointment-field-color:hsl(var(--field-h),var(--field-s),var(--field-l));--appointment-field-hover-color:hsl(var(--field-h),var(--field-s),calc(var(--field-l) + 10%));--appointment-plate-color:hsl(var(--main-h),calc(var(--main-s) - 50%),var(--main-l));--appointment-form-text-color:#f5f5f5;--appointment-btn-bg-color:#12b1e3;--appointment-btn-text-color:#fff;--appointment-start-btn-text-color:#fff;--appointment-start-btn-bg-color:#025ea1;color:var(--appointment-form-text-color);font-family:Roboto,sans-serif;font-size:12px;font-style:normal;font-weight:400}.app_appointment-button-wrapper__MilBW.app_hidden__MT-zb{margin-bottom:0;max-height:0;transform:scaleY(0);transform-origin:center top}#app_appointment-button__vAbo4{--this-btn-br:6px;align-items:center;background:transparent;border:none;border-radius:var(--this-btn-br);bottom:20px;color:var(--appointment-start-btn-text-color);cursor:pointer;display:flex;font-size:14px;height:40px;justify-content:center;left:10px;outline:none;padding:0 15px;position:fixed;transition:.2s;width:auto;z-index:1005}#app_appointment-button__vAbo4:before{animation:app_appointmentPulse__j9AYC 1.5s linear infinite;z-index:0}#app_appointment-button__vAbo4:after,#app_appointment-button__vAbo4:before{background:var(--appointment-start-btn-bg-color);border-radius:var(--this-btn-br);content:\"\";cursor:pointer;height:100%;left:0;position:absolute;top:0;transition:.2s;width:100%}#app_appointment-button__vAbo4:after{align-items:center;display:flex;font-size:20px;justify-content:center;text-align:center;z-index:1}#app_appointment-button__vAbo4 span{font-weight:600;position:relative;z-index:2}#app_appointment-button__vAbo4.app_active__vW0S6:before{animation:none;opacity:0}#app_appointment-button__vAbo4.app_active__vW0S6:after{content:\"\\2716\"}#app_appointment-button__vAbo4.app_active__vW0S6 span{opacity:0}#app_appointment-button__vAbo4.app_success__8u0SY{pointer-events:none}#app_appointment-button__vAbo4.app_success__8u0SY:before{animation:none;opacity:0}#app_appointment-button__vAbo4.app_success__8u0SY:after{content:\"\\2714\";font-size:24px}#app_appointment-button__vAbo4.app_success__8u0SY span{opacity:0}@keyframes app_appointmentPulse__j9AYC{0%{opacity:1;transform:scaleX(1)}to{opacity:0;transform:scale3d(1.2,1.6,1.2)}}@media (max-width:767px){.app_appointment-popup-overlay__Wno-g #app_appointment-form__rPVyI{padding-top:50px;top:0;transform:translateY(0)}.app_appointment-popup-overlay__Wno-g #app_appointment-form__rPVyI #app_appointment-form-close__qzv0-{align-items:center;color:var(--appointment-form-text-color);cursor:pointer;display:flex;font-size:16px;font-weight:600;height:30px;justify-content:center;line-height:1;position:absolute;right:10px;top:10px;width:30px}.app_appointment-popup-overlay__Wno-g.app_active__vW0S6 #app_appointment-form__rPVyI{border-radius:0;margin:0;min-height:100vh;top:0;transform:translateY(0)}.app_appointment-popup-overlay__Wno-g.app_active__vW0S6 #app_appointment-form__rPVyI.app_appointment-form-confirmation-mode__4Y5Iq{margin-top:0;top:0;transform:translateY(0)}#app_appointment-button__vAbo4.app_active__vW0S6{display:none}}";
  var styles = {
    "appointment-popup-overlay": "app_appointment-popup-overlay__Wno-g",
    "appointment-warning-text": "app_appointment-warning-text__s-Od7",
    "appointment-form": "app_appointment-form__rPVyI",
    "loading": "app_loading__svaGP",
    "default-loader-wrapper": "app_default-loader-wrapper__U0L4b",
    "default-loader-circular": "app_default-loader-circular__vTOun",
    "loader-rotate": "app_loader-rotate__lk8Hp",
    "default-loader-path": "app_default-loader-path__Yksuc",
    "loader-dash": "app_loader-dash__BL3Xy",
    "off": "app_off__-q-J-",
    "appointment-result-block": "app_appointment-result-block__CEjD5",
    "appointment-form-confirmation-mode": "app_appointment-form-confirmation-mode__4Y5Iq",
    "appointment-form-confirmation-wrapper": "app_appointment-form-confirmation-wrapper__2dQj-",
    "appointment-form-head": "app_appointment-form-head__22fv4",
    "appointment-form-close": "app_appointment-form-close__qzv0-",
    "appointment-form-head-logo": "app_appointment-form-head-logo__5xu5o",
    "appointment-form-step": "app_appointment-form-step__pZ9s0",
    "hidden": "app_hidden__MT-zb",
    "active": "app_active__vW0S6",
    "selection-block": "app_selection-block__jPMo8",
    "appointment-form_input-wrapper": "app_appointment-form_input-wrapper__gmnZQ",
    "error": "app_error__JtV2y",
    "disabled": "app_disabled__Ghk-k",
    "selection-item-selected": "app_selection-item-selected__J4EWB",
    "selection-item-list": "app_selection-item-list__MofBn",
    "column-mode": "app_column-mode__hghAT",
    "horizontal-scroll-buttons": "app_horizontal-scroll-buttons__WX-1p",
    "appointment-form_input": "app_appointment-form_input__KdGOL",
    "empty-selection-message": "app_empty-selection-message__M-Reh",
    "appointment-form_textarea": "app_appointment-form_textarea__4--nv",
    "appointment-form-message": "app_appointment-form-message__CmKRf",
    "appointment-form-button-wrapper": "app_appointment-form-button-wrapper__DOAlv",
    "appointment-form-button": "app_appointment-form-button__V605q",
    "rotating": "app_rotating__ur-7t",
    "appointment-form-button-link": "app_appointment-form-button-link__33Kwz",
    "appointment-info-message": "app_appointment-info-message__c-Afe",
    "success": "app_success__8u0SY",
    "appointment-button-wrapper": "app_appointment-button-wrapper__MilBW",
    "appointment-button": "app_appointment-button__vAbo4",
    "appointmentPulse": "app_appointmentPulse__j9AYC"
  };
  styleInject(css_248z);

  /**
   * add phone mask
   * @param input
   * @param mask
   */
  function maskInput(input, mask) {
    var value = input.value;
    var literalPattern = /[0]/;
    var numberPattern = /[0-9]/;
    var newValue = "";
    var valueIndex = 0;

    for (var i = 0; i < mask.length; i++) {
      if (i >= value.length) break;
      if (mask[i] === "0" && !numberPattern.test(value[valueIndex])) break;

      while (!literalPattern.test(mask[i])) {
        if (value[valueIndex] === mask[i]) break;
        newValue += mask[i++];
      }

      newValue += value[valueIndex++];
    }

    input.value = newValue;
  }
  function convertHexToHsl(hex) {
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);

    if (result) {
      var r = parseInt(result[1], 16);
      var g = parseInt(result[2], 16);
      var b = parseInt(result[3], 16);
      r /= 255;
      g /= 255;
      b /= 255;
      var max = Math.max(r, g, b),
          min = Math.min(r, g, b);
      var h = 0,
          s,
          l = (max + min) / 2;

      if (max === min) {
        h = s = 0; // achromatic
      } else {
        var d = max - min;
        s = l > 0.5 ? d / (2 - max - min) : d / (max + min);

        switch (max) {
          case r:
            h = (g - b) / d + (g < b ? 6 : 0);
            break;

          case g:
            h = (b - r) / d + 2;
            break;

          case b:
            h = (r - g) / d + 4;
            break;
        }

        h /= 6;
      }

      s = s * 100;
      s = Math.round(s);
      l = l * 100;
      l = Math.round(l);
      h = Math.round(360 * h); //`hsl(${h}, ${s}%, ${l}%)`

      return {
        h: h,
        s: "".concat(s, "%"),
        l: "".concat(l, "%")
      };
    }

    return null;
  }

  var EventManager = /*#__PURE__*/function (_Event$EventEmitter) {
    babelHelpers.inherits(EventManager, _Event$EventEmitter);

    function EventManager() {
      babelHelpers.classCallCheck(this, EventManager);
      return babelHelpers.possibleConstructorReturn(this, babelHelpers.getPrototypeOf(EventManager).apply(this, arguments));
    }

    babelHelpers.createClass(EventManager, null, [{
      key: "bind",
      value: function bind(target, eventName, handler, options) {
        main_core.Event.bind(target, eventName, handler, options);
      }
    }]);
    return EventManager;
  }(main_core.Event.EventEmitter);
  babelHelpers.defineProperty(EventManager, "fullDataLoaded", 'BX.FirstBit.Appointment:dataLoaded');
  babelHelpers.defineProperty(EventManager, "clinicsRendered", 'BX.FirstBit.Appointment:clinicsRendered');
  babelHelpers.defineProperty(EventManager, "formStepChanged", 'BX.FirstBit.Appointment:formStepChanged');

  var Renderer = /*#__PURE__*/function () {
    function Renderer(styles$$1, application) {
      babelHelpers.classCallCheck(this, Renderer);
      this.styles = styles$$1;
      this.application = application;
    }

    babelHelpers.createClass(Renderer, [{
      key: "getAppHtmlSkeleton",
      value: function getAppHtmlSkeleton() {
        return BX.create('div', {
          attrs: {
            id: this.application.selectors.overlayId,
            className: this.styles['appointment-popup-overlay']
          },
          children: [BX.create('form', {
            attrs: {
              id: this.application.selectors.formId,
              className: this.styles['appointment-form']
            },
            children: [this.getLogoBlock(), this.getCloseButton(), this.getFormFirstBlock(), this.getFormSecondBlock(), this.getFormUserDataBlock(), this.getFormMessageBlock(), this.getFormPrivacyBlock(), this.getFormResultBlock(), this.getFormLoaderBlock()]
          })]
        });
      }
    }, {
      key: "getLogoBlock",
      value: function getLogoBlock() {
        return BX.create('div', {
          attrs: {
            className: this.styles['appointment-form-head']
          },
          children: [this.application.companyLogo ? BX.create('img', {
            attrs: {
              className: this.styles['appointment-form-head-logo'],
              src: this.application.companyLogo,
              alt: 'company logo'
            }
          }) : '']
        });
      }
    }, {
      key: "getCloseButton",
      value: function getCloseButton() {
        return BX.create('span', {
          attrs: {
            id: this.styles['appointment-form-close']
          },
          html: '&#10006;'
        });
      }
    }, {
      key: "getFormFirstBlock",
      value: function getFormFirstBlock() {
        var _this = this;

        var doctorBtn = this.getFormBtn(BX.message("FIRSTBIT_JS_FORM_BTN_DOCTOR_FIRST"), function () {
          _this.application.setSelectionDoctorBeforeService(true);
        });
        var serviceBtn = this.getFormBtn(BX.message("FIRSTBIT_JS_FORM_BTN_SERVICE_FIRST"), function () {
          _this.application.setSelectionDoctorBeforeService(false);
        });
        var buttons = this.application.useServices ? [doctorBtn, serviceBtn] : [doctorBtn];
        return BX.create('div', {
          attrs: {
            className: styles['appointment-form-step'],
            id: this.application.selectors.formStepIds.one
          },
          dataset: {
            services: !this.application.useServices ? 'disabled' : false
          },
          children: [].concat(babelHelpers.toConsumableArray(this.getSelectionNodes([this.application.dataKeys.clinicsKey, this.application.dataKeys.specialtiesKey])), [this.getFormButtonsBlock(buttons)])
        });
      }
    }, {
      key: "getFormSecondBlock",
      value: function getFormSecondBlock() {
        var _this2 = this;

        var btnPrev = this.getFormBtn(BX.message('FIRSTBIT_JS_FORM_BTN_PREV'), function () {
          _this2.application.changeFormStep(_this2.application.formStepNodes.one, true);
        }, false, true);
        var btnNext = this.getFormBtn(BX.message('FIRSTBIT_JS_FORM_BTN_NEXT'), function () {
          _this2.application.changeFormStep(_this2.application.formStepNodes.userData);
        });
        return BX.create('div', {
          attrs: {
            className: "".concat(styles['appointment-form-step'], " ").concat(styles['hidden']),
            id: this.application.selectors.formStepIds.two
          },
          children: [].concat(babelHelpers.toConsumableArray(this.getSelectionNodes([this.application.dataKeys.employeesKey, this.application.useServices ? this.application.dataKeys.servicesKey : null, this.application.dataKeys.scheduleKey])), [this.getFormButtonsBlock([btnPrev, btnNext])])
        });
      }
    }, {
      key: "getFormUserDataBlock",
      value: function getFormUserDataBlock() {
        return BX.create('div', {
          attrs: {
            className: "".concat(styles['appointment-form-step'], " ").concat(styles['hidden']),
            id: this.application.selectors.formStepIds.userData
          },
          children: [].concat(babelHelpers.toConsumableArray(this.getTextNodes()), [this.getFormButtonsBlock([this.getFormSubmitBtn()])])
        });
      }
    }, {
      key: "getFormMessageBlock",
      value: function getFormMessageBlock() {
        return BX.create('p', {
          attrs: {
            id: this.application.selectors.messageNodeId
          }
        });
      }
    }, {
      key: "getFormButtonsBlock",
      value: function getFormButtonsBlock(buttons) {
        return BX.create('div', {
          attrs: {
            className: this.styles['appointment-form-button-wrapper']
          },
          children: buttons
        });
      }
    }, {
      key: "getFormSubmitBtn",
      value: function getFormSubmitBtn() {
        return BX.create('button', {
          attrs: {
            type: "submit",
            id: this.application.selectors.submitBtnId,
            className: this.styles['appointment-form-button']
          },
          dataset: {
            "readonly": "Y"
          },
          text: BX.message('FIRSTBIT_JS_FORM_BTN_TEXT')
        });
      }
    }, {
      key: "getFormBtn",
      value: function getFormBtn(text, handler) {
        var disabled = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : true;
        var readonly = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;
        return BX.create('button', {
          attrs: {
            type: "button",
            className: this.styles['appointment-form-button'],
            disabled: disabled
          },
          dataset: {
            "readonly": readonly ? "Y" : "N"
          },
          text: text,
          events: {
            click: handler
          }
        });
      }
    }, {
      key: "getFormPrivacyBlock",
      value: function getFormPrivacyBlock() {
        return BX.create('p', {
          attrs: {
            className: this.styles['appointment-info-message']
          },
          children: [BX.create('span', {
            text: "".concat(BX.message('FIRSTBIT_JS_FORM_CONFIRM_INFO_TEXT'), " ")
          }), BX.create('a', {
            attrs: {
              href: this.application.initParams['privacyPageLink'],
              target: '_blank'
            },
            text: BX.message('FIRSTBIT_JS_FORM_CONFIRM_INFO_LINK')
          })]
        });
      }
    }, {
      key: "getFormResultBlock",
      value: function getFormResultBlock() {
        return BX.create('div', {
          attrs: {
            id: this.application.selectors.appResultBlockId
          },
          children: [BX.create('p', {
            text: ''
          })]
        });
      }
    }, {
      key: "getFormLoaderBlock",
      value: function getFormLoaderBlock() {
        return BX.create('div', {
          attrs: {
            className: this.styles['default-loader-wrapper']
          },
          html: "<svg class=\"".concat(this.styles['default-loader-circular'], "\" viewBox=\"25 25 50 50\">\n                     <circle class=\"").concat(this.styles['default-loader-path'], "\" cx=\"50\" cy=\"50\" r=\"20\" fill=\"none\" stroke-miterlimit=\"10\"></circle>\n                   </svg>")
        });
      }
    }, {
      key: "getSelectionNodes",
      value: function getSelectionNodes(blockKeys) {
        var _this3 = this;

        var arNodes = [];
        blockKeys.length && blockKeys.forEach(function (key) {
          if (!_this3.application.selectionBlocks.hasOwnProperty(key)) {
            return;
          }

          var selected = BX.create('p', {
            attrs: {
              id: _this3.application.selectionBlocks[key].selectedId,
              className: _this3.styles['selection-item-selected']
            },
            text: _this3.application.defaultText[key],
            events: {
              click: function click() {
                return _this3.application.toggleSelectionList(key, selected, list);
              }
            }
          });
          var list = BX.create('ul', {
            attrs: {
              id: _this3.application.selectionBlocks[key].listId,
              className: "".concat(_this3.styles['selection-item-list'])
            },
            text: _this3.application.defaultText[key]
          });
          var input = BX.create('input', {
            attrs: {
              id: _this3.application.selectionBlocks[key].inputId,
              name: _this3.application.selectionBlocks[key].inputId,
              type: 'hidden'
            }
          });
          var additionalClass = key === _this3.application.dataKeys.clinicsKey ? '' : _this3.styles['disabled'];
          arNodes.push(BX.create('div', {
            attrs: {
              id: _this3.application.selectionBlocks[key].blockId,
              className: "".concat(_this3.styles['selection-block'], " ").concat(additionalClass)
            },
            children: [selected, list, input]
          }));
        });
        return arNodes;
      }
    }, {
      key: "getTextNodes",
      value: function getTextNodes() {
        var arNodes = [];

        for (var key in this.application.initParams['textBlocks']) {
          if (!this.application.initParams['textBlocks'].hasOwnProperty(key)) {
            continue;
          }

          arNodes.push(BX.create('label', {
            attrs: {
              className: this.styles['appointment-form_input-wrapper']
            },
            children: [BX.create({
              tag: this.application.initParams['textBlocks'][key]["type"] ? 'input' : 'textarea',
              attrs: this.getTextInputAttrs(this.application.initParams['textBlocks'][key])
            })]
          }));
        }

        return arNodes;
      }
    }, {
      key: "getTextInputAttrs",
      value: function getTextInputAttrs(attrs) {
        var preparedAttrs = {};

        for (var attr in attrs) {
          if (attrs.hasOwnProperty(attr)) {
            if (attr === "class") {
              preparedAttrs.className = this.styles[attrs[attr]];
            } else {
              preparedAttrs[attr] = attrs[attr];
            }
          }
        }

        return preparedAttrs;
      }
      /**
       * Create start button elements
       * @returns {div}
       */

    }, {
      key: "getDefaultStartBtn",
      value: function getDefaultStartBtn() {
        return BX.create('div', {
          attrs: {
            id: this.application.selectors.startBtnWrapId,
            className: this.styles['appointment-button-wrapper']
          },
          children: [BX.create('button', {
            attrs: {
              id: this.application.selectors.startBtnId
            },
            children: [BX.create('span', {
              text: BX.message('FIRSTBIT_JS_START_BTN_TEXT')
            })]
          })]
        });
      }
    }, {
      key: "getRootElement",
      value: function getRootElement() {
        return BX.create('div', {
          attrs: {
            id: this.application.selectors.rootNodeId
          }
        });
      }
    }, {
      key: "renderSelectionItems",
      value: function renderSelectionItems(listNode, dataKey, items) {
        for (var key in items) {
          if (!items.hasOwnProperty(key)) continue;
          if (!this.application.allowToRender(listNode, dataKey, items[key])) continue;

          if (dataKey === this.application.dataKeys.scheduleKey) {
            this.renderScheduleItem(listNode, items[key]);
          } else {
            var _items$key$uid, _items$key$fullName, _items$key$fullName2;

            if (items[key].hasOwnProperty('price')) {
              var price = Number(items[key]['price']) > 0 ? "<b>".concat(items[key]['price'], "</b>&#8381;") : "";
              items[key].fullName = "<p>".concat(items[key].name, "<br> <b>").concat(price, "</b></p>");
            }

            var dataAttrs = {
              uid: (_items$key$uid = items[key].uid) !== null && _items$key$uid !== void 0 ? _items$key$uid : key,
              name: (_items$key$fullName = items[key].fullName) !== null && _items$key$fullName !== void 0 ? _items$key$fullName : items[key].name
            };
            items[key].duration ? dataAttrs.duration = items[key].duration : void 0;
            BX.append(BX.create('li', {
              dataset: dataAttrs,
              html: (_items$key$fullName2 = items[key].fullName) !== null && _items$key$fullName2 !== void 0 ? _items$key$fullName2 : items[key].name
            }), listNode);
          }
        }

        if (listNode.children.length === 0) {
          BX.append(this.createEmptySelectionNode(BX.message("FIRSTBIT_JS_".concat(dataKey.toUpperCase(), "_NOT_FOUND_ERROR"))), listNode);
        } else {
          if (dataKey === this.application.dataKeys.scheduleKey) {
            this.addHorizontalScrollButtons(listNode);
          }

          this.application.addItemActions(dataKey);
        }
      }
    }, {
      key: "renderScheduleItem",
      value: function renderScheduleItem(scheduleList, scheduleItem) {
        var _scheduleItem$timetab,
            _scheduleItem$timetab2,
            _this4 = this;

        var serviceDuration = this.application.getServiceDuration(scheduleItem);
        var renderCustomIntervals = this.application.useServices && serviceDuration > 0;
        var timeKey = renderCustomIntervals ? "freeNotFormatted" : "free";

        if ((_scheduleItem$timetab = scheduleItem['timetable']) !== null && _scheduleItem$timetab !== void 0 && (_scheduleItem$timetab2 = _scheduleItem$timetab[timeKey]) !== null && _scheduleItem$timetab2 !== void 0 && _scheduleItem$timetab2.length) {
          var intervals = scheduleItem['timetable'][timeKey];

          if (renderCustomIntervals) {
            var customIntervals = this.application.getIntervalsForServiceDuration(intervals, serviceDuration * 1000);

            if (customIntervals.length === 0) {
              return;
            } else {
              intervals = customIntervals;
            }
          }

          var renderDate;
          var renderColumn = undefined;
          intervals.forEach(function (day, index) {
            var isLast = index === intervals.length - 1;

            if (day.date !== renderDate || isLast) {
              renderColumn ? scheduleList.append(renderColumn) : void 0;
              !isLast || intervals.length === 1 ? renderColumn = _this4.createDayColumn(day) : void 0;
              renderDate = day.date;
            }

            if (renderColumn) {
              BX.append(BX.create('span', {
                dataset: {
                  displayDate: "".concat(day['formattedDate'], " "),
                  date: day.date,
                  start: day.timeBegin,
                  end: day.timeEnd
                },
                text: "".concat(day['formattedTimeBegin'])
              }), renderColumn);
            }
          });
        }
      }
    }, {
      key: "createDayColumn",
      value: function createDayColumn(day) {
        var date$$1 = this.application.readDateInfo(day.timeBegin);
        return BX.create('li', {
          children: [BX.create('p', {
            text: "".concat(date$$1.weekDay, "\n                        ").concat(day['formattedDate'])
          })]
        });
      }
    }, {
      key: "addHorizontalScrollButtons",
      value: function addHorizontalScrollButtons(scroller) {
        var item = scroller.querySelector('li');

        if (scroller && item) {
          var itemWidth = scroller.querySelector('li').clientWidth;
          BX.append(BX.create('div', {
            attrs: {
              className: styles["horizontal-scroll-buttons"]
            },
            children: [BX.create('button', {
              attrs: {
                type: "button"
              },
              text: "<",
              events: {
                click: function click() {
                  if (scroller.scrollLeft !== 0) {
                    scroller.scrollBy({
                      left: -itemWidth * 3,
                      top: 0,
                      behavior: 'smooth'
                    });
                  } else {
                    scroller.scrollTo({
                      left: scroller.scrollWidth,
                      top: 0,
                      behavior: 'smooth'
                    });
                  }
                }
              }
            }), BX.create('button', {
              attrs: {
                type: "button"
              },
              text: ">",
              events: {
                click: function click() {
                  if (scroller.scrollLeft < scroller.scrollWidth - itemWidth * 3 - 10) {
                    scroller.scrollBy({
                      left: itemWidth * 3,
                      top: 0,
                      behavior: 'smooth'
                    });
                  } else {
                    scroller.scrollTo({
                      left: 0,
                      top: 0,
                      behavior: 'smooth'
                    });
                  }
                }
              }
            })]
          }), scroller);
        }
      }
    }, {
      key: "getConfirmationBlock",
      value: function getConfirmationBlock() {
        var _this5 = this;

        var confirmWarningNode = BX.create('p', {
          attrs: {
            className: styles['appointment-warning-text']
          }
        });
        var confirmInputNode = BX.create('input', {
          attrs: {
            type: 'number',
            className: this.styles['appointment-form_input'],
            placeholder: BX.message("FIRSTBIT_JS_CONFIRM_CODE_MESSAGE"),
            required: 'true',
            autocomplete: 'new-password'
          },
          events: {
            input: function input(e) {
              var _e$target, _e$target$value;

              if (((_e$target = e.target) === null || _e$target === void 0 ? void 0 : (_e$target$value = _e$target.value) === null || _e$target$value === void 0 ? void 0 : _e$target$value.length) > 4) {
                e.target.value = e.target.value.substring(0, 4);
              }
            }
          }
        });
        var confirmSubmitBtn = BX.create('div', {
          attrs: {
            className: styles['appointment-form-button-wrapper']
          },
          children: [BX.create('button', {
            attrs: {
              className: styles['appointment-form-button'],
              type: 'button'
            },
            text: BX.message("FIRSTBIT_JS_SEND_BTN_TEXT"),
            events: {
              click: function click(e) {
                return _this5.application.verifyConfirmCode(confirmInputNode.value, confirmWarningNode, e.target);
              }
            }
          })]
        });
        var confirmRepeatBtn = BX.create('a', {
          attrs: {
            className: styles['appointment-form-button-link'],
            href: "#"
          }
        });
        var confirmWrapper = BX.create('div', {
          attrs: {
            id: this.application.selectors.confirmWrapperId,
            style: "width: 100%"
          },
          children: [BX.create('label', {
            attrs: {
              className: styles['appointment-form_input-wrapper']
            },
            children: [confirmInputNode]
          }), confirmWarningNode, confirmSubmitBtn, confirmRepeatBtn]
        });
        this.application.startCodeTimerActions(confirmRepeatBtn);
        return confirmWrapper;
      }
    }, {
      key: "createEmptySelectionNode",
      value: function createEmptySelectionNode(message) {
        return BX.create('span', {
          attrs: {
            className: styles["empty-selection-message"]
          },
          text: message
        });
      }
    }]);
    return Renderer;
  }();

  var TextInputNames = {
    name: "name",
    middleName: "middleName",
    surname: "surname",
    phone: "phone",
    email: "email",
    birthday: "birthday",
    comment: "comment"
  };

  // @disabled-flow

  function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }

  function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { babelHelpers.defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }

  function _createForOfIteratorHelper(o, allowArrayLike) { var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"]; if (!it) { if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it["return"] != null) it["return"](); } finally { if (didErr) throw err; } } }; }

  function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

  function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }
  var AppointmentSteps = /*#__PURE__*/function () {
    /**
     * AppointmentSteps constructor
     * @param params
     */
    function AppointmentSteps(params) {
      var _babelHelpers$defineP, _params$companyLogo, _params$customColors, _this$filledInputs$te, _this$filledInputs, _this$filledInputs$te2, _this$filledInputs$te3, _this$filledInputs2, _this$filledInputs2$t, _this$filledInputs$te4, _this$filledInputs3, _this$filledInputs3$t, _this$filledInputs$te5, _this$filledInputs4, _this$filledInputs4$t, _this$filledInputs$te6, _this$filledInputs5, _this$filledInputs5$t, _this$filledInputs$te7, _this$filledInputs6, _this$filledInputs6$t, _this$filledInputs$te8, _this$filledInputs7, _this$filledInputs7$t, _this$filledInputs$te9, _this$filledInputs8, _this$filledInputs8$t, _this$filledInputs9;

      babelHelpers.classCallCheck(this, AppointmentSteps);
      babelHelpers.defineProperty(this, "selectionStep", '');
      babelHelpers.defineProperty(this, "currentFormStep", null);
      babelHelpers.defineProperty(this, "formStepNodes", {
        one: null,
        two: null,
        userData: null
      });
      babelHelpers.defineProperty(this, "phoneMask", '+7(000)000-00-00');
      babelHelpers.defineProperty(this, "loaded", false);
      babelHelpers.defineProperty(this, "timeExpires", 0);
      babelHelpers.defineProperty(this, "requiredInputs", []);
      babelHelpers.defineProperty(this, "initParams", {});
      babelHelpers.defineProperty(this, "eventHandlersAdded", {});
      babelHelpers.defineProperty(this, "servicesStorage", {});
      babelHelpers.defineProperty(this, "dataKeys", {
        clinicsKey: "clinics",
        specialtiesKey: "specialties",
        employeesKey: "employees",
        servicesKey: "services",
        scheduleKey: "schedule"
      });
      babelHelpers.defineProperty(this, "data", (_babelHelpers$defineP = {}, babelHelpers.defineProperty(_babelHelpers$defineP, this.dataKeys.clinicsKey, []), babelHelpers.defineProperty(_babelHelpers$defineP, this.dataKeys.specialtiesKey, {}), babelHelpers.defineProperty(_babelHelpers$defineP, this.dataKeys.servicesKey, {}), babelHelpers.defineProperty(_babelHelpers$defineP, this.dataKeys.employeesKey, {}), babelHelpers.defineProperty(_babelHelpers$defineP, this.dataKeys.scheduleKey, []), _babelHelpers$defineP));
      babelHelpers.defineProperty(this, "orderData", {});
      babelHelpers.defineProperty(this, "selectionBlocks", {});
      babelHelpers.defineProperty(this, "selectionNodes", {});
      babelHelpers.defineProperty(this, "textNodes", {});
      babelHelpers.defineProperty(this, "defaultText", {});
      babelHelpers.defineProperty(this, "selectDoctorBeforeService", true);
      this.firstInit = true;
      this.initParams = params;
      this.selectors = this.getAppSelectors(styles);
      this.selectionSteps = Object.values(this.dataKeys);
      this.useServices = params.useServices === "Y";
      this.useTimeSteps = params.useTimeSteps === "Y";
      this.timeStepDurationMinutes = Number(params.timeStepDurationMinutes);
      this.strictCheckingOfRelations = params.strictCheckingOfRelations === "Y";
      this.showDoctorsWithoutDepartment = params.showDoctorsWithoutDepartment === "Y";
      this.confirmTypes = params.confirmTypes;
      this.useConfirmWith = params.useConfirmWith;
      this.useEmailNote = params.useEmailNote === "Y";
      this.companyLogo = (_params$companyLogo = params.companyLogo) !== null && _params$companyLogo !== void 0 ? _params$companyLogo : false;
      this.useCustomMainBtn = params.useCustomMainBtn === "Y" && params['customMainBtnId'];
      this.customColors = (_params$customColors = params.customColors) !== null && _params$customColors !== void 0 ? _params$customColors : {};
      this.filledInputs = (_this$filledInputs9 = {}, babelHelpers.defineProperty(_this$filledInputs9, this.dataKeys.clinicsKey, {
        clinicUid: false,
        clinicName: false
      }), babelHelpers.defineProperty(_this$filledInputs9, this.dataKeys.specialtiesKey, {
        specialty: false,
        specialtyUid: false
      }), babelHelpers.defineProperty(_this$filledInputs9, this.dataKeys.servicesKey, {
        serviceUid: false,
        serviceName: false,
        serviceDuration: false
      }), babelHelpers.defineProperty(_this$filledInputs9, this.dataKeys.employeesKey, {
        refUid: false,
        doctorName: false
      }), babelHelpers.defineProperty(_this$filledInputs9, this.dataKeys.scheduleKey, {
        orderDate: false,
        timeBegin: false,
        timeEnd: false
      }), babelHelpers.defineProperty(_this$filledInputs9, "textValues", {
        name: (_this$filledInputs$te = (_this$filledInputs = this.filledInputs) === null || _this$filledInputs === void 0 ? void 0 : (_this$filledInputs$te2 = _this$filledInputs.textValues) === null || _this$filledInputs$te2 === void 0 ? void 0 : _this$filledInputs$te2.name) !== null && _this$filledInputs$te !== void 0 ? _this$filledInputs$te : false,
        surname: (_this$filledInputs$te3 = (_this$filledInputs2 = this.filledInputs) === null || _this$filledInputs2 === void 0 ? void 0 : (_this$filledInputs2$t = _this$filledInputs2.textValues) === null || _this$filledInputs2$t === void 0 ? void 0 : _this$filledInputs2$t.surname) !== null && _this$filledInputs$te3 !== void 0 ? _this$filledInputs$te3 : false,
        middleName: (_this$filledInputs$te4 = (_this$filledInputs3 = this.filledInputs) === null || _this$filledInputs3 === void 0 ? void 0 : (_this$filledInputs3$t = _this$filledInputs3.textValues) === null || _this$filledInputs3$t === void 0 ? void 0 : _this$filledInputs3$t.middleName) !== null && _this$filledInputs$te4 !== void 0 ? _this$filledInputs$te4 : false,
        phone: (_this$filledInputs$te5 = (_this$filledInputs4 = this.filledInputs) === null || _this$filledInputs4 === void 0 ? void 0 : (_this$filledInputs4$t = _this$filledInputs4.textValues) === null || _this$filledInputs4$t === void 0 ? void 0 : _this$filledInputs4$t.phone) !== null && _this$filledInputs$te5 !== void 0 ? _this$filledInputs$te5 : false,
        address: (_this$filledInputs$te6 = (_this$filledInputs5 = this.filledInputs) === null || _this$filledInputs5 === void 0 ? void 0 : (_this$filledInputs5$t = _this$filledInputs5.textValues) === null || _this$filledInputs5$t === void 0 ? void 0 : _this$filledInputs5$t.address) !== null && _this$filledInputs$te6 !== void 0 ? _this$filledInputs$te6 : false,
        email: (_this$filledInputs$te7 = (_this$filledInputs6 = this.filledInputs) === null || _this$filledInputs6 === void 0 ? void 0 : (_this$filledInputs6$t = _this$filledInputs6.textValues) === null || _this$filledInputs6$t === void 0 ? void 0 : _this$filledInputs6$t.email) !== null && _this$filledInputs$te7 !== void 0 ? _this$filledInputs$te7 : false,
        birthday: (_this$filledInputs$te8 = (_this$filledInputs7 = this.filledInputs) === null || _this$filledInputs7 === void 0 ? void 0 : (_this$filledInputs7$t = _this$filledInputs7.textValues) === null || _this$filledInputs7$t === void 0 ? void 0 : _this$filledInputs7$t.birthday) !== null && _this$filledInputs$te8 !== void 0 ? _this$filledInputs$te8 : false,
        comment: (_this$filledInputs$te9 = (_this$filledInputs8 = this.filledInputs) === null || _this$filledInputs8 === void 0 ? void 0 : (_this$filledInputs8$t = _this$filledInputs8.textValues) === null || _this$filledInputs8$t === void 0 ? void 0 : _this$filledInputs8$t.comment) !== null && _this$filledInputs$te9 !== void 0 ? _this$filledInputs$te9 : false
      }), _this$filledInputs9);
      this.prepareSelectionBlocksForRender();
      this.renderer = new Renderer(styles, this);
    }
    /**
     * create js objects that contains html ids and default textContent for selection blocks
     * this objects will be used for creating selection blocks html
     */


    babelHelpers.createClass(AppointmentSteps, [{
      key: "prepareSelectionBlocksForRender",
      value: function prepareSelectionBlocksForRender() {
        var _this = this;

        this.selectionSteps.forEach(function (step) {
          _this.selectionBlocks[step] = {
            "blockId": "appointment_".concat(step, "_block"),
            "listId": "appointment_".concat(step, "_list"),
            "selectedId": "appointment_".concat(step, "_selected"),
            "inputId": "appointment_".concat(step, "_value"),
            "isRequired": !(step === _this.dataKeys.servicesKey && _this.initParams.useServices !== "Y")
          };
          _this.defaultText[step] = BX.message("FIRSTBIT_JS_APPOINTMENT_SELECT_".concat(step.toUpperCase(), "_TEXT"));
        });
      }
      /**
       * start application
       */

    }, {
      key: "run",
      value: function run() {
        this.checkRoot();
        this.insertAppHtml();
        this.init();
      }
      /**
       * check root selector and creates it if needed
       */

    }, {
      key: "checkRoot",
      value: function checkRoot() {
        if (!this.root || !BX.type.isDomNode(this.root)) {
          this.root = this.renderer.getRootElement();
          BX.append(this.root, document.body);
        } else {
          BX.cleanNode(this.root);
        }
      }
      /**
       * build basic html skeleton and insert it to DOM
       */

    }, {
      key: "insertAppHtml",
      value: function insertAppHtml() {
        BX.append(this.renderer.getAppHtmlSkeleton(), this.root);
        !this.useCustomMainBtn && BX.append(this.renderer.getDefaultStartBtn(), this.root);
      }
      /**
       * start all application actions
       */

    }, {
      key: "init",
      value: function init() {
        try {
          this.initCustomEvents();
          this.initFormStepNodes();
          this.initStartBtn();
          this.initBaseNodes();
          this.initOverlayAction();
          this.initForm();
          this.initMobileCloseBtn();
          this.initSelectionNodes();
          this.initTextNodes();
          this.addPhoneMasks();
          this.addCalendarSelection();
          this.addCustomColors();
        } catch (e) {
          this.logResultErrors(e);
        }
      }
      /**
       * subscribing on custom js events
       */

    }, {
      key: "initCustomEvents",
      value: function initCustomEvents() {
        var _this2 = this;

        EventManager.subscribe(EventManager.fullDataLoaded, function () {
          _this2.loaded = true;

          try {
            _this2.renderBlock(_this2.dataKeys.clinicsKey);
          } catch (e) {
            _this2.logResultErrors(e);
          }
        });
        EventManager.subscribe(EventManager.clinicsRendered, function () {
          _this2.createSpecialtiesList();

          _this2.toggleLoader(false);
        });
        EventManager.subscribe(EventManager.formStepChanged, function (e) {
          e.data.isBack ? _this2.selectionStep = _this2.dataKeys.specialtiesKey : void 0;

          _this2.changeFormStepActions(e.data);

          _this2.activateSelectionNodes();
        });
      }
    }, {
      key: "initFormStepNodes",
      value: function initFormStepNodes() {
        this.currentFormStep = this.formStepNodes.one = BX(this.selectors.formStepIds.one);
        this.formStepNodes.two = BX(this.selectors.formStepIds.two);
        this.formStepNodes.userData = BX(this.selectors.formStepIds.userData);
      }
      /**
       * find or create start button and add event handler for click
       */

    }, {
      key: "initStartBtn",
      value: function initStartBtn() {
        if (!this.firstInit && this.useCustomMainBtn) {
          return;
        }

        var startBtnId = this.useCustomMainBtn ? this.initParams['customMainBtnId'] : this.selectors.startBtnId;
        this.startBtn = BX(startBtnId);

        if (BX.type.isDomNode(this.startBtn)) {
          EventManager.bind(this.startBtn, 'click', this.togglePopup.bind(this));
        } else {
          throw new Error("".concat(BX.message('FIRSTBIT_JS_NODE_NOT_FOUND'), " \"").concat(this.initParams['customMainBtnId'], "\""));
        }
      }
      /**
       * find all base nodes and save them to this object props
       */

    }, {
      key: "initBaseNodes",
      value: function initBaseNodes() {
        this.overlay = BX(this.selectors.overlayId);
        this.startBtnWrap = BX(this.selectors.startBtnWrapId);
        this.mobileCloseBtn = BX(this.selectors.mobileCloseBtnId);
        this.messageNode = BX(this.selectors.messageNodeId);
        this.submitBtn = BX(this.selectors.submitBtnId);
        this.resultBlock = BX(this.selectors.appResultBlockId);
      }
      /**
       * make popup hidden by click to overlay
       */

    }, {
      key: "initOverlayAction",
      value: function initOverlayAction() {
        var _this3 = this;

        if (BX.type.isDomNode(this.overlay)) {
          EventManager.bind(this.overlay, 'click', function (e) {
            var _e$target;

            if (((_e$target = e.target) === null || _e$target === void 0 ? void 0 : _e$target.getAttribute('id')) === _this3.selectors.overlayId) {
              _this3.togglePopup();
            }
          });
        }
      }
      /**
       * find form node and add event listeners
       */

    }, {
      key: "initForm",
      value: function initForm() {
        this.form = BX(this.selectors.formId);

        if (this.form) {
          EventManager.bind(this.form, 'submit', this.submit.bind(this));
        } else {
          throw new Error("".concat(BX.message('FIRSTBIT_JS_NODE_NOT_FOUND'), " ").concat(this.selectors.formId));
        }
      }
      /**
       * find node and add event listener to close form
       */

    }, {
      key: "initMobileCloseBtn",
      value: function initMobileCloseBtn() {
        if (this.mobileCloseBtn) {
          EventManager.bind(this.mobileCloseBtn, 'click', this.togglePopup.bind(this));
        } else {
          throw new Error("".concat(BX.message('FIRSTBIT_JS_NODE_NOT_FOUND'), " ").concat(this.selectors.mobileCloseBtnId));
        }
      }
      /**
       * find nodes and save their data to this object
       */

    }, {
      key: "initSelectionNodes",
      value: function initSelectionNodes() {
        for (var key in this.selectionBlocks) {
          this.selectionNodes[key] = {
            blockNode: BX(this.selectionBlocks[key].blockId),
            listNode: BX(this.selectionBlocks[key].listId),
            selectedNode: BX(this.selectionBlocks[key].selectedId),
            inputNode: BX(this.selectionBlocks[key].inputId)
          };

          if (this.selectionBlocks[key].isRequired) {
            this.requiredInputs.push(this.selectionNodes[key].inputNode);
          }
        }
      }
      /**
       * find nodes, add actions and save their data to this object
       */

    }, {
      key: "initTextNodes",
      value: function initTextNodes() {
        var _this4 = this;

        this.initParams['textBlocks'].forEach(function (block) {
          var input = BX(block.id);

          if (!input) {
            throw new Error("".concat(BX.message("FIRSTBIT_JS_NODE_NOT_FOUND"), " ").concat(block.id));
          }

          var currentValue = _this4.filledInputs.textValues[block.name];
          input.value = currentValue ? currentValue : '';

          if (input && currentValue && block.name === TextInputNames.birthday) {
            var date$$1 = new Date(currentValue);
            input.value = _this4.convertDateToDisplay(date$$1.getTime(), false, true);
          }

          EventManager.bind(input, 'input', function (e) {
            var _e$target$value;

            var val = (_e$target$value = e.target.value) !== null && _e$target$value !== void 0 ? _e$target$value : '';

            if (e.target.name === TextInputNames.phone && val.length > _this4.phoneMask.length) {
              val = val.substring(0, _this4.phoneMask.length);
            }

            _this4.filledInputs.textValues[block.name] = val;
          });

          if (block["data-required"] === "true") {
            _this4.requiredInputs.push(input);
          } else {
            if (_this4.useConfirmWith === _this4.confirmTypes.email && block.name === TextInputNames.email) {
              _this4.requiredInputs.push(input);
            }
          }

          _this4.textNodes[block.name] = {
            inputNode: input
          };
        });
      }
      /**
       * loading data from 1c and build selectors html
       */

    }, {
      key: "start",
      value: function start() {
        this.toggleLoader(true);
        this.loadData();
      }
      /**
       * sequentially loads data from 1c
       */

    }, {
      key: "loadData",
      value: function loadData() {
        var _this5 = this;

        this.getListClinic().then(function (clinicsResponse) {
          var _clinicsResponse$data, _clinicsResponse$data3;

          if ((_clinicsResponse$data = clinicsResponse.data) !== null && _clinicsResponse$data !== void 0 && _clinicsResponse$data.error) {
            var _clinicsResponse$data2;

            throw new Error((_clinicsResponse$data2 = clinicsResponse.data) === null || _clinicsResponse$data2 === void 0 ? void 0 : _clinicsResponse$data2.error);
          } else if (((_clinicsResponse$data3 = clinicsResponse.data) === null || _clinicsResponse$data3 === void 0 ? void 0 : _clinicsResponse$data3.length) === 0) {
            throw new Error(BX.message("FIRSTBIT_JS_CLINICS_NOT_FOUND_ERROR"));
          } else {
            _this5.data.clinics = clinicsResponse.data;
            return _this5.getListEmployees();
          }
        }).then(function (employeesResponse) {
          var _employeesResponse$da;

          if ((_employeesResponse$da = employeesResponse.data) !== null && _employeesResponse$da !== void 0 && _employeesResponse$da.error) {
            var _employeesResponse$da2;

            throw new Error((_employeesResponse$da2 = employeesResponse.data) === null || _employeesResponse$da2 === void 0 ? void 0 : _employeesResponse$da2.error);
          } else if (Object.keys(employeesResponse.data).length === 0) {
            throw new Error(BX.message("FIRSTBIT_JS_DOCTORS_NOT_FOUND_ERROR"));
          } else {
            _this5.data.employees = employeesResponse.data;
            return _this5.getSchedule();
          }
        }).then(function (scheduleResponse) {
          var _scheduleResponse$dat, _scheduleResponse$dat3;

          if ((_scheduleResponse$dat = scheduleResponse.data) !== null && _scheduleResponse$dat !== void 0 && _scheduleResponse$dat.error) {
            var _scheduleResponse$dat2;

            throw new Error((_scheduleResponse$dat2 = scheduleResponse.data) === null || _scheduleResponse$dat2 === void 0 ? void 0 : _scheduleResponse$dat2.error);
          }

          if ((_scheduleResponse$dat3 = scheduleResponse.data) !== null && _scheduleResponse$dat3 !== void 0 && _scheduleResponse$dat3.hasOwnProperty("schedule")) {
            _this5.data.schedule = scheduleResponse.data.schedule;
            EventManager.emit(EventManager.fullDataLoaded);
          }
        })["catch"](function (e) {
          !_this5.useCustomMainBtn && _this5.startBtnWrap.classList.add(styles['hidden']);

          _this5.logResultErrors(e);
        });
      }
      /**
       * Load clinics list from 1c
       * @returns {Promise<any>}
       */

    }, {
      key: "getListClinic",
      value: function getListClinic() {
        return BX.ajax.runAction('firstbit:appointment.oneCController.getClinics', {
          data: {
            sessid: BX.bitrix_sessid()
          }
        });
      }
      /**
       * Load employees list from 1c
       * @returns {Promise<any>}
       */

    }, {
      key: "getListEmployees",
      value: function getListEmployees() {
        return BX.ajax.runAction('firstbit:appointment.oneCController.getEmployees', {
          data: {
            sessid: BX.bitrix_sessid()
          }
        });
      }
      /**
       * Load doctor's schedule from 1c
       * @returns {Promise<any>}
       */

    }, {
      key: "getSchedule",
      value: function getSchedule() {
        return BX.ajax.runAction('firstbit:appointment.oneCController.getSchedule', {
          data: {
            sessid: BX.bitrix_sessid()
          }
        });
      }
      /**
       * Load nomenclature list from 1c
       * @param clinicGuid
       * @returns {Promise<any>}
       */

    }, {
      key: "getListNomenclature",
      value: function getListNomenclature(clinicGuid) {
        return BX.ajax.runAction('firstbit:appointment.oneCController.getNomenclature', {
          data: {
            sessid: BX.bitrix_sessid(),
            clinicGuid: clinicGuid
          }
        });
      }
    }, {
      key: "renderSpecialtiesList",
      value: function renderSpecialtiesList() {
        this.renderBlock(this.dataKeys.specialtiesKey);
      }
    }, {
      key: "renderServicesList",
      value: function renderServicesList() {
        this.renderBlock(this.dataKeys.servicesKey);
      }
    }, {
      key: "renderEmployeesList",
      value: function renderEmployeesList() {
        this.renderBlock(this.dataKeys.employeesKey);
      }
    }, {
      key: "renderScheduleList",
      value: function renderScheduleList() {
        this.renderBlock(this.dataKeys.scheduleKey);
      }
    }, {
      key: "renderBlock",
      value: function renderBlock(dataKey) {
        var _this$selectionNodes$;

        var listNode = (_this$selectionNodes$ = this.selectionNodes[dataKey]) === null || _this$selectionNodes$ === void 0 ? void 0 : _this$selectionNodes$.listNode;

        if (!listNode) {
          throw new Error(BX.message("FIRSTBIT_JS_".concat(dataKey.toUpperCase(), "_NODE_NOT_FOUND_ERROR")));
        }

        dataKey === this.dataKeys.scheduleKey ? listNode.classList.add(styles["column-mode"]) : void 0;
        BX.cleanNode(listNode);

        if (Object.keys(this.data[dataKey]).length > 0) {
          var items = this.data[dataKey];
          this.renderer.renderSelectionItems(listNode, dataKey, items);
          dataKey === this.dataKeys.clinicsKey ? EventManager.emit(EventManager.clinicsRendered) : void 0;
        }
      }
    }, {
      key: "allowToRender",
      value: function allowToRender(listNode, dataKey, item) {
        var canRender = true;
        var selectedClinic = this.filledInputs[this.dataKeys.clinicsKey].clinicUid;
        var selectedSpecialtyUid = this.filledInputs[this.dataKeys.specialtiesKey].specialtyUid;
        var selectedEmployeeUid = this.filledInputs[this.dataKeys.employeesKey].refUid;
        var selectedServiceUid = this.filledInputs[this.dataKeys.servicesKey].serviceUid;
        var clinicCondition, specialtyCondition;

        switch (dataKey) {
          case this.dataKeys.specialtiesKey:
            var alreadyRendered = listNode.querySelector("[data-uid=\"".concat(item.uid, "\"]"));
            clinicCondition = item.clinics.includes(selectedClinic);

            if (this.strictCheckingOfRelations) {
              canRender = clinicCondition;

              if (this.showDoctorsWithoutDepartment) {
                canRender = clinicCondition || item.clinics.includes('');
              }
            }

            canRender = canRender && BX.type.isNotEmptyString(item.name) && !alreadyRendered;
            break;

          case this.dataKeys.employeesKey:
            specialtyCondition = item.specialtyUid === selectedSpecialtyUid;
            clinicCondition = selectedClinic === item.clinicUid;
            canRender = specialtyCondition;

            if (this.strictCheckingOfRelations) {
              if (this.showDoctorsWithoutDepartment) {
                canRender = specialtyCondition && !item.clinicUid || specialtyCondition && clinicCondition;
              } else {
                canRender = specialtyCondition && clinicCondition;
              }
            }

            if (this.useServices && !this.selectDoctorBeforeService) {
              canRender = canRender && item.services.hasOwnProperty(selectedServiceUid);
            }

            break;

          case this.dataKeys.servicesKey:
            canRender = selectedSpecialtyUid === item.specialtyUid;

            if (this.selectDoctorBeforeService) {
              canRender = canRender && this.data.employees[selectedEmployeeUid].services.hasOwnProperty(item.uid);
            }

            break;

          case this.dataKeys.scheduleKey:
            canRender = item.clinicUid === selectedClinic && item.refUid === selectedEmployeeUid;
            break;

          default:
            break;
        }

        return canRender;
      }
    }, {
      key: "getServiceDuration",
      value: function getServiceDuration(scheduleItem) {
        var selectedEmployee = this.data.employees[scheduleItem.refUid];
        var selectedService = this.filledInputs[this.dataKeys.servicesKey];
        var serviceDuration = Number(selectedService.serviceDuration);

        if (selectedEmployee.services.hasOwnProperty(selectedService.serviceUid)) {
          if (selectedEmployee.services[selectedService.serviceUid].hasOwnProperty("personalDuration")) {
            var personalDuration = selectedEmployee.services[selectedService.serviceUid]["personalDuration"];
            serviceDuration = Number(personalDuration) > 0 ? Number(personalDuration) : serviceDuration;
          }
        }

        return serviceDuration;
      }
    }, {
      key: "getIntervalsForServiceDuration",
      value: function getIntervalsForServiceDuration(intervals, serviceDurationMs) {
        var _this6 = this;

        var newIntervals = [];
        intervals.length && intervals.forEach(function (day) {
          var timestampTimeBegin = new Date(day.timeBegin).getTime();
          var timestampTimeEnd = new Date(day.timeEnd).getTime();
          var timeDifference = timestampTimeEnd - timestampTimeBegin;
          var appointmentsCount = Math.floor(timeDifference / serviceDurationMs);

          if (appointmentsCount > 0) {
            if (_this6.useTimeSteps && serviceDurationMs >= 30 * 60 * 1000) //use timeSteps only for services with duration>=30 minutes
              {
                var start = new Date(timestampTimeBegin);
                var end = new Date(timestampTimeBegin + serviceDurationMs);

                while (end.getTime() <= timestampTimeEnd) {
                  newIntervals.push({
                    "date": day.date,
                    "timeBegin": _this6.convertDateToISO(Number(start)),
                    "timeEnd": _this6.convertDateToISO(Number(end)),
                    "formattedDate": _this6.convertDateToDisplay(Number(start), false),
                    "formattedTimeBegin": _this6.convertDateToDisplay(Number(start), true),
                    "formattedTimeEnd": _this6.convertDateToDisplay(Number(end), true)
                  });
                  start.setMinutes(start.getMinutes() + _this6.timeStepDurationMinutes);
                  end.setMinutes(end.getMinutes() + _this6.timeStepDurationMinutes);
                }
              } else {
              for (var i = 0; i < appointmentsCount; i++) {
                var _start = Number(new Date(timestampTimeBegin + serviceDurationMs * i));

                var _end = Number(new Date(timestampTimeBegin + serviceDurationMs * (i + 1)));

                newIntervals.push({
                  "date": day.date,
                  "timeBegin": _this6.convertDateToISO(_start),
                  "timeEnd": _this6.convertDateToISO(_end),
                  "formattedDate": _this6.convertDateToDisplay(_start, false),
                  "formattedTimeBegin": _this6.convertDateToDisplay(_start, true),
                  "formattedTimeEnd": _this6.convertDateToDisplay(_end, true)
                });
              }
            }
          }
        });
        return newIntervals;
      }
    }, {
      key: "toggleSelectionList",
      value: function toggleSelectionList(dataKey, selected, list) {
        list.classList.toggle(styles['active']);

        for (var nodesKey in this.selectionNodes) {
          if (this.selectionNodes.hasOwnProperty(nodesKey) && nodesKey !== dataKey) {
            var _this$selectionNodes$2, _this$selectionNodes$3;

            (_this$selectionNodes$2 = this.selectionNodes[nodesKey]) === null || _this$selectionNodes$2 === void 0 ? void 0 : (_this$selectionNodes$3 = _this$selectionNodes$2.listNode) === null || _this$selectionNodes$3 === void 0 ? void 0 : _this$selectionNodes$3.classList.remove(styles['active']);
          }
        }
      }
    }, {
      key: "addItemActions",
      value: function addItemActions(dataKey) {
        var _this7 = this;

        var items = this.selectionNodes[dataKey].listNode.children;

        if (!items.length) {
          return;
        }

        var _iterator = _createForOfIteratorHelper(items),
            _step;

        try {
          for (_iterator.s(); !(_step = _iterator.n()).done;) {
            var item = _step.value;

            if (dataKey === this.dataKeys.scheduleKey) {
              var times = item.querySelectorAll('span');
              times.length && times.forEach(function (time) {
                time.addEventListener('click', function (e) {
                  e.stopPropagation();

                  _this7.selectionNodes[dataKey].listNode.classList.remove(styles['active']);

                  _this7.selectionNodes[dataKey].selectedNode.innerHTML = "\n                            <span>\n                                ".concat(e.currentTarget.dataset.displayDate, " - \n                                ").concat(e.currentTarget.textContent, "\n                            </span>\n                        ");

                  _this7.changeSelectionStep(dataKey, e.currentTarget);

                  _this7.activateSelectionNodes();
                });
              });
            } else {
              item.addEventListener('click', function (e) {
                e.stopPropagation();

                _this7.selectionNodes[dataKey].listNode.classList.remove(styles['active']);

                _this7.selectionNodes[dataKey].selectedNode.innerHTML = "<span>".concat(e.currentTarget.textContent, "</span>");

                _this7.changeSelectionStep(dataKey, e.currentTarget);

                if (dataKey !== _this7.dataKeys.specialtiesKey) {
                  _this7.activateSelectionNodes();
                } else {
                  _this7.activateStepButtons();
                }
              });
            }
          }
        } catch (err) {
          _iterator.e(err);
        } finally {
          _iterator.f();
        }
      }
    }, {
      key: "changeSelectionStep",
      value: function changeSelectionStep(dataKey, target) {
        var _this8 = this;

        this.selectionNodes[dataKey].inputNode.value = target.dataset.uid;

        switch (dataKey) {
          case this.dataKeys.clinicsKey:
            var clinicUid = target.dataset.uid;
            this.filledInputs[dataKey].clinicUid = clinicUid;
            this.filledInputs[dataKey].clinicName = target.dataset.name;

            if (this.useServices) {
              if (this.servicesStorage[clinicUid]) {
                this.data.services = _objectSpread({}, this.servicesStorage[clinicUid]);
                this.renderSpecialtiesList();
              } else {
                this.toggleLoader(true);
                this.getListNomenclature("".concat(clinicUid)).then(function (nomenclature) {
                  var _nomenclature$data;

                  if ((_nomenclature$data = nomenclature.data) !== null && _nomenclature$data !== void 0 && _nomenclature$data.error) {
                    throw new Error(nomenclature.data.error);
                  } else {
                    if (Object.keys(nomenclature.data).length > 0) {
                      _this8.data.services = nomenclature.data;

                      _this8.bindServicesToSpecialties();

                      _this8.renderSpecialtiesList();

                      _this8.servicesStorage[clinicUid] = _objectSpread({}, _this8.data.services);
                    }
                  }

                  _this8.toggleLoader(false);
                })["catch"](function (res) {
                  _this8.logResultErrors(res);
                });
              }
            } else {
              this.renderSpecialtiesList();
            }

            break;

          case this.dataKeys.specialtiesKey:
            this.filledInputs[dataKey].specialty = target.dataset.name;
            this.filledInputs[dataKey].specialtyUid = target.dataset.uid;
            break;

          case this.dataKeys.servicesKey:
            this.filledInputs[dataKey].serviceName = target.textContent;
            this.filledInputs[dataKey].serviceUid = target.dataset.uid;
            this.filledInputs[dataKey].serviceDuration = target.dataset.duration;
            this.selectDoctorBeforeService ? this.renderScheduleList() : this.renderEmployeesList();
            break;

          case this.dataKeys.employeesKey:
            this.filledInputs[dataKey].doctorName = target.textContent;
            this.filledInputs[dataKey].refUid = target.dataset.uid;

            if (this.useServices) {
              if (this.selectDoctorBeforeService) {
                this.renderServicesList();
              } else {
                this.renderScheduleList();
              }
            } else {
              this.renderScheduleList();
            }

            break;

          case this.dataKeys.scheduleKey:
            this.filledInputs[dataKey].orderDate = target.dataset.date;
            this.filledInputs[dataKey].timeBegin = target.dataset.start;
            this.filledInputs[dataKey].timeEnd = target.dataset.end;
            this.selectionNodes[dataKey].inputNode.value = target.dataset.date;
            break;

          default:
            break;
        }

        this.selectionStep = dataKey;
      }
    }, {
      key: "bindServicesToSpecialties",
      value: function bindServicesToSpecialties() {
        var services = this.data.services;
        var employees = this.data.employees;

        if (Object.keys(employees).length > 0) {
          for (var employeeUid in employees) {
            if (!employees.hasOwnProperty(employeeUid)) {
              return;
            }

            var empServices = employees[employeeUid].services;
            var specialty = employees[employeeUid].specialty;

            if (specialty) {
              if (empServices && Object.keys(empServices).length > 0) {
                for (var empServiceUid in empServices) {
                  if (!empServices.hasOwnProperty(empServiceUid)) {
                    return;
                  }

                  if (services.hasOwnProperty(empServiceUid)) {
                    services[empServiceUid].specialtyUid = employees[employeeUid].specialtyUid;
                  }
                }
              }
            }
          }
        }
      }
    }, {
      key: "createSpecialtiesList",
      value: function createSpecialtiesList() {
        var employees = this.data.employees;

        if (Object.keys(employees).length > 0) {
          for (var uid in employees) {
            if (employees.hasOwnProperty(uid)) {
              var specialty = employees[uid].specialty;
              specialty && this.addSpecialty(employees[uid]);
            }
          }
        }
      }
    }, {
      key: "addSpecialty",
      value: function addSpecialty(employee) {
        if (this.data[this.dataKeys.specialtiesKey][employee.specialtyUid]) {
          this.addClinicToSpecialty(this.data[this.dataKeys.specialtiesKey][employee.specialtyUid], employee.clinicUid);
        } else {
          this.data[this.dataKeys.specialtiesKey][employee.specialtyUid] = {
            uid: employee.specialtyUid,
            name: employee.specialty,
            clinics: [employee.clinicUid]
          };
        }
      }
    }, {
      key: "addClinicToSpecialty",
      value: function addClinicToSpecialty(specialty, clinicUid) {
        if (Array.isArray(specialty.clinics) && !specialty.clinics.includes(clinicUid)) {
          specialty.clinics.push(clinicUid);
        }
      }
    }, {
      key: "activateSelectionNodes",
      value: function activateSelectionNodes() {
        var _this9 = this;

        var current = false;
        var next = false;
        this.selectionSteps.forEach(function (nodesKey) {
          if (!_this9.useServices && nodesKey === _this9.dataKeys.servicesKey) {
            return;
          }

          console.log(nodesKey, current, next);

          if (_this9.selectionNodes.hasOwnProperty(nodesKey)) {
            var block = _this9.selectionNodes[nodesKey].blockNode;

            if (!current && !next) {
              block.classList.remove(styles["disabled"]);
            } else if (current && !next) {
              block.classList.remove(styles["disabled"]);

              _this9.resetValue(nodesKey);
            } else {
              block.classList.add(styles["disabled"]);

              _this9.resetValue(nodesKey);
            }

            next = current;

            if (nodesKey === _this9.selectionStep) {
              current = true;
              var selectedSpecialty = _this9.filledInputs[_this9.dataKeys.specialtiesKey].specialtyUid;
              var selectedDate = _this9.filledInputs[_this9.dataKeys.scheduleKey].orderDate;
              var specialtyCondition = nodesKey === _this9.dataKeys.specialtiesKey && selectedSpecialty;
              var dateCondition = nodesKey === _this9.dataKeys.scheduleKey && selectedDate;

              if (_this9.currentFormStep === _this9.formStepNodes.one && specialtyCondition || _this9.currentFormStep === _this9.formStepNodes.two && dateCondition) {
                _this9.activateStepButtons();
              } else {
                _this9.deactivateStepButtons();
              }
            }
          }
        });
      }
    }, {
      key: "resetValue",
      value: function resetValue(nodesKey) {
        this.selectionNodes[nodesKey].selectedNode.textContent = this.defaultText[nodesKey];
        this.selectionNodes[nodesKey].inputNode.value = "";

        if (this.filledInputs.hasOwnProperty(nodesKey)) {
          for (var propKey in this.filledInputs[nodesKey]) {
            if (this.filledInputs[nodesKey].hasOwnProperty(propKey)) {
              this.filledInputs[nodesKey][propKey] = false;
            }
          }
        }
      }
    }, {
      key: "setSelectionDoctorBeforeService",
      value: function setSelectionDoctorBeforeService(value) {
        if (this.filledInputs[this.dataKeys.specialtiesKey].specialty !== false) {
          this.resetValue(this.dataKeys.employeesKey);
          this.selectDoctorBeforeService = value;

          if (this.useServices) {
            this.resetValue(this.dataKeys.servicesKey);

            if (value === true) {
              BX.insertBefore(this.selectionNodes[this.dataKeys.employeesKey].blockNode, this.selectionNodes[this.dataKeys.servicesKey].blockNode);
              this.selectionSteps[3] = this.dataKeys.servicesKey;
              this.selectionSteps[2] = this.dataKeys.employeesKey;
              this.renderEmployeesList();
            } else {
              BX.insertBefore(this.selectionNodes[this.dataKeys.servicesKey].blockNode, this.selectionNodes[this.dataKeys.employeesKey].blockNode);
              this.selectionSteps[2] = this.dataKeys.servicesKey;
              this.selectionSteps[3] = this.dataKeys.employeesKey;
              this.renderServicesList();
            }
          } else {
            this.renderEmployeesList();
          }

          EventManager.emit(EventManager.formStepChanged, new main_core.Event.BaseEvent({
            data: {
              previousStep: this.formStepNodes.one,
              newStep: this.formStepNodes.two
            }
          }));
        } else {
          this.checkRequiredFields();
        }
      }
    }, {
      key: "changeFormStep",
      value: function changeFormStep(nextStep) {
        var isBack = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
        EventManager.emit(EventManager.formStepChanged, new main_core.Event.BaseEvent({
          data: {
            previousStep: this.currentFormStep,
            newStep: nextStep,
            isBack: isBack
          }
        }));
      }
    }, {
      key: "changeFormStepActions",
      value: function changeFormStepActions(data) {
        if (BX.type.isDomNode(data === null || data === void 0 ? void 0 : data.newStep)) {
          this.currentFormStep = data.newStep;
          data.newStep.classList.remove(styles['hidden']);
        }

        if (BX.type.isDomNode(data === null || data === void 0 ? void 0 : data.previousStep)) {
          data.previousStep.classList.add(styles['hidden']);
        }
      }
    }, {
      key: "activateStepButtons",
      value: function activateStepButtons() {
        if (BX.type.isDomNode(this.currentFormStep)) {
          var buttons = this.currentFormStep.querySelectorAll(".".concat(styles['appointment-form-button'], ":not([data-readonly=\"Y\"])"));
          buttons.length && buttons.forEach(function (button) {
            return button.removeAttribute('disabled');
          });
        }
      }
    }, {
      key: "deactivateStepButtons",
      value: function deactivateStepButtons() {
        if (BX.type.isDomNode(this.currentFormStep)) {
          var buttons = this.currentFormStep.querySelectorAll(".".concat(styles['appointment-form-button'], ":not([data-readonly=\"Y\"])"));
          buttons.length && buttons.forEach(function (button) {
            return button.setAttribute('disabled', true);
          });
        }
      }
    }, {
      key: "submit",
      value: function submit(event) {
        event.preventDefault();

        if (this.checkRequiredFields()) {
          this.messageNode ? this.messageNode.textContent = "" : void 0;
          this.toggleLoader(true);
          this.orderData = _objectSpread({}, this.filledInputs.textValues);

          for (var key in this.selectionNodes) {
            if (!this.useServices && key === this.dataKeys.servicesKey) {
              continue;
            }

            if (this.selectionNodes.hasOwnProperty(key) && this.filledInputs.hasOwnProperty(key)) {
              this.selectionNodes[key].inputNode.value = JSON.stringify(this.filledInputs[key]);
              this.orderData = _objectSpread(_objectSpread({}, this.orderData), this.filledInputs[key]);
            }
          }

          if (this.useConfirmWith !== this.confirmTypes.none) {
            this.sendConfirmCode();
          } else {
            this.sendOrder();
          }
        } else {
          if (this.messageNode) {
            this.messageNode.textContent = BX.message("FIRSTBIT_JS_ORDER_CHECK_FIELDS_ERROR");
          } else {
            this.logResultErrors(BX.message("FIRSTBIT_JS_ORDER_CHECK_FIELDS_ERROR"));
          }
        }
      }
    }, {
      key: "sendConfirmCode",
      value: function sendConfirmCode() {
        var _this10 = this;

        var event = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
        event && event.preventDefault();
        this.messageNode.textContent = "";
        BX.ajax.runAction('firstbit:appointment.messageController.sendConfirmCode', {
          data: {
            phone: this.orderData.phone,
            email: this.orderData.email,
            sessid: BX.bitrix_sessid()
          }
        }).then(function (result) {
          var _result$data$timeExpi, _result$data;

          _this10.timeExpires = (_result$data$timeExpi = (_result$data = result.data) === null || _result$data === void 0 ? void 0 : _result$data.timeExpires) !== null && _result$data$timeExpi !== void 0 ? _result$data$timeExpi : (new Date().getTime() / 1000).toFixed(0) + 60;

          _this10.createConfirmationForm();

          _this10.toggleLoader(false);
        })["catch"](function (result) {
          var _result$errors, _result$errors$;

          _this10.messageNode.textContent = ((_result$errors = result.errors) === null || _result$errors === void 0 ? void 0 : (_result$errors$ = _result$errors[0]) === null || _result$errors$ === void 0 ? void 0 : _result$errors$.message) + BX.message("FIRSTBIT_JS_SOME_DISPLAY_ERROR_POSTFIX");

          _this10.logResultErrors(result);

          _this10.toggleLoader(false);
        });
      }
    }, {
      key: "createConfirmationForm",
      value: function createConfirmationForm() {
        this.confirmWrapper && this.confirmWrapper.remove();
        this.confirmWrapper = this.renderer.getConfirmationBlock();
        this.form.classList.add(styles['appointment-form-confirmation-mode']);
        BX.append(this.confirmWrapper, this.form);
      }
    }, {
      key: "verifyConfirmCode",
      value: function verifyConfirmCode(code, confirmWarningNode, btnNode) {
        var _this11 = this;

        if (confirmWarningNode && btnNode) {
          confirmWarningNode.textContent = '';

          if ((code === null || code === void 0 ? void 0 : code.length) === 4) {
            btnNode.classList.add(styles['loading']);
            BX.ajax.runAction('firstbit:appointment.messageController.verifyConfirmCode', {
              data: {
                code: code,
                email: this.orderData.email,
                sessid: BX.bitrix_sessid()
              }
            }).then(function () {
              return _this11.sendOrder();
            })["catch"](function (result) {
              var _result$errors2;

              btnNode.classList.remove(styles['loading']);

              if (((_result$errors2 = result.errors) === null || _result$errors2 === void 0 ? void 0 : _result$errors2.length) > 0) {
                result.errors.forEach(function (error) {
                  confirmWarningNode.innerHTML = Number(error.code) === 400 || Number(error.code) === 406 || Number(error.code) === 425 ? "".concat(confirmWarningNode.innerHTML).concat(error.message, "<br>") : BX.message("FIRSTBIT_JS_APPLICATION_ERROR");
                });
              }
            });
          } else {
            confirmWarningNode.textContent = BX.message("FIRSTBIT_JS_CONFIRM_CODE_LENGTH");
          }
        }
      }
    }, {
      key: "sendOrder",
      value: function sendOrder() {
        var _this12 = this;

        BX.ajax.runAction('firstbit:appointment.oneCController.addOrder', {
          data: {
            params: JSON.stringify(this.orderData),
            sessid: BX.bitrix_sessid()
          }
        }).then(function (result) {
          var _result$data2;

          _this12.destroyConfirmationForm();

          _this12.toggleLoader(false);

          if ((_result$data2 = result.data) !== null && _result$data2 !== void 0 && _result$data2.error) {
            throw new Error(result.data.error);
          } else {
            if (_this12.useEmailNote && _this12.orderData.email) {
              _this12.sendEmailNote();
            }

            _this12.finalizingWidget(true);
          }
        })["catch"](function (result) {
          _this12.destroyConfirmationForm();

          _this12.toggleLoader(false);

          _this12.logResultErrors(result);

          _this12.finalizingWidget(false);
        });
      }
    }, {
      key: "sendEmailNote",
      value: function sendEmailNote() {
        BX.ajax.runAction('firstbit:appointment.messageController.sendEmailNote', {
          data: {
            params: JSON.stringify(this.orderData),
            sessid: BX.bitrix_sessid()
          }
        }).then()["catch"]();
      }
    }, {
      key: "startCodeTimerActions",
      value: function startCodeTimerActions(confirmRepeatBtn) {
        var _this13 = this;

        var curTimeSeconds = Number((new Date().getTime() / 1000).toFixed(0));
        var remainingTime = this.timeExpires - curTimeSeconds;
        var interval = setInterval(function () {
          if (remainingTime <= 0) {
            EventManager.bind(confirmRepeatBtn, 'click', _this13.sendConfirmCode.bind(_this13));
            clearInterval(interval);
          } else {
            remainingTime--;
            confirmRepeatBtn.textContent = "".concat(BX.message("FIRSTBIT_JS_CONFIRM_CODE_SEND_AGAIN"), " \n                                                ").concat(remainingTime > 0 ? remainingTime : '');
          }
        }, 1000);
      }
    }, {
      key: "destroyConfirmationForm",
      value: function destroyConfirmationForm() {
        this.confirmWrapper && this.confirmWrapper.remove();
        this.form.classList.remove(styles['appointment-form-confirmation-mode']);
      }
    }, {
      key: "finalizingWidget",
      value: function finalizingWidget(success) {
        var _this14 = this;

        this.resultBlock.classList.add(styles['active']);
        this.form.classList.add(styles['off']);
        var resTextNode = this.resultBlock.querySelector('p');

        if (resTextNode) {
          if (success) {
            var date$$1 = this.convertDateToDisplay(this.orderData.timeBegin, false);
            var time = this.convertDateToDisplay(this.orderData.timeBegin, true);
            var doctor = this.orderData.doctorName;
            resTextNode.innerHTML = "".concat(BX.message("FIRSTBIT_JS_APPOINTMENT_SUCCESS"), "\n                                         <br>").concat(date$$1, " ").concat(time, "\n                                         <br>").concat(BX.message("FIRSTBIT_JS_APPOINTMENT_DOCTOR"), " - ").concat(doctor);
            resTextNode.classList.add(styles['success']);
            this.finalAnimations();
          } else {
            resTextNode.append(this.createFinalError());
            resTextNode.classList.add(styles['error']);
            setTimeout(function () {
              _this14.reload();
            }, 5000);
          }
        }
      }
    }, {
      key: "finalAnimations",
      value: function finalAnimations() {
        var _this15 = this;

        this.startBtn.classList.remove(styles['active']);
        this.startBtn.classList.add(styles['success']);
        setTimeout(function () {
          _this15.reload();
        }, 5000);
      }
    }, {
      key: "reload",
      value: function reload() {
        var event = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
        event && event.preventDefault();
        this.overlay.classList.remove(styles['active']);
        this.firstInit = false;
        this.loaded = false;
        setTimeout(this.run.bind(this), 500);
      }
    }, {
      key: "createFinalError",
      value: function createFinalError() {
        var _this16 = this;

        return BX.create('p', {
          children: [BX.create('span', {
            html: BX.message('FIRSTBIT_JS_APPOINTMENT_FINAL_ERROR_START')
          }), BX.create('a', {
            attrs: {
              href: "#"
            },
            text: BX.message('FIRSTBIT_JS_APPOINTMENT_FINAL_ERROR_LINK'),
            events: {
              click: function click(e) {
                return _this16.reload(e);
              }
            }
          }), BX.create('span', {
            html: BX.message('FIRSTBIT_JS_APPOINTMENT_FINAL_ERROR_END')
          })]
        });
      }
    }, {
      key: "checkRequiredFields",
      value: function checkRequiredFields() {
        var allNotEmpty = true;

        if (this.requiredInputs.length > 0) {
          this.requiredInputs.some(function (input) {
            if (!BX.type.isNotEmptyString(input.value)) {
              var _input$parentElement;

              allNotEmpty = false;
              (_input$parentElement = input.parentElement) === null || _input$parentElement === void 0 ? void 0 : _input$parentElement.classList.add(styles["error"]);
              return true;
            } else {
              var _input$parentElement2;

              (_input$parentElement2 = input.parentElement) === null || _input$parentElement2 === void 0 ? void 0 : _input$parentElement2.classList.remove(styles["error"]);
            }
          });
        }

        return allNotEmpty && this.phoneIsValid(this.textNodes.phone.inputNode);
      }
    }, {
      key: "phoneIsValid",
      value: function phoneIsValid(phoneInput) {
        var phone = phoneInput.value;
        var isValid = !(!phone || phone.length !== this.phoneMask.length);

        if (phoneInput.parentElement !== null) {
          !isValid ? phoneInput.parentElement.classList.add(styles["error"]) : phoneInput.parentElement.classList.remove(styles["error"]);
        }

        return isValid;
      }
      /**
       * add input mask to all inputs with type=tel
       */

    }, {
      key: "addPhoneMasks",
      value: function addPhoneMasks() {
        var _this17 = this;

        var maskedInputs = this.overlay.querySelectorAll('input[type="tel"]');
        maskedInputs.length && maskedInputs.forEach(function (input) {
          input.addEventListener('input', function (e) {
            maskInput(e.currentTarget, _this17.phoneMask);
          });
        });
      }
      /**
       * add BX.calendar extension to select birthday date on related input
       */

    }, {
      key: "addCalendarSelection",
      value: function addCalendarSelection() {
        var that = this;
        var birthdayInput = this.overlay.querySelector('input[name="birthday"]');
        birthdayInput.addEventListener('keydown', function (e) {
          e.preventDefault();
          return false;
        });
        birthdayInput.addEventListener('click', function () {
          BX.calendar({
            node: birthdayInput,
            field: birthdayInput,
            bTime: false,
            callback_after: function callback_after(date$$1) {
              var timestamp = new Date(date$$1).getTime();
              that.filledInputs.textValues.birthday = that.convertDateToISO(timestamp);
            }
          });
        });
      }
      /**
       * inject styles with custom color variables from module settings
       */

    }, {
      key: "addCustomColors",
      value: function addCustomColors() {
        if (Object.keys(this.customColors).length > 0) {
          var style = BX.create('style');
          style.textContent = ".".concat(styles['appointment-popup-overlay'], ", .").concat(styles['appointment-button-wrapper'], "{");

          for (var key in this.customColors) {
            if (this.customColors.hasOwnProperty(key)) {
              switch (key) {
                case "--appointment-main-color":
                  var hslM = convertHexToHsl(this.customColors[key]);

                  if (hslM) {
                    style.textContent += "--main-h: ".concat(hslM.h, ";--main-s: ").concat(hslM.s, ";--main-l: ").concat(hslM.l, ";");
                  }

                  break;

                case "--appointment-field-color":
                  var hslF = convertHexToHsl(this.customColors[key]);

                  if (hslF) {
                    style.textContent += "-field-h: ".concat(hslF.h, ";--field-s: ").concat(hslF.s, ";--field-l: ").concat(hslF.l, ";");
                  }

                  break;

                default:
                  style.textContent += "".concat(key, ": ").concat(this.customColors[key], ";");
                  break;
              }
            }
          }

          style.textContent = style.textContent + "}";
          this.overlay.after(style);
        }
      }
      /**
       * show/hide popup with appointment form and starts loading data from 1c on first showing
       */

    }, {
      key: "togglePopup",
      value: function togglePopup() {
        this.overlay.classList.toggle(styles['active']);
        this.useCustomMainBtn ? this.startBtn.classList.toggle('active') : this.startBtn.classList.toggle(styles['active']);

        if (!this.loaded) {
          this.start();
        }
      }
      /**
       * toggle load animation on form
       * @param on
       */

    }, {
      key: "toggleLoader",
      value: function toggleLoader() {
        var on = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
        on ? this.form.classList.add(styles['loading']) : this.form.classList.remove(styles['loading']);
      }
      /**
       * convert date to ISO format without seconds
       * @param timestamp
       * @returns {string}
       */

    }, {
      key: "convertDateToISO",
      value: function convertDateToISO(timestamp) {
        var date$$1 = this.readDateInfo(timestamp);
        return "".concat(date$$1.year, "-").concat(date$$1.month, "-").concat(date$$1.day, "T").concat(date$$1.hours, ":").concat(date$$1.minutes, ":00");
      }
      /**
       * convert date to format "d-m-Y" / "d.m.Y" / "H:i"
       * @param timestamp
       * @param onlyTime
       * @param onlyDate
       * @returns {string}
       */

    }, {
      key: "convertDateToDisplay",
      value: function convertDateToDisplay(timestamp) {
        var onlyTime = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
        var onlyDate = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
        var date$$1 = this.readDateInfo(timestamp);

        if (onlyTime) {
          return "".concat(date$$1.hours, ":").concat(date$$1.minutes);
        }

        if (onlyDate) {
          return "".concat(date$$1.day, ".").concat(date$$1.month, ".").concat(date$$1.year);
        }

        return "".concat(date$$1.day, "-").concat(date$$1.month, "-").concat(date$$1.year);
      }
      /**
       * convert param to object with detail info about date
       * @param timestampOrISO
       * @returns {{hours: string, seconds: string, month: string, year: number, minutes: string, weekDay, day: string}}
       */

    }, {
      key: "readDateInfo",
      value: function readDateInfo(timestampOrISO) {
        var date$$1 = new Date(timestampOrISO);
        var day = "".concat(date$$1.getDate());

        if (Number(day) < 10) {
          day = "0".concat(day);
        }

        var month = "".concat(date$$1.getMonth() + 1);

        if (Number(month) < 10) {
          month = "0".concat(month);
        }

        var hours = "".concat(date$$1.getHours());

        if (Number(hours) < 10) {
          hours = "0".concat(hours);
        }

        var minutes = "".concat(date$$1.getMinutes());

        if (Number(minutes) < 10) {
          minutes = "0".concat(minutes);
        }

        var seconds = "".concat(date$$1.getSeconds());

        if (Number(seconds) < 10) {
          seconds = "0".concat(seconds);
        }

        return {
          "day": day,
          "month": month,
          "year": date$$1.getFullYear(),
          "hours": hours,
          "minutes": minutes,
          "seconds": seconds,
          "weekDay": this.ucFirst(date$$1.toLocaleString('ru', {
            weekday: 'short'
          }))
        };
      }
      /**
       * make the first letter of a string uppercase
       * @param str
       * @returns {string|*}
       */

    }, {
      key: "ucFirst",
      value: function ucFirst(str) {
        if (!str) return str;
        return str[0].toUpperCase() + str.slice(1);
      }
      /**
       * error logging
       * @param res
       */

    }, {
      key: "logResultErrors",
      value: function logResultErrors(res) {
        if (res.errors && Array.isArray(res.errors) && res.errors.length > 0) {
          res.errors.forEach(function (error) {
            console.log("".concat(BX.message("FIRSTBIT_JS_APPLICATION_ERROR"), " - ").concat(error.message));
          });
        } else {
          var _res$message;

          console.log(BX.message("FIRSTBIT_JS_APPLICATION_ERROR") + "\r\n", (_res$message = res.message) !== null && _res$message !== void 0 ? _res$message : res);
        }
      }
      /**
       * init elements selectors
       * @param stylesObject
       * @returns object
       */

    }, {
      key: "getAppSelectors",
      value: function getAppSelectors(stylesObject) {
        return {
          rootNodeId: 'firstbit-appointment-application-root',
          overlayId: 'appointment-popup-steps-overlay',
          startBtnWrapId: stylesObject['appointment-button-wrapper'],
          startBtnId: stylesObject['appointment-button'],
          formId: stylesObject['appointment-form'],
          mobileCloseBtnId: stylesObject['appointment-form-close'],
          messageNodeId: stylesObject['appointment-form-message'],
          submitBtnId: stylesObject['appointment-form-button'],
          appResultBlockId: stylesObject['appointment-result-block'],
          inputClass: stylesObject['appointment-form_input'],
          textareaClass: stylesObject['appointment-form_textarea'],
          confirmWrapperId: stylesObject['appointment-form-confirmation-wrapper'],
          formStepIds: {
            one: 'appointment-form-step-one',
            two: 'appointment-form-step-two',
            userData: 'appointment-form-step-userData'
          }
        };
      }
    }]);
    return AppointmentSteps;
  }();

  BX.ajax.runComponentAction('firstbit:appointment.add', 'getResult', {
    mode: 'ajax',
    data: {
      sessid: BX.bitrix_sessid()
    }
  }).then(function (response) {
    var AppPlace = BX.namespace('FirstBit.Appointment');
    AppPlace.AppointmentSteps = new AppointmentSteps(response.data);
    AppPlace.AppointmentSteps.run();
  })["catch"](function (e) {
    if (e.errors && BX.type.isArray(e.errors)) {
      var errorText = '';
      response.errors.forEach(function (error) {
        errorText = "".concat(errorText, " ").concat(error.code, " - ").concat(error.message, ";");
      });
      console.log(errorText);
    } else {
      console.log('app data loading error', e);
    }
  });

}((this.BX.FirstBit.Appointment = this.BX.FirstBit.Appointment || {}),BX,BX));
