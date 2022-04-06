this.BX = this.BX || {};
(function (exports,main_core) {
    'use strict';

    var Appointment = /*#__PURE__*/function () {
      function Appointment() {
        var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {
          name: 'def name'
        };
        babelHelpers.classCallCheck(this, Appointment);
        babelHelpers.defineProperty(this, "param", {
          name: 'string'
        });
        this.name = options.name;
      }

      babelHelpers.createClass(Appointment, [{
        key: "setName",
        value: function setName(name) {
          if (main_core.Type.isString(name)) {
            this.name = name;
          }
        }
      }, {
        key: "getName",
        value: function getName() {
          return this.name;
        }
      }]);
      return Appointment;
    }();

    exports.Appointment = Appointment;

}((this.BX.Firstbit = this.BX.Firstbit || {}),BX));
