this.BX=this.BX||{},this.BX.Anz=this.BX.Anz||{},function(t){"use strict";var e={ajaxUrl:"/bitrix/services/main/ajax.php",controller:"anz:appointment.oneCController",requestParams:{method:"POST",body:""},deleteRecord:function(t,e,n){this.runAction(t,e,n,"deleteOrder")},updateRecord:function(t,e,n){this.runAction(t,e,n,"getOrderStatus")},runAction:function(t,e,n,a){var i=this,o=BX.Main.gridManager.getInstanceById(e);o&&o.tableFade();var r="".concat(this.controller,".").concat(a);this.requestParams.body=this.createFormData({id:t,orderUid:n}),fetch("".concat(this.ajaxUrl,"?action=").concat(r),this.requestParams).then((function(t){if(t.ok)return t.json();console.log("Error. Status code ".concat(t.status))})).then((function(t){t.status})).catch((function(t){return console.log(t)})).finally((function(){if(o){var t=babelHelpers.defineProperty({},e,"page-".concat(i.getGridCurrentPage(o)));o.baseUrl=BX.Grid.Utils.addUrlParams(o.baseUrl,t),o.reloadTable("POST",{apply_filter:"Y",clear_nav:"N"})}}))},createFormData:function(t){var e=new FormData;for(var n in t)t.hasOwnProperty(n)&&e.set(n,t[n]);return e.set("sessid",BX.bitrix_sessid()),e},getGridCurrentPage:function(t){var e,n=0;if(BX.type.isDomNode(null==t||null===(e=t.data)||void 0===e?void 0:e.pagination)){var a=t.data.pagination.querySelector(".main-ui-pagination-active");a&&(n=isNaN(parseInt(a.textContent))?0:parseInt(a.textContent))}return n},bindColorPickerToNode:function(t,e){var n=arguments.length>2&&void 0!==arguments[2]?arguments[2]:"",a=BX(t),i=BX(e);BX.bind(a,"click",(function(){new BX.ColorPicker({bindElement:a,defaultColor:null!=n?n:"#FFFFFF",allowCustomColor:!0,onColorSelected:function(t){i.value=t},popupOptions:{angle:!0,autoHide:!0,closeByEsc:!0,events:{onPopupClose:function(){}}}}).open()}))},activateInputs:function(){var t=this,e={customMainBtnCheckbox:BX("appointment_view_use_custom_main_btn")},n=function(n){if(e.hasOwnProperty(n)&&"customMainBtnCheckbox"===n)e[n]&&(t.changeInputsState(e[n]),e[n].addEventListener("change",(function(){return t.changeInputsState(e[n])})))};for(var a in e)n(a)},changeInputsState:function(t){var e=BX("appointment_view_custom_main_btn_id"),n=BX("--appointment-start-btn-bg-color"),a=BX("--appointment-start-btn-text-color");t.checked?(e.removeAttribute("disabled"),n.setAttribute("disabled",!0),a.setAttribute("disabled",!0)):(e.setAttribute("disabled",!0),n.removeAttribute("disabled"),a.removeAttribute("disabled"))}};t.Admin=e}(this.BX.Anz.Appointment=this.BX.Anz.Appointment||{});
//# sourceMappingURL=admin.bundle.js.map