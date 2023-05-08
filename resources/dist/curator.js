(() => {
  var __create = Object.create;
  var __defProp = Object.defineProperty;
  var __getProtoOf = Object.getPrototypeOf;
  var __hasOwnProp = Object.prototype.hasOwnProperty;
  var __getOwnPropNames = Object.getOwnPropertyNames;
  var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
  var __markAsModule = (target) => __defProp(target, "__esModule", {value: true});
  var __commonJS = (callback, module) => () => {
    if (!module) {
      module = {exports: {}};
      callback(module.exports, module);
    }
    return module.exports;
  };
  var __exportStar = (target, module, desc) => {
    if (module && typeof module === "object" || typeof module === "function") {
      for (let key of __getOwnPropNames(module))
        if (!__hasOwnProp.call(target, key) && key !== "default")
          __defProp(target, key, {get: () => module[key], enumerable: !(desc = __getOwnPropDesc(module, key)) || desc.enumerable});
    }
    return target;
  };
  var __toModule = (module) => {
    return __exportStar(__markAsModule(__defProp(module != null ? __create(__getProtoOf(module)) : {}, "default", module && module.__esModule && "default" in module ? {get: () => module.default, enumerable: true} : {value: module, enumerable: true})), module);
  };

  // node_modules/cropperjs/dist/cropper.js
  var require_cropper = __commonJS((exports, module) => {
    /*!
     * Cropper.js v1.5.13
     * https://fengyuanchen.github.io/cropperjs
     *
     * Copyright 2015-present Chen Fengyuan
     * Released under the MIT license
     *
     * Date: 2022-11-20T05:30:46.114Z
     */
    (function(global, factory) {
      typeof exports === "object" && typeof module !== "undefined" ? module.exports = factory() : typeof define === "function" && define.amd ? define(factory) : (global = typeof globalThis !== "undefined" ? globalThis : global || self, global.Cropper = factory());
    })(exports, function() {
      "use strict";
      function ownKeys(object, enumerableOnly) {
        var keys = Object.keys(object);
        if (Object.getOwnPropertySymbols) {
          var symbols = Object.getOwnPropertySymbols(object);
          enumerableOnly && (symbols = symbols.filter(function(sym) {
            return Object.getOwnPropertyDescriptor(object, sym).enumerable;
          })), keys.push.apply(keys, symbols);
        }
        return keys;
      }
      function _objectSpread2(target) {
        for (var i = 1; i < arguments.length; i++) {
          var source = arguments[i] != null ? arguments[i] : {};
          i % 2 ? ownKeys(Object(source), true).forEach(function(key) {
            _defineProperty(target, key, source[key]);
          }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function(key) {
            Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key));
          });
        }
        return target;
      }
      function _typeof(obj) {
        "@babel/helpers - typeof";
        return _typeof = typeof Symbol == "function" && typeof Symbol.iterator == "symbol" ? function(obj2) {
          return typeof obj2;
        } : function(obj2) {
          return obj2 && typeof Symbol == "function" && obj2.constructor === Symbol && obj2 !== Symbol.prototype ? "symbol" : typeof obj2;
        }, _typeof(obj);
      }
      function _classCallCheck(instance, Constructor) {
        if (!(instance instanceof Constructor)) {
          throw new TypeError("Cannot call a class as a function");
        }
      }
      function _defineProperties(target, props) {
        for (var i = 0; i < props.length; i++) {
          var descriptor = props[i];
          descriptor.enumerable = descriptor.enumerable || false;
          descriptor.configurable = true;
          if ("value" in descriptor)
            descriptor.writable = true;
          Object.defineProperty(target, descriptor.key, descriptor);
        }
      }
      function _createClass(Constructor, protoProps, staticProps) {
        if (protoProps)
          _defineProperties(Constructor.prototype, protoProps);
        if (staticProps)
          _defineProperties(Constructor, staticProps);
        Object.defineProperty(Constructor, "prototype", {
          writable: false
        });
        return Constructor;
      }
      function _defineProperty(obj, key, value) {
        if (key in obj) {
          Object.defineProperty(obj, key, {
            value,
            enumerable: true,
            configurable: true,
            writable: true
          });
        } else {
          obj[key] = value;
        }
        return obj;
      }
      function _toConsumableArray(arr) {
        return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread();
      }
      function _arrayWithoutHoles(arr) {
        if (Array.isArray(arr))
          return _arrayLikeToArray(arr);
      }
      function _iterableToArray(iter) {
        if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null)
          return Array.from(iter);
      }
      function _unsupportedIterableToArray(o, minLen) {
        if (!o)
          return;
        if (typeof o === "string")
          return _arrayLikeToArray(o, minLen);
        var n = Object.prototype.toString.call(o).slice(8, -1);
        if (n === "Object" && o.constructor)
          n = o.constructor.name;
        if (n === "Map" || n === "Set")
          return Array.from(o);
        if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n))
          return _arrayLikeToArray(o, minLen);
      }
      function _arrayLikeToArray(arr, len) {
        if (len == null || len > arr.length)
          len = arr.length;
        for (var i = 0, arr2 = new Array(len); i < len; i++)
          arr2[i] = arr[i];
        return arr2;
      }
      function _nonIterableSpread() {
        throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
      }
      var IS_BROWSER = typeof window !== "undefined" && typeof window.document !== "undefined";
      var WINDOW = IS_BROWSER ? window : {};
      var IS_TOUCH_DEVICE = IS_BROWSER && WINDOW.document.documentElement ? "ontouchstart" in WINDOW.document.documentElement : false;
      var HAS_POINTER_EVENT = IS_BROWSER ? "PointerEvent" in WINDOW : false;
      var NAMESPACE = "cropper";
      var ACTION_ALL = "all";
      var ACTION_CROP = "crop";
      var ACTION_MOVE = "move";
      var ACTION_ZOOM = "zoom";
      var ACTION_EAST = "e";
      var ACTION_WEST = "w";
      var ACTION_SOUTH = "s";
      var ACTION_NORTH = "n";
      var ACTION_NORTH_EAST = "ne";
      var ACTION_NORTH_WEST = "nw";
      var ACTION_SOUTH_EAST = "se";
      var ACTION_SOUTH_WEST = "sw";
      var CLASS_CROP = "".concat(NAMESPACE, "-crop");
      var CLASS_DISABLED = "".concat(NAMESPACE, "-disabled");
      var CLASS_HIDDEN = "".concat(NAMESPACE, "-hidden");
      var CLASS_HIDE = "".concat(NAMESPACE, "-hide");
      var CLASS_INVISIBLE = "".concat(NAMESPACE, "-invisible");
      var CLASS_MODAL = "".concat(NAMESPACE, "-modal");
      var CLASS_MOVE = "".concat(NAMESPACE, "-move");
      var DATA_ACTION = "".concat(NAMESPACE, "Action");
      var DATA_PREVIEW = "".concat(NAMESPACE, "Preview");
      var DRAG_MODE_CROP = "crop";
      var DRAG_MODE_MOVE = "move";
      var DRAG_MODE_NONE = "none";
      var EVENT_CROP = "crop";
      var EVENT_CROP_END = "cropend";
      var EVENT_CROP_MOVE = "cropmove";
      var EVENT_CROP_START = "cropstart";
      var EVENT_DBLCLICK = "dblclick";
      var EVENT_TOUCH_START = IS_TOUCH_DEVICE ? "touchstart" : "mousedown";
      var EVENT_TOUCH_MOVE = IS_TOUCH_DEVICE ? "touchmove" : "mousemove";
      var EVENT_TOUCH_END = IS_TOUCH_DEVICE ? "touchend touchcancel" : "mouseup";
      var EVENT_POINTER_DOWN = HAS_POINTER_EVENT ? "pointerdown" : EVENT_TOUCH_START;
      var EVENT_POINTER_MOVE = HAS_POINTER_EVENT ? "pointermove" : EVENT_TOUCH_MOVE;
      var EVENT_POINTER_UP = HAS_POINTER_EVENT ? "pointerup pointercancel" : EVENT_TOUCH_END;
      var EVENT_READY = "ready";
      var EVENT_RESIZE = "resize";
      var EVENT_WHEEL = "wheel";
      var EVENT_ZOOM = "zoom";
      var MIME_TYPE_JPEG = "image/jpeg";
      var REGEXP_ACTIONS = /^e|w|s|n|se|sw|ne|nw|all|crop|move|zoom$/;
      var REGEXP_DATA_URL = /^data:/;
      var REGEXP_DATA_URL_JPEG = /^data:image\/jpeg;base64,/;
      var REGEXP_TAG_NAME = /^img|canvas$/i;
      var MIN_CONTAINER_WIDTH = 200;
      var MIN_CONTAINER_HEIGHT = 100;
      var DEFAULTS = {
        viewMode: 0,
        dragMode: DRAG_MODE_CROP,
        initialAspectRatio: NaN,
        aspectRatio: NaN,
        data: null,
        preview: "",
        responsive: true,
        restore: true,
        checkCrossOrigin: true,
        checkOrientation: true,
        modal: true,
        guides: true,
        center: true,
        highlight: true,
        background: true,
        autoCrop: true,
        autoCropArea: 0.8,
        movable: true,
        rotatable: true,
        scalable: true,
        zoomable: true,
        zoomOnTouch: true,
        zoomOnWheel: true,
        wheelZoomRatio: 0.1,
        cropBoxMovable: true,
        cropBoxResizable: true,
        toggleDragModeOnDblclick: true,
        minCanvasWidth: 0,
        minCanvasHeight: 0,
        minCropBoxWidth: 0,
        minCropBoxHeight: 0,
        minContainerWidth: MIN_CONTAINER_WIDTH,
        minContainerHeight: MIN_CONTAINER_HEIGHT,
        ready: null,
        cropstart: null,
        cropmove: null,
        cropend: null,
        crop: null,
        zoom: null
      };
      var TEMPLATE = '<div class="cropper-container" touch-action="none"><div class="cropper-wrap-box"><div class="cropper-canvas"></div></div><div class="cropper-drag-box"></div><div class="cropper-crop-box"><span class="cropper-view-box"></span><span class="cropper-dashed dashed-h"></span><span class="cropper-dashed dashed-v"></span><span class="cropper-center"></span><span class="cropper-face"></span><span class="cropper-line line-e" data-cropper-action="e"></span><span class="cropper-line line-n" data-cropper-action="n"></span><span class="cropper-line line-w" data-cropper-action="w"></span><span class="cropper-line line-s" data-cropper-action="s"></span><span class="cropper-point point-e" data-cropper-action="e"></span><span class="cropper-point point-n" data-cropper-action="n"></span><span class="cropper-point point-w" data-cropper-action="w"></span><span class="cropper-point point-s" data-cropper-action="s"></span><span class="cropper-point point-ne" data-cropper-action="ne"></span><span class="cropper-point point-nw" data-cropper-action="nw"></span><span class="cropper-point point-sw" data-cropper-action="sw"></span><span class="cropper-point point-se" data-cropper-action="se"></span></div></div>';
      var isNaN = Number.isNaN || WINDOW.isNaN;
      function isNumber(value) {
        return typeof value === "number" && !isNaN(value);
      }
      var isPositiveNumber = function isPositiveNumber2(value) {
        return value > 0 && value < Infinity;
      };
      function isUndefined(value) {
        return typeof value === "undefined";
      }
      function isObject(value) {
        return _typeof(value) === "object" && value !== null;
      }
      var hasOwnProperty = Object.prototype.hasOwnProperty;
      function isPlainObject(value) {
        if (!isObject(value)) {
          return false;
        }
        try {
          var _constructor = value.constructor;
          var prototype = _constructor.prototype;
          return _constructor && prototype && hasOwnProperty.call(prototype, "isPrototypeOf");
        } catch (error) {
          return false;
        }
      }
      function isFunction(value) {
        return typeof value === "function";
      }
      var slice = Array.prototype.slice;
      function toArray(value) {
        return Array.from ? Array.from(value) : slice.call(value);
      }
      function forEach(data, callback) {
        if (data && isFunction(callback)) {
          if (Array.isArray(data) || isNumber(data.length)) {
            toArray(data).forEach(function(value, key) {
              callback.call(data, value, key, data);
            });
          } else if (isObject(data)) {
            Object.keys(data).forEach(function(key) {
              callback.call(data, data[key], key, data);
            });
          }
        }
        return data;
      }
      var assign = Object.assign || function assign2(target) {
        for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
          args[_key - 1] = arguments[_key];
        }
        if (isObject(target) && args.length > 0) {
          args.forEach(function(arg) {
            if (isObject(arg)) {
              Object.keys(arg).forEach(function(key) {
                target[key] = arg[key];
              });
            }
          });
        }
        return target;
      };
      var REGEXP_DECIMALS = /\.\d*(?:0|9){12}\d*$/;
      function normalizeDecimalNumber(value) {
        var times = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : 1e11;
        return REGEXP_DECIMALS.test(value) ? Math.round(value * times) / times : value;
      }
      var REGEXP_SUFFIX = /^width|height|left|top|marginLeft|marginTop$/;
      function setStyle(element, styles) {
        var style = element.style;
        forEach(styles, function(value, property) {
          if (REGEXP_SUFFIX.test(property) && isNumber(value)) {
            value = "".concat(value, "px");
          }
          style[property] = value;
        });
      }
      function hasClass(element, value) {
        return element.classList ? element.classList.contains(value) : element.className.indexOf(value) > -1;
      }
      function addClass(element, value) {
        if (!value) {
          return;
        }
        if (isNumber(element.length)) {
          forEach(element, function(elem) {
            addClass(elem, value);
          });
          return;
        }
        if (element.classList) {
          element.classList.add(value);
          return;
        }
        var className = element.className.trim();
        if (!className) {
          element.className = value;
        } else if (className.indexOf(value) < 0) {
          element.className = "".concat(className, " ").concat(value);
        }
      }
      function removeClass(element, value) {
        if (!value) {
          return;
        }
        if (isNumber(element.length)) {
          forEach(element, function(elem) {
            removeClass(elem, value);
          });
          return;
        }
        if (element.classList) {
          element.classList.remove(value);
          return;
        }
        if (element.className.indexOf(value) >= 0) {
          element.className = element.className.replace(value, "");
        }
      }
      function toggleClass(element, value, added) {
        if (!value) {
          return;
        }
        if (isNumber(element.length)) {
          forEach(element, function(elem) {
            toggleClass(elem, value, added);
          });
          return;
        }
        if (added) {
          addClass(element, value);
        } else {
          removeClass(element, value);
        }
      }
      var REGEXP_CAMEL_CASE = /([a-z\d])([A-Z])/g;
      function toParamCase(value) {
        return value.replace(REGEXP_CAMEL_CASE, "$1-$2").toLowerCase();
      }
      function getData(element, name) {
        if (isObject(element[name])) {
          return element[name];
        }
        if (element.dataset) {
          return element.dataset[name];
        }
        return element.getAttribute("data-".concat(toParamCase(name)));
      }
      function setData(element, name, data) {
        if (isObject(data)) {
          element[name] = data;
        } else if (element.dataset) {
          element.dataset[name] = data;
        } else {
          element.setAttribute("data-".concat(toParamCase(name)), data);
        }
      }
      function removeData(element, name) {
        if (isObject(element[name])) {
          try {
            delete element[name];
          } catch (error) {
            element[name] = void 0;
          }
        } else if (element.dataset) {
          try {
            delete element.dataset[name];
          } catch (error) {
            element.dataset[name] = void 0;
          }
        } else {
          element.removeAttribute("data-".concat(toParamCase(name)));
        }
      }
      var REGEXP_SPACES = /\s\s*/;
      var onceSupported = function() {
        var supported = false;
        if (IS_BROWSER) {
          var once = false;
          var listener = function listener2() {
          };
          var options = Object.defineProperty({}, "once", {
            get: function get() {
              supported = true;
              return once;
            },
            set: function set(value) {
              once = value;
            }
          });
          WINDOW.addEventListener("test", listener, options);
          WINDOW.removeEventListener("test", listener, options);
        }
        return supported;
      }();
      function removeListener(element, type, listener) {
        var options = arguments.length > 3 && arguments[3] !== void 0 ? arguments[3] : {};
        var handler = listener;
        type.trim().split(REGEXP_SPACES).forEach(function(event) {
          if (!onceSupported) {
            var listeners = element.listeners;
            if (listeners && listeners[event] && listeners[event][listener]) {
              handler = listeners[event][listener];
              delete listeners[event][listener];
              if (Object.keys(listeners[event]).length === 0) {
                delete listeners[event];
              }
              if (Object.keys(listeners).length === 0) {
                delete element.listeners;
              }
            }
          }
          element.removeEventListener(event, handler, options);
        });
      }
      function addListener(element, type, listener) {
        var options = arguments.length > 3 && arguments[3] !== void 0 ? arguments[3] : {};
        var _handler = listener;
        type.trim().split(REGEXP_SPACES).forEach(function(event) {
          if (options.once && !onceSupported) {
            var _element$listeners = element.listeners, listeners = _element$listeners === void 0 ? {} : _element$listeners;
            _handler = function handler() {
              delete listeners[event][listener];
              element.removeEventListener(event, _handler, options);
              for (var _len2 = arguments.length, args = new Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
                args[_key2] = arguments[_key2];
              }
              listener.apply(element, args);
            };
            if (!listeners[event]) {
              listeners[event] = {};
            }
            if (listeners[event][listener]) {
              element.removeEventListener(event, listeners[event][listener], options);
            }
            listeners[event][listener] = _handler;
            element.listeners = listeners;
          }
          element.addEventListener(event, _handler, options);
        });
      }
      function dispatchEvent(element, type, data) {
        var event;
        if (isFunction(Event) && isFunction(CustomEvent)) {
          event = new CustomEvent(type, {
            detail: data,
            bubbles: true,
            cancelable: true
          });
        } else {
          event = document.createEvent("CustomEvent");
          event.initCustomEvent(type, true, true, data);
        }
        return element.dispatchEvent(event);
      }
      function getOffset(element) {
        var box = element.getBoundingClientRect();
        return {
          left: box.left + (window.pageXOffset - document.documentElement.clientLeft),
          top: box.top + (window.pageYOffset - document.documentElement.clientTop)
        };
      }
      var location = WINDOW.location;
      var REGEXP_ORIGINS = /^(\w+:)\/\/([^:/?#]*):?(\d*)/i;
      function isCrossOriginURL(url) {
        var parts = url.match(REGEXP_ORIGINS);
        return parts !== null && (parts[1] !== location.protocol || parts[2] !== location.hostname || parts[3] !== location.port);
      }
      function addTimestamp(url) {
        var timestamp = "timestamp=".concat(new Date().getTime());
        return url + (url.indexOf("?") === -1 ? "?" : "&") + timestamp;
      }
      function getTransforms(_ref) {
        var rotate = _ref.rotate, scaleX = _ref.scaleX, scaleY = _ref.scaleY, translateX = _ref.translateX, translateY = _ref.translateY;
        var values = [];
        if (isNumber(translateX) && translateX !== 0) {
          values.push("translateX(".concat(translateX, "px)"));
        }
        if (isNumber(translateY) && translateY !== 0) {
          values.push("translateY(".concat(translateY, "px)"));
        }
        if (isNumber(rotate) && rotate !== 0) {
          values.push("rotate(".concat(rotate, "deg)"));
        }
        if (isNumber(scaleX) && scaleX !== 1) {
          values.push("scaleX(".concat(scaleX, ")"));
        }
        if (isNumber(scaleY) && scaleY !== 1) {
          values.push("scaleY(".concat(scaleY, ")"));
        }
        var transform = values.length ? values.join(" ") : "none";
        return {
          WebkitTransform: transform,
          msTransform: transform,
          transform
        };
      }
      function getMaxZoomRatio(pointers) {
        var pointers2 = _objectSpread2({}, pointers);
        var maxRatio = 0;
        forEach(pointers, function(pointer, pointerId) {
          delete pointers2[pointerId];
          forEach(pointers2, function(pointer2) {
            var x1 = Math.abs(pointer.startX - pointer2.startX);
            var y1 = Math.abs(pointer.startY - pointer2.startY);
            var x2 = Math.abs(pointer.endX - pointer2.endX);
            var y2 = Math.abs(pointer.endY - pointer2.endY);
            var z1 = Math.sqrt(x1 * x1 + y1 * y1);
            var z2 = Math.sqrt(x2 * x2 + y2 * y2);
            var ratio = (z2 - z1) / z1;
            if (Math.abs(ratio) > Math.abs(maxRatio)) {
              maxRatio = ratio;
            }
          });
        });
        return maxRatio;
      }
      function getPointer(_ref2, endOnly) {
        var pageX = _ref2.pageX, pageY = _ref2.pageY;
        var end = {
          endX: pageX,
          endY: pageY
        };
        return endOnly ? end : _objectSpread2({
          startX: pageX,
          startY: pageY
        }, end);
      }
      function getPointersCenter(pointers) {
        var pageX = 0;
        var pageY = 0;
        var count = 0;
        forEach(pointers, function(_ref3) {
          var startX = _ref3.startX, startY = _ref3.startY;
          pageX += startX;
          pageY += startY;
          count += 1;
        });
        pageX /= count;
        pageY /= count;
        return {
          pageX,
          pageY
        };
      }
      function getAdjustedSizes(_ref4) {
        var aspectRatio = _ref4.aspectRatio, height = _ref4.height, width = _ref4.width;
        var type = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : "contain";
        var isValidWidth = isPositiveNumber(width);
        var isValidHeight = isPositiveNumber(height);
        if (isValidWidth && isValidHeight) {
          var adjustedWidth = height * aspectRatio;
          if (type === "contain" && adjustedWidth > width || type === "cover" && adjustedWidth < width) {
            height = width / aspectRatio;
          } else {
            width = height * aspectRatio;
          }
        } else if (isValidWidth) {
          height = width / aspectRatio;
        } else if (isValidHeight) {
          width = height * aspectRatio;
        }
        return {
          width,
          height
        };
      }
      function getRotatedSizes(_ref5) {
        var width = _ref5.width, height = _ref5.height, degree = _ref5.degree;
        degree = Math.abs(degree) % 180;
        if (degree === 90) {
          return {
            width: height,
            height: width
          };
        }
        var arc = degree % 90 * Math.PI / 180;
        var sinArc = Math.sin(arc);
        var cosArc = Math.cos(arc);
        var newWidth = width * cosArc + height * sinArc;
        var newHeight = width * sinArc + height * cosArc;
        return degree > 90 ? {
          width: newHeight,
          height: newWidth
        } : {
          width: newWidth,
          height: newHeight
        };
      }
      function getSourceCanvas(image, _ref6, _ref7, _ref8) {
        var imageAspectRatio = _ref6.aspectRatio, imageNaturalWidth = _ref6.naturalWidth, imageNaturalHeight = _ref6.naturalHeight, _ref6$rotate = _ref6.rotate, rotate = _ref6$rotate === void 0 ? 0 : _ref6$rotate, _ref6$scaleX = _ref6.scaleX, scaleX = _ref6$scaleX === void 0 ? 1 : _ref6$scaleX, _ref6$scaleY = _ref6.scaleY, scaleY = _ref6$scaleY === void 0 ? 1 : _ref6$scaleY;
        var aspectRatio = _ref7.aspectRatio, naturalWidth = _ref7.naturalWidth, naturalHeight = _ref7.naturalHeight;
        var _ref8$fillColor = _ref8.fillColor, fillColor = _ref8$fillColor === void 0 ? "transparent" : _ref8$fillColor, _ref8$imageSmoothingE = _ref8.imageSmoothingEnabled, imageSmoothingEnabled = _ref8$imageSmoothingE === void 0 ? true : _ref8$imageSmoothingE, _ref8$imageSmoothingQ = _ref8.imageSmoothingQuality, imageSmoothingQuality = _ref8$imageSmoothingQ === void 0 ? "low" : _ref8$imageSmoothingQ, _ref8$maxWidth = _ref8.maxWidth, maxWidth = _ref8$maxWidth === void 0 ? Infinity : _ref8$maxWidth, _ref8$maxHeight = _ref8.maxHeight, maxHeight = _ref8$maxHeight === void 0 ? Infinity : _ref8$maxHeight, _ref8$minWidth = _ref8.minWidth, minWidth = _ref8$minWidth === void 0 ? 0 : _ref8$minWidth, _ref8$minHeight = _ref8.minHeight, minHeight = _ref8$minHeight === void 0 ? 0 : _ref8$minHeight;
        var canvas = document.createElement("canvas");
        var context = canvas.getContext("2d");
        var maxSizes = getAdjustedSizes({
          aspectRatio,
          width: maxWidth,
          height: maxHeight
        });
        var minSizes = getAdjustedSizes({
          aspectRatio,
          width: minWidth,
          height: minHeight
        }, "cover");
        var width = Math.min(maxSizes.width, Math.max(minSizes.width, naturalWidth));
        var height = Math.min(maxSizes.height, Math.max(minSizes.height, naturalHeight));
        var destMaxSizes = getAdjustedSizes({
          aspectRatio: imageAspectRatio,
          width: maxWidth,
          height: maxHeight
        });
        var destMinSizes = getAdjustedSizes({
          aspectRatio: imageAspectRatio,
          width: minWidth,
          height: minHeight
        }, "cover");
        var destWidth = Math.min(destMaxSizes.width, Math.max(destMinSizes.width, imageNaturalWidth));
        var destHeight = Math.min(destMaxSizes.height, Math.max(destMinSizes.height, imageNaturalHeight));
        var params = [-destWidth / 2, -destHeight / 2, destWidth, destHeight];
        canvas.width = normalizeDecimalNumber(width);
        canvas.height = normalizeDecimalNumber(height);
        context.fillStyle = fillColor;
        context.fillRect(0, 0, width, height);
        context.save();
        context.translate(width / 2, height / 2);
        context.rotate(rotate * Math.PI / 180);
        context.scale(scaleX, scaleY);
        context.imageSmoothingEnabled = imageSmoothingEnabled;
        context.imageSmoothingQuality = imageSmoothingQuality;
        context.drawImage.apply(context, [image].concat(_toConsumableArray(params.map(function(param) {
          return Math.floor(normalizeDecimalNumber(param));
        }))));
        context.restore();
        return canvas;
      }
      var fromCharCode = String.fromCharCode;
      function getStringFromCharCode(dataView, start, length) {
        var str = "";
        length += start;
        for (var i = start; i < length; i += 1) {
          str += fromCharCode(dataView.getUint8(i));
        }
        return str;
      }
      var REGEXP_DATA_URL_HEAD = /^data:.*,/;
      function dataURLToArrayBuffer(dataURL) {
        var base64 = dataURL.replace(REGEXP_DATA_URL_HEAD, "");
        var binary = atob(base64);
        var arrayBuffer = new ArrayBuffer(binary.length);
        var uint8 = new Uint8Array(arrayBuffer);
        forEach(uint8, function(value, i) {
          uint8[i] = binary.charCodeAt(i);
        });
        return arrayBuffer;
      }
      function arrayBufferToDataURL(arrayBuffer, mimeType) {
        var chunks = [];
        var chunkSize = 8192;
        var uint8 = new Uint8Array(arrayBuffer);
        while (uint8.length > 0) {
          chunks.push(fromCharCode.apply(null, toArray(uint8.subarray(0, chunkSize))));
          uint8 = uint8.subarray(chunkSize);
        }
        return "data:".concat(mimeType, ";base64,").concat(btoa(chunks.join("")));
      }
      function resetAndGetOrientation(arrayBuffer) {
        var dataView = new DataView(arrayBuffer);
        var orientation;
        try {
          var littleEndian;
          var app1Start;
          var ifdStart;
          if (dataView.getUint8(0) === 255 && dataView.getUint8(1) === 216) {
            var length = dataView.byteLength;
            var offset = 2;
            while (offset + 1 < length) {
              if (dataView.getUint8(offset) === 255 && dataView.getUint8(offset + 1) === 225) {
                app1Start = offset;
                break;
              }
              offset += 1;
            }
          }
          if (app1Start) {
            var exifIDCode = app1Start + 4;
            var tiffOffset = app1Start + 10;
            if (getStringFromCharCode(dataView, exifIDCode, 4) === "Exif") {
              var endianness = dataView.getUint16(tiffOffset);
              littleEndian = endianness === 18761;
              if (littleEndian || endianness === 19789) {
                if (dataView.getUint16(tiffOffset + 2, littleEndian) === 42) {
                  var firstIFDOffset = dataView.getUint32(tiffOffset + 4, littleEndian);
                  if (firstIFDOffset >= 8) {
                    ifdStart = tiffOffset + firstIFDOffset;
                  }
                }
              }
            }
          }
          if (ifdStart) {
            var _length = dataView.getUint16(ifdStart, littleEndian);
            var _offset;
            var i;
            for (i = 0; i < _length; i += 1) {
              _offset = ifdStart + i * 12 + 2;
              if (dataView.getUint16(_offset, littleEndian) === 274) {
                _offset += 8;
                orientation = dataView.getUint16(_offset, littleEndian);
                dataView.setUint16(_offset, 1, littleEndian);
                break;
              }
            }
          }
        } catch (error) {
          orientation = 1;
        }
        return orientation;
      }
      function parseOrientation(orientation) {
        var rotate = 0;
        var scaleX = 1;
        var scaleY = 1;
        switch (orientation) {
          case 2:
            scaleX = -1;
            break;
          case 3:
            rotate = -180;
            break;
          case 4:
            scaleY = -1;
            break;
          case 5:
            rotate = 90;
            scaleY = -1;
            break;
          case 6:
            rotate = 90;
            break;
          case 7:
            rotate = 90;
            scaleX = -1;
            break;
          case 8:
            rotate = -90;
            break;
        }
        return {
          rotate,
          scaleX,
          scaleY
        };
      }
      var render = {
        render: function render2() {
          this.initContainer();
          this.initCanvas();
          this.initCropBox();
          this.renderCanvas();
          if (this.cropped) {
            this.renderCropBox();
          }
        },
        initContainer: function initContainer() {
          var element = this.element, options = this.options, container = this.container, cropper = this.cropper;
          var minWidth = Number(options.minContainerWidth);
          var minHeight = Number(options.minContainerHeight);
          addClass(cropper, CLASS_HIDDEN);
          removeClass(element, CLASS_HIDDEN);
          var containerData = {
            width: Math.max(container.offsetWidth, minWidth >= 0 ? minWidth : MIN_CONTAINER_WIDTH),
            height: Math.max(container.offsetHeight, minHeight >= 0 ? minHeight : MIN_CONTAINER_HEIGHT)
          };
          this.containerData = containerData;
          setStyle(cropper, {
            width: containerData.width,
            height: containerData.height
          });
          addClass(element, CLASS_HIDDEN);
          removeClass(cropper, CLASS_HIDDEN);
        },
        initCanvas: function initCanvas() {
          var containerData = this.containerData, imageData = this.imageData;
          var viewMode = this.options.viewMode;
          var rotated = Math.abs(imageData.rotate) % 180 === 90;
          var naturalWidth = rotated ? imageData.naturalHeight : imageData.naturalWidth;
          var naturalHeight = rotated ? imageData.naturalWidth : imageData.naturalHeight;
          var aspectRatio = naturalWidth / naturalHeight;
          var canvasWidth = containerData.width;
          var canvasHeight = containerData.height;
          if (containerData.height * aspectRatio > containerData.width) {
            if (viewMode === 3) {
              canvasWidth = containerData.height * aspectRatio;
            } else {
              canvasHeight = containerData.width / aspectRatio;
            }
          } else if (viewMode === 3) {
            canvasHeight = containerData.width / aspectRatio;
          } else {
            canvasWidth = containerData.height * aspectRatio;
          }
          var canvasData = {
            aspectRatio,
            naturalWidth,
            naturalHeight,
            width: canvasWidth,
            height: canvasHeight
          };
          this.canvasData = canvasData;
          this.limited = viewMode === 1 || viewMode === 2;
          this.limitCanvas(true, true);
          canvasData.width = Math.min(Math.max(canvasData.width, canvasData.minWidth), canvasData.maxWidth);
          canvasData.height = Math.min(Math.max(canvasData.height, canvasData.minHeight), canvasData.maxHeight);
          canvasData.left = (containerData.width - canvasData.width) / 2;
          canvasData.top = (containerData.height - canvasData.height) / 2;
          canvasData.oldLeft = canvasData.left;
          canvasData.oldTop = canvasData.top;
          this.initialCanvasData = assign({}, canvasData);
        },
        limitCanvas: function limitCanvas(sizeLimited, positionLimited) {
          var options = this.options, containerData = this.containerData, canvasData = this.canvasData, cropBoxData = this.cropBoxData;
          var viewMode = options.viewMode;
          var aspectRatio = canvasData.aspectRatio;
          var cropped = this.cropped && cropBoxData;
          if (sizeLimited) {
            var minCanvasWidth = Number(options.minCanvasWidth) || 0;
            var minCanvasHeight = Number(options.minCanvasHeight) || 0;
            if (viewMode > 1) {
              minCanvasWidth = Math.max(minCanvasWidth, containerData.width);
              minCanvasHeight = Math.max(minCanvasHeight, containerData.height);
              if (viewMode === 3) {
                if (minCanvasHeight * aspectRatio > minCanvasWidth) {
                  minCanvasWidth = minCanvasHeight * aspectRatio;
                } else {
                  minCanvasHeight = minCanvasWidth / aspectRatio;
                }
              }
            } else if (viewMode > 0) {
              if (minCanvasWidth) {
                minCanvasWidth = Math.max(minCanvasWidth, cropped ? cropBoxData.width : 0);
              } else if (minCanvasHeight) {
                minCanvasHeight = Math.max(minCanvasHeight, cropped ? cropBoxData.height : 0);
              } else if (cropped) {
                minCanvasWidth = cropBoxData.width;
                minCanvasHeight = cropBoxData.height;
                if (minCanvasHeight * aspectRatio > minCanvasWidth) {
                  minCanvasWidth = minCanvasHeight * aspectRatio;
                } else {
                  minCanvasHeight = minCanvasWidth / aspectRatio;
                }
              }
            }
            var _getAdjustedSizes = getAdjustedSizes({
              aspectRatio,
              width: minCanvasWidth,
              height: minCanvasHeight
            });
            minCanvasWidth = _getAdjustedSizes.width;
            minCanvasHeight = _getAdjustedSizes.height;
            canvasData.minWidth = minCanvasWidth;
            canvasData.minHeight = minCanvasHeight;
            canvasData.maxWidth = Infinity;
            canvasData.maxHeight = Infinity;
          }
          if (positionLimited) {
            if (viewMode > (cropped ? 0 : 1)) {
              var newCanvasLeft = containerData.width - canvasData.width;
              var newCanvasTop = containerData.height - canvasData.height;
              canvasData.minLeft = Math.min(0, newCanvasLeft);
              canvasData.minTop = Math.min(0, newCanvasTop);
              canvasData.maxLeft = Math.max(0, newCanvasLeft);
              canvasData.maxTop = Math.max(0, newCanvasTop);
              if (cropped && this.limited) {
                canvasData.minLeft = Math.min(cropBoxData.left, cropBoxData.left + (cropBoxData.width - canvasData.width));
                canvasData.minTop = Math.min(cropBoxData.top, cropBoxData.top + (cropBoxData.height - canvasData.height));
                canvasData.maxLeft = cropBoxData.left;
                canvasData.maxTop = cropBoxData.top;
                if (viewMode === 2) {
                  if (canvasData.width >= containerData.width) {
                    canvasData.minLeft = Math.min(0, newCanvasLeft);
                    canvasData.maxLeft = Math.max(0, newCanvasLeft);
                  }
                  if (canvasData.height >= containerData.height) {
                    canvasData.minTop = Math.min(0, newCanvasTop);
                    canvasData.maxTop = Math.max(0, newCanvasTop);
                  }
                }
              }
            } else {
              canvasData.minLeft = -canvasData.width;
              canvasData.minTop = -canvasData.height;
              canvasData.maxLeft = containerData.width;
              canvasData.maxTop = containerData.height;
            }
          }
        },
        renderCanvas: function renderCanvas(changed, transformed) {
          var canvasData = this.canvasData, imageData = this.imageData;
          if (transformed) {
            var _getRotatedSizes = getRotatedSizes({
              width: imageData.naturalWidth * Math.abs(imageData.scaleX || 1),
              height: imageData.naturalHeight * Math.abs(imageData.scaleY || 1),
              degree: imageData.rotate || 0
            }), naturalWidth = _getRotatedSizes.width, naturalHeight = _getRotatedSizes.height;
            var width = canvasData.width * (naturalWidth / canvasData.naturalWidth);
            var height = canvasData.height * (naturalHeight / canvasData.naturalHeight);
            canvasData.left -= (width - canvasData.width) / 2;
            canvasData.top -= (height - canvasData.height) / 2;
            canvasData.width = width;
            canvasData.height = height;
            canvasData.aspectRatio = naturalWidth / naturalHeight;
            canvasData.naturalWidth = naturalWidth;
            canvasData.naturalHeight = naturalHeight;
            this.limitCanvas(true, false);
          }
          if (canvasData.width > canvasData.maxWidth || canvasData.width < canvasData.minWidth) {
            canvasData.left = canvasData.oldLeft;
          }
          if (canvasData.height > canvasData.maxHeight || canvasData.height < canvasData.minHeight) {
            canvasData.top = canvasData.oldTop;
          }
          canvasData.width = Math.min(Math.max(canvasData.width, canvasData.minWidth), canvasData.maxWidth);
          canvasData.height = Math.min(Math.max(canvasData.height, canvasData.minHeight), canvasData.maxHeight);
          this.limitCanvas(false, true);
          canvasData.left = Math.min(Math.max(canvasData.left, canvasData.minLeft), canvasData.maxLeft);
          canvasData.top = Math.min(Math.max(canvasData.top, canvasData.minTop), canvasData.maxTop);
          canvasData.oldLeft = canvasData.left;
          canvasData.oldTop = canvasData.top;
          setStyle(this.canvas, assign({
            width: canvasData.width,
            height: canvasData.height
          }, getTransforms({
            translateX: canvasData.left,
            translateY: canvasData.top
          })));
          this.renderImage(changed);
          if (this.cropped && this.limited) {
            this.limitCropBox(true, true);
          }
        },
        renderImage: function renderImage(changed) {
          var canvasData = this.canvasData, imageData = this.imageData;
          var width = imageData.naturalWidth * (canvasData.width / canvasData.naturalWidth);
          var height = imageData.naturalHeight * (canvasData.height / canvasData.naturalHeight);
          assign(imageData, {
            width,
            height,
            left: (canvasData.width - width) / 2,
            top: (canvasData.height - height) / 2
          });
          setStyle(this.image, assign({
            width: imageData.width,
            height: imageData.height
          }, getTransforms(assign({
            translateX: imageData.left,
            translateY: imageData.top
          }, imageData))));
          if (changed) {
            this.output();
          }
        },
        initCropBox: function initCropBox() {
          var options = this.options, canvasData = this.canvasData;
          var aspectRatio = options.aspectRatio || options.initialAspectRatio;
          var autoCropArea = Number(options.autoCropArea) || 0.8;
          var cropBoxData = {
            width: canvasData.width,
            height: canvasData.height
          };
          if (aspectRatio) {
            if (canvasData.height * aspectRatio > canvasData.width) {
              cropBoxData.height = cropBoxData.width / aspectRatio;
            } else {
              cropBoxData.width = cropBoxData.height * aspectRatio;
            }
          }
          this.cropBoxData = cropBoxData;
          this.limitCropBox(true, true);
          cropBoxData.width = Math.min(Math.max(cropBoxData.width, cropBoxData.minWidth), cropBoxData.maxWidth);
          cropBoxData.height = Math.min(Math.max(cropBoxData.height, cropBoxData.minHeight), cropBoxData.maxHeight);
          cropBoxData.width = Math.max(cropBoxData.minWidth, cropBoxData.width * autoCropArea);
          cropBoxData.height = Math.max(cropBoxData.minHeight, cropBoxData.height * autoCropArea);
          cropBoxData.left = canvasData.left + (canvasData.width - cropBoxData.width) / 2;
          cropBoxData.top = canvasData.top + (canvasData.height - cropBoxData.height) / 2;
          cropBoxData.oldLeft = cropBoxData.left;
          cropBoxData.oldTop = cropBoxData.top;
          this.initialCropBoxData = assign({}, cropBoxData);
        },
        limitCropBox: function limitCropBox(sizeLimited, positionLimited) {
          var options = this.options, containerData = this.containerData, canvasData = this.canvasData, cropBoxData = this.cropBoxData, limited = this.limited;
          var aspectRatio = options.aspectRatio;
          if (sizeLimited) {
            var minCropBoxWidth = Number(options.minCropBoxWidth) || 0;
            var minCropBoxHeight = Number(options.minCropBoxHeight) || 0;
            var maxCropBoxWidth = limited ? Math.min(containerData.width, canvasData.width, canvasData.width + canvasData.left, containerData.width - canvasData.left) : containerData.width;
            var maxCropBoxHeight = limited ? Math.min(containerData.height, canvasData.height, canvasData.height + canvasData.top, containerData.height - canvasData.top) : containerData.height;
            minCropBoxWidth = Math.min(minCropBoxWidth, containerData.width);
            minCropBoxHeight = Math.min(minCropBoxHeight, containerData.height);
            if (aspectRatio) {
              if (minCropBoxWidth && minCropBoxHeight) {
                if (minCropBoxHeight * aspectRatio > minCropBoxWidth) {
                  minCropBoxHeight = minCropBoxWidth / aspectRatio;
                } else {
                  minCropBoxWidth = minCropBoxHeight * aspectRatio;
                }
              } else if (minCropBoxWidth) {
                minCropBoxHeight = minCropBoxWidth / aspectRatio;
              } else if (minCropBoxHeight) {
                minCropBoxWidth = minCropBoxHeight * aspectRatio;
              }
              if (maxCropBoxHeight * aspectRatio > maxCropBoxWidth) {
                maxCropBoxHeight = maxCropBoxWidth / aspectRatio;
              } else {
                maxCropBoxWidth = maxCropBoxHeight * aspectRatio;
              }
            }
            cropBoxData.minWidth = Math.min(minCropBoxWidth, maxCropBoxWidth);
            cropBoxData.minHeight = Math.min(minCropBoxHeight, maxCropBoxHeight);
            cropBoxData.maxWidth = maxCropBoxWidth;
            cropBoxData.maxHeight = maxCropBoxHeight;
          }
          if (positionLimited) {
            if (limited) {
              cropBoxData.minLeft = Math.max(0, canvasData.left);
              cropBoxData.minTop = Math.max(0, canvasData.top);
              cropBoxData.maxLeft = Math.min(containerData.width, canvasData.left + canvasData.width) - cropBoxData.width;
              cropBoxData.maxTop = Math.min(containerData.height, canvasData.top + canvasData.height) - cropBoxData.height;
            } else {
              cropBoxData.minLeft = 0;
              cropBoxData.minTop = 0;
              cropBoxData.maxLeft = containerData.width - cropBoxData.width;
              cropBoxData.maxTop = containerData.height - cropBoxData.height;
            }
          }
        },
        renderCropBox: function renderCropBox() {
          var options = this.options, containerData = this.containerData, cropBoxData = this.cropBoxData;
          if (cropBoxData.width > cropBoxData.maxWidth || cropBoxData.width < cropBoxData.minWidth) {
            cropBoxData.left = cropBoxData.oldLeft;
          }
          if (cropBoxData.height > cropBoxData.maxHeight || cropBoxData.height < cropBoxData.minHeight) {
            cropBoxData.top = cropBoxData.oldTop;
          }
          cropBoxData.width = Math.min(Math.max(cropBoxData.width, cropBoxData.minWidth), cropBoxData.maxWidth);
          cropBoxData.height = Math.min(Math.max(cropBoxData.height, cropBoxData.minHeight), cropBoxData.maxHeight);
          this.limitCropBox(false, true);
          cropBoxData.left = Math.min(Math.max(cropBoxData.left, cropBoxData.minLeft), cropBoxData.maxLeft);
          cropBoxData.top = Math.min(Math.max(cropBoxData.top, cropBoxData.minTop), cropBoxData.maxTop);
          cropBoxData.oldLeft = cropBoxData.left;
          cropBoxData.oldTop = cropBoxData.top;
          if (options.movable && options.cropBoxMovable) {
            setData(this.face, DATA_ACTION, cropBoxData.width >= containerData.width && cropBoxData.height >= containerData.height ? ACTION_MOVE : ACTION_ALL);
          }
          setStyle(this.cropBox, assign({
            width: cropBoxData.width,
            height: cropBoxData.height
          }, getTransforms({
            translateX: cropBoxData.left,
            translateY: cropBoxData.top
          })));
          if (this.cropped && this.limited) {
            this.limitCanvas(true, true);
          }
          if (!this.disabled) {
            this.output();
          }
        },
        output: function output() {
          this.preview();
          dispatchEvent(this.element, EVENT_CROP, this.getData());
        }
      };
      var preview = {
        initPreview: function initPreview() {
          var element = this.element, crossOrigin = this.crossOrigin;
          var preview2 = this.options.preview;
          var url = crossOrigin ? this.crossOriginUrl : this.url;
          var alt = element.alt || "The image to preview";
          var image = document.createElement("img");
          if (crossOrigin) {
            image.crossOrigin = crossOrigin;
          }
          image.src = url;
          image.alt = alt;
          this.viewBox.appendChild(image);
          this.viewBoxImage = image;
          if (!preview2) {
            return;
          }
          var previews = preview2;
          if (typeof preview2 === "string") {
            previews = element.ownerDocument.querySelectorAll(preview2);
          } else if (preview2.querySelector) {
            previews = [preview2];
          }
          this.previews = previews;
          forEach(previews, function(el) {
            var img = document.createElement("img");
            setData(el, DATA_PREVIEW, {
              width: el.offsetWidth,
              height: el.offsetHeight,
              html: el.innerHTML
            });
            if (crossOrigin) {
              img.crossOrigin = crossOrigin;
            }
            img.src = url;
            img.alt = alt;
            img.style.cssText = 'display:block;width:100%;height:auto;min-width:0!important;min-height:0!important;max-width:none!important;max-height:none!important;image-orientation:0deg!important;"';
            el.innerHTML = "";
            el.appendChild(img);
          });
        },
        resetPreview: function resetPreview() {
          forEach(this.previews, function(element) {
            var data = getData(element, DATA_PREVIEW);
            setStyle(element, {
              width: data.width,
              height: data.height
            });
            element.innerHTML = data.html;
            removeData(element, DATA_PREVIEW);
          });
        },
        preview: function preview2() {
          var imageData = this.imageData, canvasData = this.canvasData, cropBoxData = this.cropBoxData;
          var cropBoxWidth = cropBoxData.width, cropBoxHeight = cropBoxData.height;
          var width = imageData.width, height = imageData.height;
          var left = cropBoxData.left - canvasData.left - imageData.left;
          var top = cropBoxData.top - canvasData.top - imageData.top;
          if (!this.cropped || this.disabled) {
            return;
          }
          setStyle(this.viewBoxImage, assign({
            width,
            height
          }, getTransforms(assign({
            translateX: -left,
            translateY: -top
          }, imageData))));
          forEach(this.previews, function(element) {
            var data = getData(element, DATA_PREVIEW);
            var originalWidth = data.width;
            var originalHeight = data.height;
            var newWidth = originalWidth;
            var newHeight = originalHeight;
            var ratio = 1;
            if (cropBoxWidth) {
              ratio = originalWidth / cropBoxWidth;
              newHeight = cropBoxHeight * ratio;
            }
            if (cropBoxHeight && newHeight > originalHeight) {
              ratio = originalHeight / cropBoxHeight;
              newWidth = cropBoxWidth * ratio;
              newHeight = originalHeight;
            }
            setStyle(element, {
              width: newWidth,
              height: newHeight
            });
            setStyle(element.getElementsByTagName("img")[0], assign({
              width: width * ratio,
              height: height * ratio
            }, getTransforms(assign({
              translateX: -left * ratio,
              translateY: -top * ratio
            }, imageData))));
          });
        }
      };
      var events = {
        bind: function bind() {
          var element = this.element, options = this.options, cropper = this.cropper;
          if (isFunction(options.cropstart)) {
            addListener(element, EVENT_CROP_START, options.cropstart);
          }
          if (isFunction(options.cropmove)) {
            addListener(element, EVENT_CROP_MOVE, options.cropmove);
          }
          if (isFunction(options.cropend)) {
            addListener(element, EVENT_CROP_END, options.cropend);
          }
          if (isFunction(options.crop)) {
            addListener(element, EVENT_CROP, options.crop);
          }
          if (isFunction(options.zoom)) {
            addListener(element, EVENT_ZOOM, options.zoom);
          }
          addListener(cropper, EVENT_POINTER_DOWN, this.onCropStart = this.cropStart.bind(this));
          if (options.zoomable && options.zoomOnWheel) {
            addListener(cropper, EVENT_WHEEL, this.onWheel = this.wheel.bind(this), {
              passive: false,
              capture: true
            });
          }
          if (options.toggleDragModeOnDblclick) {
            addListener(cropper, EVENT_DBLCLICK, this.onDblclick = this.dblclick.bind(this));
          }
          addListener(element.ownerDocument, EVENT_POINTER_MOVE, this.onCropMove = this.cropMove.bind(this));
          addListener(element.ownerDocument, EVENT_POINTER_UP, this.onCropEnd = this.cropEnd.bind(this));
          if (options.responsive) {
            addListener(window, EVENT_RESIZE, this.onResize = this.resize.bind(this));
          }
        },
        unbind: function unbind() {
          var element = this.element, options = this.options, cropper = this.cropper;
          if (isFunction(options.cropstart)) {
            removeListener(element, EVENT_CROP_START, options.cropstart);
          }
          if (isFunction(options.cropmove)) {
            removeListener(element, EVENT_CROP_MOVE, options.cropmove);
          }
          if (isFunction(options.cropend)) {
            removeListener(element, EVENT_CROP_END, options.cropend);
          }
          if (isFunction(options.crop)) {
            removeListener(element, EVENT_CROP, options.crop);
          }
          if (isFunction(options.zoom)) {
            removeListener(element, EVENT_ZOOM, options.zoom);
          }
          removeListener(cropper, EVENT_POINTER_DOWN, this.onCropStart);
          if (options.zoomable && options.zoomOnWheel) {
            removeListener(cropper, EVENT_WHEEL, this.onWheel, {
              passive: false,
              capture: true
            });
          }
          if (options.toggleDragModeOnDblclick) {
            removeListener(cropper, EVENT_DBLCLICK, this.onDblclick);
          }
          removeListener(element.ownerDocument, EVENT_POINTER_MOVE, this.onCropMove);
          removeListener(element.ownerDocument, EVENT_POINTER_UP, this.onCropEnd);
          if (options.responsive) {
            removeListener(window, EVENT_RESIZE, this.onResize);
          }
        }
      };
      var handlers = {
        resize: function resize() {
          if (this.disabled) {
            return;
          }
          var options = this.options, container = this.container, containerData = this.containerData;
          var ratioX = container.offsetWidth / containerData.width;
          var ratioY = container.offsetHeight / containerData.height;
          var ratio = Math.abs(ratioX - 1) > Math.abs(ratioY - 1) ? ratioX : ratioY;
          if (ratio !== 1) {
            var canvasData;
            var cropBoxData;
            if (options.restore) {
              canvasData = this.getCanvasData();
              cropBoxData = this.getCropBoxData();
            }
            this.render();
            if (options.restore) {
              this.setCanvasData(forEach(canvasData, function(n, i) {
                canvasData[i] = n * ratio;
              }));
              this.setCropBoxData(forEach(cropBoxData, function(n, i) {
                cropBoxData[i] = n * ratio;
              }));
            }
          }
        },
        dblclick: function dblclick() {
          if (this.disabled || this.options.dragMode === DRAG_MODE_NONE) {
            return;
          }
          this.setDragMode(hasClass(this.dragBox, CLASS_CROP) ? DRAG_MODE_MOVE : DRAG_MODE_CROP);
        },
        wheel: function wheel(event) {
          var _this = this;
          var ratio = Number(this.options.wheelZoomRatio) || 0.1;
          var delta = 1;
          if (this.disabled) {
            return;
          }
          event.preventDefault();
          if (this.wheeling) {
            return;
          }
          this.wheeling = true;
          setTimeout(function() {
            _this.wheeling = false;
          }, 50);
          if (event.deltaY) {
            delta = event.deltaY > 0 ? 1 : -1;
          } else if (event.wheelDelta) {
            delta = -event.wheelDelta / 120;
          } else if (event.detail) {
            delta = event.detail > 0 ? 1 : -1;
          }
          this.zoom(-delta * ratio, event);
        },
        cropStart: function cropStart(event) {
          var buttons = event.buttons, button = event.button;
          if (this.disabled || (event.type === "mousedown" || event.type === "pointerdown" && event.pointerType === "mouse") && (isNumber(buttons) && buttons !== 1 || isNumber(button) && button !== 0 || event.ctrlKey)) {
            return;
          }
          var options = this.options, pointers = this.pointers;
          var action;
          if (event.changedTouches) {
            forEach(event.changedTouches, function(touch) {
              pointers[touch.identifier] = getPointer(touch);
            });
          } else {
            pointers[event.pointerId || 0] = getPointer(event);
          }
          if (Object.keys(pointers).length > 1 && options.zoomable && options.zoomOnTouch) {
            action = ACTION_ZOOM;
          } else {
            action = getData(event.target, DATA_ACTION);
          }
          if (!REGEXP_ACTIONS.test(action)) {
            return;
          }
          if (dispatchEvent(this.element, EVENT_CROP_START, {
            originalEvent: event,
            action
          }) === false) {
            return;
          }
          event.preventDefault();
          this.action = action;
          this.cropping = false;
          if (action === ACTION_CROP) {
            this.cropping = true;
            addClass(this.dragBox, CLASS_MODAL);
          }
        },
        cropMove: function cropMove(event) {
          var action = this.action;
          if (this.disabled || !action) {
            return;
          }
          var pointers = this.pointers;
          event.preventDefault();
          if (dispatchEvent(this.element, EVENT_CROP_MOVE, {
            originalEvent: event,
            action
          }) === false) {
            return;
          }
          if (event.changedTouches) {
            forEach(event.changedTouches, function(touch) {
              assign(pointers[touch.identifier] || {}, getPointer(touch, true));
            });
          } else {
            assign(pointers[event.pointerId || 0] || {}, getPointer(event, true));
          }
          this.change(event);
        },
        cropEnd: function cropEnd(event) {
          if (this.disabled) {
            return;
          }
          var action = this.action, pointers = this.pointers;
          if (event.changedTouches) {
            forEach(event.changedTouches, function(touch) {
              delete pointers[touch.identifier];
            });
          } else {
            delete pointers[event.pointerId || 0];
          }
          if (!action) {
            return;
          }
          event.preventDefault();
          if (!Object.keys(pointers).length) {
            this.action = "";
          }
          if (this.cropping) {
            this.cropping = false;
            toggleClass(this.dragBox, CLASS_MODAL, this.cropped && this.options.modal);
          }
          dispatchEvent(this.element, EVENT_CROP_END, {
            originalEvent: event,
            action
          });
        }
      };
      var change = {
        change: function change2(event) {
          var options = this.options, canvasData = this.canvasData, containerData = this.containerData, cropBoxData = this.cropBoxData, pointers = this.pointers;
          var action = this.action;
          var aspectRatio = options.aspectRatio;
          var left = cropBoxData.left, top = cropBoxData.top, width = cropBoxData.width, height = cropBoxData.height;
          var right = left + width;
          var bottom = top + height;
          var minLeft = 0;
          var minTop = 0;
          var maxWidth = containerData.width;
          var maxHeight = containerData.height;
          var renderable = true;
          var offset;
          if (!aspectRatio && event.shiftKey) {
            aspectRatio = width && height ? width / height : 1;
          }
          if (this.limited) {
            minLeft = cropBoxData.minLeft;
            minTop = cropBoxData.minTop;
            maxWidth = minLeft + Math.min(containerData.width, canvasData.width, canvasData.left + canvasData.width);
            maxHeight = minTop + Math.min(containerData.height, canvasData.height, canvasData.top + canvasData.height);
          }
          var pointer = pointers[Object.keys(pointers)[0]];
          var range = {
            x: pointer.endX - pointer.startX,
            y: pointer.endY - pointer.startY
          };
          var check = function check2(side) {
            switch (side) {
              case ACTION_EAST:
                if (right + range.x > maxWidth) {
                  range.x = maxWidth - right;
                }
                break;
              case ACTION_WEST:
                if (left + range.x < minLeft) {
                  range.x = minLeft - left;
                }
                break;
              case ACTION_NORTH:
                if (top + range.y < minTop) {
                  range.y = minTop - top;
                }
                break;
              case ACTION_SOUTH:
                if (bottom + range.y > maxHeight) {
                  range.y = maxHeight - bottom;
                }
                break;
            }
          };
          switch (action) {
            case ACTION_ALL:
              left += range.x;
              top += range.y;
              break;
            case ACTION_EAST:
              if (range.x >= 0 && (right >= maxWidth || aspectRatio && (top <= minTop || bottom >= maxHeight))) {
                renderable = false;
                break;
              }
              check(ACTION_EAST);
              width += range.x;
              if (width < 0) {
                action = ACTION_WEST;
                width = -width;
                left -= width;
              }
              if (aspectRatio) {
                height = width / aspectRatio;
                top += (cropBoxData.height - height) / 2;
              }
              break;
            case ACTION_NORTH:
              if (range.y <= 0 && (top <= minTop || aspectRatio && (left <= minLeft || right >= maxWidth))) {
                renderable = false;
                break;
              }
              check(ACTION_NORTH);
              height -= range.y;
              top += range.y;
              if (height < 0) {
                action = ACTION_SOUTH;
                height = -height;
                top -= height;
              }
              if (aspectRatio) {
                width = height * aspectRatio;
                left += (cropBoxData.width - width) / 2;
              }
              break;
            case ACTION_WEST:
              if (range.x <= 0 && (left <= minLeft || aspectRatio && (top <= minTop || bottom >= maxHeight))) {
                renderable = false;
                break;
              }
              check(ACTION_WEST);
              width -= range.x;
              left += range.x;
              if (width < 0) {
                action = ACTION_EAST;
                width = -width;
                left -= width;
              }
              if (aspectRatio) {
                height = width / aspectRatio;
                top += (cropBoxData.height - height) / 2;
              }
              break;
            case ACTION_SOUTH:
              if (range.y >= 0 && (bottom >= maxHeight || aspectRatio && (left <= minLeft || right >= maxWidth))) {
                renderable = false;
                break;
              }
              check(ACTION_SOUTH);
              height += range.y;
              if (height < 0) {
                action = ACTION_NORTH;
                height = -height;
                top -= height;
              }
              if (aspectRatio) {
                width = height * aspectRatio;
                left += (cropBoxData.width - width) / 2;
              }
              break;
            case ACTION_NORTH_EAST:
              if (aspectRatio) {
                if (range.y <= 0 && (top <= minTop || right >= maxWidth)) {
                  renderable = false;
                  break;
                }
                check(ACTION_NORTH);
                height -= range.y;
                top += range.y;
                width = height * aspectRatio;
              } else {
                check(ACTION_NORTH);
                check(ACTION_EAST);
                if (range.x >= 0) {
                  if (right < maxWidth) {
                    width += range.x;
                  } else if (range.y <= 0 && top <= minTop) {
                    renderable = false;
                  }
                } else {
                  width += range.x;
                }
                if (range.y <= 0) {
                  if (top > minTop) {
                    height -= range.y;
                    top += range.y;
                  }
                } else {
                  height -= range.y;
                  top += range.y;
                }
              }
              if (width < 0 && height < 0) {
                action = ACTION_SOUTH_WEST;
                height = -height;
                width = -width;
                top -= height;
                left -= width;
              } else if (width < 0) {
                action = ACTION_NORTH_WEST;
                width = -width;
                left -= width;
              } else if (height < 0) {
                action = ACTION_SOUTH_EAST;
                height = -height;
                top -= height;
              }
              break;
            case ACTION_NORTH_WEST:
              if (aspectRatio) {
                if (range.y <= 0 && (top <= minTop || left <= minLeft)) {
                  renderable = false;
                  break;
                }
                check(ACTION_NORTH);
                height -= range.y;
                top += range.y;
                width = height * aspectRatio;
                left += cropBoxData.width - width;
              } else {
                check(ACTION_NORTH);
                check(ACTION_WEST);
                if (range.x <= 0) {
                  if (left > minLeft) {
                    width -= range.x;
                    left += range.x;
                  } else if (range.y <= 0 && top <= minTop) {
                    renderable = false;
                  }
                } else {
                  width -= range.x;
                  left += range.x;
                }
                if (range.y <= 0) {
                  if (top > minTop) {
                    height -= range.y;
                    top += range.y;
                  }
                } else {
                  height -= range.y;
                  top += range.y;
                }
              }
              if (width < 0 && height < 0) {
                action = ACTION_SOUTH_EAST;
                height = -height;
                width = -width;
                top -= height;
                left -= width;
              } else if (width < 0) {
                action = ACTION_NORTH_EAST;
                width = -width;
                left -= width;
              } else if (height < 0) {
                action = ACTION_SOUTH_WEST;
                height = -height;
                top -= height;
              }
              break;
            case ACTION_SOUTH_WEST:
              if (aspectRatio) {
                if (range.x <= 0 && (left <= minLeft || bottom >= maxHeight)) {
                  renderable = false;
                  break;
                }
                check(ACTION_WEST);
                width -= range.x;
                left += range.x;
                height = width / aspectRatio;
              } else {
                check(ACTION_SOUTH);
                check(ACTION_WEST);
                if (range.x <= 0) {
                  if (left > minLeft) {
                    width -= range.x;
                    left += range.x;
                  } else if (range.y >= 0 && bottom >= maxHeight) {
                    renderable = false;
                  }
                } else {
                  width -= range.x;
                  left += range.x;
                }
                if (range.y >= 0) {
                  if (bottom < maxHeight) {
                    height += range.y;
                  }
                } else {
                  height += range.y;
                }
              }
              if (width < 0 && height < 0) {
                action = ACTION_NORTH_EAST;
                height = -height;
                width = -width;
                top -= height;
                left -= width;
              } else if (width < 0) {
                action = ACTION_SOUTH_EAST;
                width = -width;
                left -= width;
              } else if (height < 0) {
                action = ACTION_NORTH_WEST;
                height = -height;
                top -= height;
              }
              break;
            case ACTION_SOUTH_EAST:
              if (aspectRatio) {
                if (range.x >= 0 && (right >= maxWidth || bottom >= maxHeight)) {
                  renderable = false;
                  break;
                }
                check(ACTION_EAST);
                width += range.x;
                height = width / aspectRatio;
              } else {
                check(ACTION_SOUTH);
                check(ACTION_EAST);
                if (range.x >= 0) {
                  if (right < maxWidth) {
                    width += range.x;
                  } else if (range.y >= 0 && bottom >= maxHeight) {
                    renderable = false;
                  }
                } else {
                  width += range.x;
                }
                if (range.y >= 0) {
                  if (bottom < maxHeight) {
                    height += range.y;
                  }
                } else {
                  height += range.y;
                }
              }
              if (width < 0 && height < 0) {
                action = ACTION_NORTH_WEST;
                height = -height;
                width = -width;
                top -= height;
                left -= width;
              } else if (width < 0) {
                action = ACTION_SOUTH_WEST;
                width = -width;
                left -= width;
              } else if (height < 0) {
                action = ACTION_NORTH_EAST;
                height = -height;
                top -= height;
              }
              break;
            case ACTION_MOVE:
              this.move(range.x, range.y);
              renderable = false;
              break;
            case ACTION_ZOOM:
              this.zoom(getMaxZoomRatio(pointers), event);
              renderable = false;
              break;
            case ACTION_CROP:
              if (!range.x || !range.y) {
                renderable = false;
                break;
              }
              offset = getOffset(this.cropper);
              left = pointer.startX - offset.left;
              top = pointer.startY - offset.top;
              width = cropBoxData.minWidth;
              height = cropBoxData.minHeight;
              if (range.x > 0) {
                action = range.y > 0 ? ACTION_SOUTH_EAST : ACTION_NORTH_EAST;
              } else if (range.x < 0) {
                left -= width;
                action = range.y > 0 ? ACTION_SOUTH_WEST : ACTION_NORTH_WEST;
              }
              if (range.y < 0) {
                top -= height;
              }
              if (!this.cropped) {
                removeClass(this.cropBox, CLASS_HIDDEN);
                this.cropped = true;
                if (this.limited) {
                  this.limitCropBox(true, true);
                }
              }
              break;
          }
          if (renderable) {
            cropBoxData.width = width;
            cropBoxData.height = height;
            cropBoxData.left = left;
            cropBoxData.top = top;
            this.action = action;
            this.renderCropBox();
          }
          forEach(pointers, function(p) {
            p.startX = p.endX;
            p.startY = p.endY;
          });
        }
      };
      var methods = {
        crop: function crop() {
          if (this.ready && !this.cropped && !this.disabled) {
            this.cropped = true;
            this.limitCropBox(true, true);
            if (this.options.modal) {
              addClass(this.dragBox, CLASS_MODAL);
            }
            removeClass(this.cropBox, CLASS_HIDDEN);
            this.setCropBoxData(this.initialCropBoxData);
          }
          return this;
        },
        reset: function reset() {
          if (this.ready && !this.disabled) {
            this.imageData = assign({}, this.initialImageData);
            this.canvasData = assign({}, this.initialCanvasData);
            this.cropBoxData = assign({}, this.initialCropBoxData);
            this.renderCanvas();
            if (this.cropped) {
              this.renderCropBox();
            }
          }
          return this;
        },
        clear: function clear() {
          if (this.cropped && !this.disabled) {
            assign(this.cropBoxData, {
              left: 0,
              top: 0,
              width: 0,
              height: 0
            });
            this.cropped = false;
            this.renderCropBox();
            this.limitCanvas(true, true);
            this.renderCanvas();
            removeClass(this.dragBox, CLASS_MODAL);
            addClass(this.cropBox, CLASS_HIDDEN);
          }
          return this;
        },
        replace: function replace(url) {
          var hasSameSize = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : false;
          if (!this.disabled && url) {
            if (this.isImg) {
              this.element.src = url;
            }
            if (hasSameSize) {
              this.url = url;
              this.image.src = url;
              if (this.ready) {
                this.viewBoxImage.src = url;
                forEach(this.previews, function(element) {
                  element.getElementsByTagName("img")[0].src = url;
                });
              }
            } else {
              if (this.isImg) {
                this.replaced = true;
              }
              this.options.data = null;
              this.uncreate();
              this.load(url);
            }
          }
          return this;
        },
        enable: function enable() {
          if (this.ready && this.disabled) {
            this.disabled = false;
            removeClass(this.cropper, CLASS_DISABLED);
          }
          return this;
        },
        disable: function disable() {
          if (this.ready && !this.disabled) {
            this.disabled = true;
            addClass(this.cropper, CLASS_DISABLED);
          }
          return this;
        },
        destroy: function destroy() {
          var element = this.element;
          if (!element[NAMESPACE]) {
            return this;
          }
          element[NAMESPACE] = void 0;
          if (this.isImg && this.replaced) {
            element.src = this.originalUrl;
          }
          this.uncreate();
          return this;
        },
        move: function move(offsetX) {
          var offsetY = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : offsetX;
          var _this$canvasData = this.canvasData, left = _this$canvasData.left, top = _this$canvasData.top;
          return this.moveTo(isUndefined(offsetX) ? offsetX : left + Number(offsetX), isUndefined(offsetY) ? offsetY : top + Number(offsetY));
        },
        moveTo: function moveTo(x) {
          var y = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : x;
          var canvasData = this.canvasData;
          var changed = false;
          x = Number(x);
          y = Number(y);
          if (this.ready && !this.disabled && this.options.movable) {
            if (isNumber(x)) {
              canvasData.left = x;
              changed = true;
            }
            if (isNumber(y)) {
              canvasData.top = y;
              changed = true;
            }
            if (changed) {
              this.renderCanvas(true);
            }
          }
          return this;
        },
        zoom: function zoom(ratio, _originalEvent) {
          var canvasData = this.canvasData;
          ratio = Number(ratio);
          if (ratio < 0) {
            ratio = 1 / (1 - ratio);
          } else {
            ratio = 1 + ratio;
          }
          return this.zoomTo(canvasData.width * ratio / canvasData.naturalWidth, null, _originalEvent);
        },
        zoomTo: function zoomTo(ratio, pivot, _originalEvent) {
          var options = this.options, canvasData = this.canvasData;
          var width = canvasData.width, height = canvasData.height, naturalWidth = canvasData.naturalWidth, naturalHeight = canvasData.naturalHeight;
          ratio = Number(ratio);
          if (ratio >= 0 && this.ready && !this.disabled && options.zoomable) {
            var newWidth = naturalWidth * ratio;
            var newHeight = naturalHeight * ratio;
            if (dispatchEvent(this.element, EVENT_ZOOM, {
              ratio,
              oldRatio: width / naturalWidth,
              originalEvent: _originalEvent
            }) === false) {
              return this;
            }
            if (_originalEvent) {
              var pointers = this.pointers;
              var offset = getOffset(this.cropper);
              var center = pointers && Object.keys(pointers).length ? getPointersCenter(pointers) : {
                pageX: _originalEvent.pageX,
                pageY: _originalEvent.pageY
              };
              canvasData.left -= (newWidth - width) * ((center.pageX - offset.left - canvasData.left) / width);
              canvasData.top -= (newHeight - height) * ((center.pageY - offset.top - canvasData.top) / height);
            } else if (isPlainObject(pivot) && isNumber(pivot.x) && isNumber(pivot.y)) {
              canvasData.left -= (newWidth - width) * ((pivot.x - canvasData.left) / width);
              canvasData.top -= (newHeight - height) * ((pivot.y - canvasData.top) / height);
            } else {
              canvasData.left -= (newWidth - width) / 2;
              canvasData.top -= (newHeight - height) / 2;
            }
            canvasData.width = newWidth;
            canvasData.height = newHeight;
            this.renderCanvas(true);
          }
          return this;
        },
        rotate: function rotate(degree) {
          return this.rotateTo((this.imageData.rotate || 0) + Number(degree));
        },
        rotateTo: function rotateTo(degree) {
          degree = Number(degree);
          if (isNumber(degree) && this.ready && !this.disabled && this.options.rotatable) {
            this.imageData.rotate = degree % 360;
            this.renderCanvas(true, true);
          }
          return this;
        },
        scaleX: function scaleX(_scaleX) {
          var scaleY = this.imageData.scaleY;
          return this.scale(_scaleX, isNumber(scaleY) ? scaleY : 1);
        },
        scaleY: function scaleY(_scaleY) {
          var scaleX = this.imageData.scaleX;
          return this.scale(isNumber(scaleX) ? scaleX : 1, _scaleY);
        },
        scale: function scale(scaleX) {
          var scaleY = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : scaleX;
          var imageData = this.imageData;
          var transformed = false;
          scaleX = Number(scaleX);
          scaleY = Number(scaleY);
          if (this.ready && !this.disabled && this.options.scalable) {
            if (isNumber(scaleX)) {
              imageData.scaleX = scaleX;
              transformed = true;
            }
            if (isNumber(scaleY)) {
              imageData.scaleY = scaleY;
              transformed = true;
            }
            if (transformed) {
              this.renderCanvas(true, true);
            }
          }
          return this;
        },
        getData: function getData2() {
          var rounded = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : false;
          var options = this.options, imageData = this.imageData, canvasData = this.canvasData, cropBoxData = this.cropBoxData;
          var data;
          if (this.ready && this.cropped) {
            data = {
              x: cropBoxData.left - canvasData.left,
              y: cropBoxData.top - canvasData.top,
              width: cropBoxData.width,
              height: cropBoxData.height
            };
            var ratio = imageData.width / imageData.naturalWidth;
            forEach(data, function(n, i) {
              data[i] = n / ratio;
            });
            if (rounded) {
              var bottom = Math.round(data.y + data.height);
              var right = Math.round(data.x + data.width);
              data.x = Math.round(data.x);
              data.y = Math.round(data.y);
              data.width = right - data.x;
              data.height = bottom - data.y;
            }
          } else {
            data = {
              x: 0,
              y: 0,
              width: 0,
              height: 0
            };
          }
          if (options.rotatable) {
            data.rotate = imageData.rotate || 0;
          }
          if (options.scalable) {
            data.scaleX = imageData.scaleX || 1;
            data.scaleY = imageData.scaleY || 1;
          }
          return data;
        },
        setData: function setData2(data) {
          var options = this.options, imageData = this.imageData, canvasData = this.canvasData;
          var cropBoxData = {};
          if (this.ready && !this.disabled && isPlainObject(data)) {
            var transformed = false;
            if (options.rotatable) {
              if (isNumber(data.rotate) && data.rotate !== imageData.rotate) {
                imageData.rotate = data.rotate;
                transformed = true;
              }
            }
            if (options.scalable) {
              if (isNumber(data.scaleX) && data.scaleX !== imageData.scaleX) {
                imageData.scaleX = data.scaleX;
                transformed = true;
              }
              if (isNumber(data.scaleY) && data.scaleY !== imageData.scaleY) {
                imageData.scaleY = data.scaleY;
                transformed = true;
              }
            }
            if (transformed) {
              this.renderCanvas(true, true);
            }
            var ratio = imageData.width / imageData.naturalWidth;
            if (isNumber(data.x)) {
              cropBoxData.left = data.x * ratio + canvasData.left;
            }
            if (isNumber(data.y)) {
              cropBoxData.top = data.y * ratio + canvasData.top;
            }
            if (isNumber(data.width)) {
              cropBoxData.width = data.width * ratio;
            }
            if (isNumber(data.height)) {
              cropBoxData.height = data.height * ratio;
            }
            this.setCropBoxData(cropBoxData);
          }
          return this;
        },
        getContainerData: function getContainerData() {
          return this.ready ? assign({}, this.containerData) : {};
        },
        getImageData: function getImageData() {
          return this.sized ? assign({}, this.imageData) : {};
        },
        getCanvasData: function getCanvasData() {
          var canvasData = this.canvasData;
          var data = {};
          if (this.ready) {
            forEach(["left", "top", "width", "height", "naturalWidth", "naturalHeight"], function(n) {
              data[n] = canvasData[n];
            });
          }
          return data;
        },
        setCanvasData: function setCanvasData(data) {
          var canvasData = this.canvasData;
          var aspectRatio = canvasData.aspectRatio;
          if (this.ready && !this.disabled && isPlainObject(data)) {
            if (isNumber(data.left)) {
              canvasData.left = data.left;
            }
            if (isNumber(data.top)) {
              canvasData.top = data.top;
            }
            if (isNumber(data.width)) {
              canvasData.width = data.width;
              canvasData.height = data.width / aspectRatio;
            } else if (isNumber(data.height)) {
              canvasData.height = data.height;
              canvasData.width = data.height * aspectRatio;
            }
            this.renderCanvas(true);
          }
          return this;
        },
        getCropBoxData: function getCropBoxData() {
          var cropBoxData = this.cropBoxData;
          var data;
          if (this.ready && this.cropped) {
            data = {
              left: cropBoxData.left,
              top: cropBoxData.top,
              width: cropBoxData.width,
              height: cropBoxData.height
            };
          }
          return data || {};
        },
        setCropBoxData: function setCropBoxData(data) {
          var cropBoxData = this.cropBoxData;
          var aspectRatio = this.options.aspectRatio;
          var widthChanged;
          var heightChanged;
          if (this.ready && this.cropped && !this.disabled && isPlainObject(data)) {
            if (isNumber(data.left)) {
              cropBoxData.left = data.left;
            }
            if (isNumber(data.top)) {
              cropBoxData.top = data.top;
            }
            if (isNumber(data.width) && data.width !== cropBoxData.width) {
              widthChanged = true;
              cropBoxData.width = data.width;
            }
            if (isNumber(data.height) && data.height !== cropBoxData.height) {
              heightChanged = true;
              cropBoxData.height = data.height;
            }
            if (aspectRatio) {
              if (widthChanged) {
                cropBoxData.height = cropBoxData.width / aspectRatio;
              } else if (heightChanged) {
                cropBoxData.width = cropBoxData.height * aspectRatio;
              }
            }
            this.renderCropBox();
          }
          return this;
        },
        getCroppedCanvas: function getCroppedCanvas() {
          var options = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : {};
          if (!this.ready || !window.HTMLCanvasElement) {
            return null;
          }
          var canvasData = this.canvasData;
          var source = getSourceCanvas(this.image, this.imageData, canvasData, options);
          if (!this.cropped) {
            return source;
          }
          var _this$getData = this.getData(), initialX = _this$getData.x, initialY = _this$getData.y, initialWidth = _this$getData.width, initialHeight = _this$getData.height;
          var ratio = source.width / Math.floor(canvasData.naturalWidth);
          if (ratio !== 1) {
            initialX *= ratio;
            initialY *= ratio;
            initialWidth *= ratio;
            initialHeight *= ratio;
          }
          var aspectRatio = initialWidth / initialHeight;
          var maxSizes = getAdjustedSizes({
            aspectRatio,
            width: options.maxWidth || Infinity,
            height: options.maxHeight || Infinity
          });
          var minSizes = getAdjustedSizes({
            aspectRatio,
            width: options.minWidth || 0,
            height: options.minHeight || 0
          }, "cover");
          var _getAdjustedSizes = getAdjustedSizes({
            aspectRatio,
            width: options.width || (ratio !== 1 ? source.width : initialWidth),
            height: options.height || (ratio !== 1 ? source.height : initialHeight)
          }), width = _getAdjustedSizes.width, height = _getAdjustedSizes.height;
          width = Math.min(maxSizes.width, Math.max(minSizes.width, width));
          height = Math.min(maxSizes.height, Math.max(minSizes.height, height));
          var canvas = document.createElement("canvas");
          var context = canvas.getContext("2d");
          canvas.width = normalizeDecimalNumber(width);
          canvas.height = normalizeDecimalNumber(height);
          context.fillStyle = options.fillColor || "transparent";
          context.fillRect(0, 0, width, height);
          var _options$imageSmoothi = options.imageSmoothingEnabled, imageSmoothingEnabled = _options$imageSmoothi === void 0 ? true : _options$imageSmoothi, imageSmoothingQuality = options.imageSmoothingQuality;
          context.imageSmoothingEnabled = imageSmoothingEnabled;
          if (imageSmoothingQuality) {
            context.imageSmoothingQuality = imageSmoothingQuality;
          }
          var sourceWidth = source.width;
          var sourceHeight = source.height;
          var srcX = initialX;
          var srcY = initialY;
          var srcWidth;
          var srcHeight;
          var dstX;
          var dstY;
          var dstWidth;
          var dstHeight;
          if (srcX <= -initialWidth || srcX > sourceWidth) {
            srcX = 0;
            srcWidth = 0;
            dstX = 0;
            dstWidth = 0;
          } else if (srcX <= 0) {
            dstX = -srcX;
            srcX = 0;
            srcWidth = Math.min(sourceWidth, initialWidth + srcX);
            dstWidth = srcWidth;
          } else if (srcX <= sourceWidth) {
            dstX = 0;
            srcWidth = Math.min(initialWidth, sourceWidth - srcX);
            dstWidth = srcWidth;
          }
          if (srcWidth <= 0 || srcY <= -initialHeight || srcY > sourceHeight) {
            srcY = 0;
            srcHeight = 0;
            dstY = 0;
            dstHeight = 0;
          } else if (srcY <= 0) {
            dstY = -srcY;
            srcY = 0;
            srcHeight = Math.min(sourceHeight, initialHeight + srcY);
            dstHeight = srcHeight;
          } else if (srcY <= sourceHeight) {
            dstY = 0;
            srcHeight = Math.min(initialHeight, sourceHeight - srcY);
            dstHeight = srcHeight;
          }
          var params = [srcX, srcY, srcWidth, srcHeight];
          if (dstWidth > 0 && dstHeight > 0) {
            var scale = width / initialWidth;
            params.push(dstX * scale, dstY * scale, dstWidth * scale, dstHeight * scale);
          }
          context.drawImage.apply(context, [source].concat(_toConsumableArray(params.map(function(param) {
            return Math.floor(normalizeDecimalNumber(param));
          }))));
          return canvas;
        },
        setAspectRatio: function setAspectRatio(aspectRatio) {
          var options = this.options;
          if (!this.disabled && !isUndefined(aspectRatio)) {
            options.aspectRatio = Math.max(0, aspectRatio) || NaN;
            if (this.ready) {
              this.initCropBox();
              if (this.cropped) {
                this.renderCropBox();
              }
            }
          }
          return this;
        },
        setDragMode: function setDragMode(mode) {
          var options = this.options, dragBox = this.dragBox, face = this.face;
          if (this.ready && !this.disabled) {
            var croppable = mode === DRAG_MODE_CROP;
            var movable = options.movable && mode === DRAG_MODE_MOVE;
            mode = croppable || movable ? mode : DRAG_MODE_NONE;
            options.dragMode = mode;
            setData(dragBox, DATA_ACTION, mode);
            toggleClass(dragBox, CLASS_CROP, croppable);
            toggleClass(dragBox, CLASS_MOVE, movable);
            if (!options.cropBoxMovable) {
              setData(face, DATA_ACTION, mode);
              toggleClass(face, CLASS_CROP, croppable);
              toggleClass(face, CLASS_MOVE, movable);
            }
          }
          return this;
        }
      };
      var AnotherCropper = WINDOW.Cropper;
      var Cropper2 = /* @__PURE__ */ function() {
        function Cropper3(element) {
          var options = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {};
          _classCallCheck(this, Cropper3);
          if (!element || !REGEXP_TAG_NAME.test(element.tagName)) {
            throw new Error("The first argument is required and must be an <img> or <canvas> element.");
          }
          this.element = element;
          this.options = assign({}, DEFAULTS, isPlainObject(options) && options);
          this.cropped = false;
          this.disabled = false;
          this.pointers = {};
          this.ready = false;
          this.reloading = false;
          this.replaced = false;
          this.sized = false;
          this.sizing = false;
          this.init();
        }
        _createClass(Cropper3, [{
          key: "init",
          value: function init() {
            var element = this.element;
            var tagName = element.tagName.toLowerCase();
            var url;
            if (element[NAMESPACE]) {
              return;
            }
            element[NAMESPACE] = this;
            if (tagName === "img") {
              this.isImg = true;
              url = element.getAttribute("src") || "";
              this.originalUrl = url;
              if (!url) {
                return;
              }
              url = element.src;
            } else if (tagName === "canvas" && window.HTMLCanvasElement) {
              url = element.toDataURL();
            }
            this.load(url);
          }
        }, {
          key: "load",
          value: function load(url) {
            var _this = this;
            if (!url) {
              return;
            }
            this.url = url;
            this.imageData = {};
            var element = this.element, options = this.options;
            if (!options.rotatable && !options.scalable) {
              options.checkOrientation = false;
            }
            if (!options.checkOrientation || !window.ArrayBuffer) {
              this.clone();
              return;
            }
            if (REGEXP_DATA_URL.test(url)) {
              if (REGEXP_DATA_URL_JPEG.test(url)) {
                this.read(dataURLToArrayBuffer(url));
              } else {
                this.clone();
              }
              return;
            }
            var xhr = new XMLHttpRequest();
            var clone = this.clone.bind(this);
            this.reloading = true;
            this.xhr = xhr;
            xhr.onabort = clone;
            xhr.onerror = clone;
            xhr.ontimeout = clone;
            xhr.onprogress = function() {
              if (xhr.getResponseHeader("content-type") !== MIME_TYPE_JPEG) {
                xhr.abort();
              }
            };
            xhr.onload = function() {
              _this.read(xhr.response);
            };
            xhr.onloadend = function() {
              _this.reloading = false;
              _this.xhr = null;
            };
            if (options.checkCrossOrigin && isCrossOriginURL(url) && element.crossOrigin) {
              url = addTimestamp(url);
            }
            xhr.open("GET", url, true);
            xhr.responseType = "arraybuffer";
            xhr.withCredentials = element.crossOrigin === "use-credentials";
            xhr.send();
          }
        }, {
          key: "read",
          value: function read(arrayBuffer) {
            var options = this.options, imageData = this.imageData;
            var orientation = resetAndGetOrientation(arrayBuffer);
            var rotate = 0;
            var scaleX = 1;
            var scaleY = 1;
            if (orientation > 1) {
              this.url = arrayBufferToDataURL(arrayBuffer, MIME_TYPE_JPEG);
              var _parseOrientation = parseOrientation(orientation);
              rotate = _parseOrientation.rotate;
              scaleX = _parseOrientation.scaleX;
              scaleY = _parseOrientation.scaleY;
            }
            if (options.rotatable) {
              imageData.rotate = rotate;
            }
            if (options.scalable) {
              imageData.scaleX = scaleX;
              imageData.scaleY = scaleY;
            }
            this.clone();
          }
        }, {
          key: "clone",
          value: function clone() {
            var element = this.element, url = this.url;
            var crossOrigin = element.crossOrigin;
            var crossOriginUrl = url;
            if (this.options.checkCrossOrigin && isCrossOriginURL(url)) {
              if (!crossOrigin) {
                crossOrigin = "anonymous";
              }
              crossOriginUrl = addTimestamp(url);
            }
            this.crossOrigin = crossOrigin;
            this.crossOriginUrl = crossOriginUrl;
            var image = document.createElement("img");
            if (crossOrigin) {
              image.crossOrigin = crossOrigin;
            }
            image.src = crossOriginUrl || url;
            image.alt = element.alt || "The image to crop";
            this.image = image;
            image.onload = this.start.bind(this);
            image.onerror = this.stop.bind(this);
            addClass(image, CLASS_HIDE);
            element.parentNode.insertBefore(image, element.nextSibling);
          }
        }, {
          key: "start",
          value: function start() {
            var _this2 = this;
            var image = this.image;
            image.onload = null;
            image.onerror = null;
            this.sizing = true;
            var isIOSWebKit = WINDOW.navigator && /(?:iPad|iPhone|iPod).*?AppleWebKit/i.test(WINDOW.navigator.userAgent);
            var done = function done2(naturalWidth, naturalHeight) {
              assign(_this2.imageData, {
                naturalWidth,
                naturalHeight,
                aspectRatio: naturalWidth / naturalHeight
              });
              _this2.initialImageData = assign({}, _this2.imageData);
              _this2.sizing = false;
              _this2.sized = true;
              _this2.build();
            };
            if (image.naturalWidth && !isIOSWebKit) {
              done(image.naturalWidth, image.naturalHeight);
              return;
            }
            var sizingImage = document.createElement("img");
            var body = document.body || document.documentElement;
            this.sizingImage = sizingImage;
            sizingImage.onload = function() {
              done(sizingImage.width, sizingImage.height);
              if (!isIOSWebKit) {
                body.removeChild(sizingImage);
              }
            };
            sizingImage.src = image.src;
            if (!isIOSWebKit) {
              sizingImage.style.cssText = "left:0;max-height:none!important;max-width:none!important;min-height:0!important;min-width:0!important;opacity:0;position:absolute;top:0;z-index:-1;";
              body.appendChild(sizingImage);
            }
          }
        }, {
          key: "stop",
          value: function stop() {
            var image = this.image;
            image.onload = null;
            image.onerror = null;
            image.parentNode.removeChild(image);
            this.image = null;
          }
        }, {
          key: "build",
          value: function build() {
            if (!this.sized || this.ready) {
              return;
            }
            var element = this.element, options = this.options, image = this.image;
            var container = element.parentNode;
            var template = document.createElement("div");
            template.innerHTML = TEMPLATE;
            var cropper = template.querySelector(".".concat(NAMESPACE, "-container"));
            var canvas = cropper.querySelector(".".concat(NAMESPACE, "-canvas"));
            var dragBox = cropper.querySelector(".".concat(NAMESPACE, "-drag-box"));
            var cropBox = cropper.querySelector(".".concat(NAMESPACE, "-crop-box"));
            var face = cropBox.querySelector(".".concat(NAMESPACE, "-face"));
            this.container = container;
            this.cropper = cropper;
            this.canvas = canvas;
            this.dragBox = dragBox;
            this.cropBox = cropBox;
            this.viewBox = cropper.querySelector(".".concat(NAMESPACE, "-view-box"));
            this.face = face;
            canvas.appendChild(image);
            addClass(element, CLASS_HIDDEN);
            container.insertBefore(cropper, element.nextSibling);
            removeClass(image, CLASS_HIDE);
            this.initPreview();
            this.bind();
            options.initialAspectRatio = Math.max(0, options.initialAspectRatio) || NaN;
            options.aspectRatio = Math.max(0, options.aspectRatio) || NaN;
            options.viewMode = Math.max(0, Math.min(3, Math.round(options.viewMode))) || 0;
            addClass(cropBox, CLASS_HIDDEN);
            if (!options.guides) {
              addClass(cropBox.getElementsByClassName("".concat(NAMESPACE, "-dashed")), CLASS_HIDDEN);
            }
            if (!options.center) {
              addClass(cropBox.getElementsByClassName("".concat(NAMESPACE, "-center")), CLASS_HIDDEN);
            }
            if (options.background) {
              addClass(cropper, "".concat(NAMESPACE, "-bg"));
            }
            if (!options.highlight) {
              addClass(face, CLASS_INVISIBLE);
            }
            if (options.cropBoxMovable) {
              addClass(face, CLASS_MOVE);
              setData(face, DATA_ACTION, ACTION_ALL);
            }
            if (!options.cropBoxResizable) {
              addClass(cropBox.getElementsByClassName("".concat(NAMESPACE, "-line")), CLASS_HIDDEN);
              addClass(cropBox.getElementsByClassName("".concat(NAMESPACE, "-point")), CLASS_HIDDEN);
            }
            this.render();
            this.ready = true;
            this.setDragMode(options.dragMode);
            if (options.autoCrop) {
              this.crop();
            }
            this.setData(options.data);
            if (isFunction(options.ready)) {
              addListener(element, EVENT_READY, options.ready, {
                once: true
              });
            }
            dispatchEvent(element, EVENT_READY);
          }
        }, {
          key: "unbuild",
          value: function unbuild() {
            if (!this.ready) {
              return;
            }
            this.ready = false;
            this.unbind();
            this.resetPreview();
            var parentNode = this.cropper.parentNode;
            if (parentNode) {
              parentNode.removeChild(this.cropper);
            }
            removeClass(this.element, CLASS_HIDDEN);
          }
        }, {
          key: "uncreate",
          value: function uncreate() {
            if (this.ready) {
              this.unbuild();
              this.ready = false;
              this.cropped = false;
            } else if (this.sizing) {
              this.sizingImage.onload = null;
              this.sizing = false;
              this.sized = false;
            } else if (this.reloading) {
              this.xhr.onabort = null;
              this.xhr.abort();
            } else if (this.image) {
              this.stop();
            }
          }
        }], [{
          key: "noConflict",
          value: function noConflict() {
            window.Cropper = AnotherCropper;
            return Cropper3;
          }
        }, {
          key: "setDefaults",
          value: function setDefaults(options) {
            assign(DEFAULTS, isPlainObject(options) && options);
          }
        }]);
        return Cropper3;
      }();
      assign(Cropper2.prototype, render, preview, events, handlers, change, methods);
      return Cropper2;
    });
  });

  // resources/js/plugin.js
  var import_cropperjs = __toModule(require_cropper());
  document.addEventListener("alpine:init", () => {
    Alpine.data("curator", ({
      statePath,
      types,
      initialSelection = null
    }) => ({
      statePath,
      types,
      selected: [],
      files: [],
      nextPageUrl: null,
      isFetching: false,
      async init() {
        await this.getFiles("/curator/media", initialSelection?.id);
        const observer = new IntersectionObserver(([e]) => {
          if (e.isIntersecting) {
            this.loadMoreFiles();
          }
        }, {
          rootMargin: "0px",
          threshold: [0]
        });
        observer.observe(this.$refs.loadMore);
        if (initialSelection) {
          this.setSelected(initialSelection.id);
        }
      },
      getFiles: async function(url = "/curator/media", selected = null) {
        if (selected) {
          let indicator = url.includes("?") ? "&" : "?";
          url = url + indicator + "media_id=" + selected;
        }
        this.isFetching = true;
        const response = await fetch(url);
        const result = await response.json();
        this.files = this.files ? this.files.concat(result.data) : result.data;
        this.nextPageUrl = result.next_page_url;
        this.isFetching = false;
      },
      loadMoreFiles: async function() {
        if (this.nextPageUrl) {
          this.isFetching = true;
          await this.getFiles(this.nextPageUrl, this.selected?.id);
          this.isFetching = false;
        }
      },
      searchFiles: async function(event) {
        this.isFetching = true;
        const response = await fetch("/curator/media/search?q=" + event.target.value);
        const result = await response.json();
        this.files = result.data;
        this.isFetching = false;
      },
      addNewFile: function(media = null) {
        if (media) {
          this.files = [...media, ...this.files];
          this.$nextTick(() => {
            this.setSelected(media[0].id);
          });
        }
      },
      removeFile: function(media = null) {
        if (media) {
          this.files = this.files.filter((obj) => obj.id !== media.id);
          this.selected = null;
        }
      },
      setSelected: function(mediaId = null) {
        if (!mediaId || this.selected && this.selected.id === mediaId) {
          this.selected = null;
        } else {
          this.selected = this.files.find((obj) => obj.id === mediaId);
        }
        this.$wire.setCurrentFile(this.selected);
      }
    }));
    Alpine.data("curation", ({statePath, fileName, fileType, presets = {}}) => ({
      statePath,
      filename: fileName,
      filetype: fileType,
      cropper: null,
      presets,
      preset: "custom",
      flippedHorizontally: false,
      flippedVertically: false,
      format: "jpg",
      quality: 60,
      key: null,
      finalWidth: 0,
      finalHeight: 0,
      cropBoxData: {
        left: 0,
        top: 0,
        width: 0,
        height: 0
      },
      data: {
        left: 0,
        top: 0,
        width: 0,
        height: 0,
        rotate: 0,
        scaleX: 1,
        scaleY: 1
      },
      init() {
        this.destroy();
        setTimeout(() => {
          this.cropper = new import_cropperjs.default(this.$refs.image, {
            background: false
          });
        }, 100);
        this.$watch("preset", ($value) => {
          if ($value === "custom") {
            this.cropper.reset();
            this.key = null;
            this.format = "jpg";
            this.quality = 60;
          } else {
            let containerData = this.cropper.getContainerData();
            let cropBoxData = this.cropper.getCropBoxData();
            let preset = this.presets.find((p) => p.key === $value);
            let width = preset.width;
            let height = preset.height;
            let left = Math.round((containerData.width - width) / 2);
            let top = Math.round((containerData.height - height) / 2);
            this.cropper.setCropBoxData({...cropBoxData, left, top, width, height});
            this.key = preset.key;
            this.format = preset.format;
            this.quality = preset.quality;
          }
        });
      },
      destroy() {
        if (this.cropper == null)
          return;
        this.cropper.destroy();
        this.cropper = null;
      },
      setData() {
        this.finalWidth = this.data.width;
        this.finalHeight = this.data.height;
        this.data = this.cropper.getData(true);
        this.cropBoxData = this.cropper.getCropBoxData();
      },
      updateData() {
        this.finalWidth = this.data.width;
        this.finalHeight = this.data.height;
        this.data = this.cropper.getData(true);
        this.cropBoxData = this.cropper.getCropBoxData();
      },
      setCropBoxX($event) {
        let currentCropBox = this.cropper.getCropBoxData();
        this.cropper.setCropBoxData({...currentCropBox, left: parseInt($event.target.value)});
      },
      setCropBoxY($event) {
        let currentCropBox = this.cropper.getCropBoxData();
        this.cropper.setCropBoxData({...currentCropBox, top: parseInt($event.target.value)});
      },
      setCropBoxWidth($event) {
        let currentCropBox = this.cropper.getCropBoxData();
        this.cropper.setCropBoxData({...currentCropBox, width: parseInt($event.target.value)});
      },
      setCropBoxHeight($event) {
        let currentCropBox = this.cropper.getCropBoxData();
        this.cropper.setCropBoxData({...currentCropBox, height: parseInt($event.target.value)});
      },
      flipHorizontally() {
        this.cropper.scaleX(this.flippedHorizontally ? 1 : -1);
        this.flippedHorizontally = !this.flippedHorizontally;
      },
      flipVertically() {
        this.cropper.scaleY(this.flippedVertically ? 1 : -1);
        this.flippedVertically = !this.flippedVertically;
      },
      saveCuration() {
        let data = this.cropper.getData(true);
        data = {
          ...data,
          containerData: this.cropper.getContainerData(),
          imageData: this.cropper.getImageData(),
          canvasData: this.cropper.getCanvasData(),
          croppedCanvasData: this.cropper.getCroppedCanvas(),
          format: this.format,
          quality: this.quality,
          preset: this.preset,
          key: this.key ?? this.preset
        };
        this.$wire.saveCuration(data);
      }
    }));
  });
})();
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsiLi4vLi4vbm9kZV9tb2R1bGVzL2Nyb3BwZXJqcy9kaXN0L2Nyb3BwZXIuanMiLCAiLi4vanMvcGx1Z2luLmpzIl0sCiAgInNvdXJjZXNDb250ZW50IjogWyIvKiFcbiAqIENyb3BwZXIuanMgdjEuNS4xM1xuICogaHR0cHM6Ly9mZW5neXVhbmNoZW4uZ2l0aHViLmlvL2Nyb3BwZXJqc1xuICpcbiAqIENvcHlyaWdodCAyMDE1LXByZXNlbnQgQ2hlbiBGZW5neXVhblxuICogUmVsZWFzZWQgdW5kZXIgdGhlIE1JVCBsaWNlbnNlXG4gKlxuICogRGF0ZTogMjAyMi0xMS0yMFQwNTozMDo0Ni4xMTRaXG4gKi9cblxuKGZ1bmN0aW9uIChnbG9iYWwsIGZhY3RvcnkpIHtcbiAgdHlwZW9mIGV4cG9ydHMgPT09ICdvYmplY3QnICYmIHR5cGVvZiBtb2R1bGUgIT09ICd1bmRlZmluZWQnID8gbW9kdWxlLmV4cG9ydHMgPSBmYWN0b3J5KCkgOlxuICB0eXBlb2YgZGVmaW5lID09PSAnZnVuY3Rpb24nICYmIGRlZmluZS5hbWQgPyBkZWZpbmUoZmFjdG9yeSkgOlxuICAoZ2xvYmFsID0gdHlwZW9mIGdsb2JhbFRoaXMgIT09ICd1bmRlZmluZWQnID8gZ2xvYmFsVGhpcyA6IGdsb2JhbCB8fCBzZWxmLCBnbG9iYWwuQ3JvcHBlciA9IGZhY3RvcnkoKSk7XG59KSh0aGlzLCAoZnVuY3Rpb24gKCkgeyAndXNlIHN0cmljdCc7XG5cbiAgZnVuY3Rpb24gb3duS2V5cyhvYmplY3QsIGVudW1lcmFibGVPbmx5KSB7XG4gICAgdmFyIGtleXMgPSBPYmplY3Qua2V5cyhvYmplY3QpO1xuICAgIGlmIChPYmplY3QuZ2V0T3duUHJvcGVydHlTeW1ib2xzKSB7XG4gICAgICB2YXIgc3ltYm9scyA9IE9iamVjdC5nZXRPd25Qcm9wZXJ0eVN5bWJvbHMob2JqZWN0KTtcbiAgICAgIGVudW1lcmFibGVPbmx5ICYmIChzeW1ib2xzID0gc3ltYm9scy5maWx0ZXIoZnVuY3Rpb24gKHN5bSkge1xuICAgICAgICByZXR1cm4gT2JqZWN0LmdldE93blByb3BlcnR5RGVzY3JpcHRvcihvYmplY3QsIHN5bSkuZW51bWVyYWJsZTtcbiAgICAgIH0pKSwga2V5cy5wdXNoLmFwcGx5KGtleXMsIHN5bWJvbHMpO1xuICAgIH1cbiAgICByZXR1cm4ga2V5cztcbiAgfVxuICBmdW5jdGlvbiBfb2JqZWN0U3ByZWFkMih0YXJnZXQpIHtcbiAgICBmb3IgKHZhciBpID0gMTsgaSA8IGFyZ3VtZW50cy5sZW5ndGg7IGkrKykge1xuICAgICAgdmFyIHNvdXJjZSA9IG51bGwgIT0gYXJndW1lbnRzW2ldID8gYXJndW1lbnRzW2ldIDoge307XG4gICAgICBpICUgMiA/IG93bktleXMoT2JqZWN0KHNvdXJjZSksICEwKS5mb3JFYWNoKGZ1bmN0aW9uIChrZXkpIHtcbiAgICAgICAgX2RlZmluZVByb3BlcnR5KHRhcmdldCwga2V5LCBzb3VyY2Vba2V5XSk7XG4gICAgICB9KSA6IE9iamVjdC5nZXRPd25Qcm9wZXJ0eURlc2NyaXB0b3JzID8gT2JqZWN0LmRlZmluZVByb3BlcnRpZXModGFyZ2V0LCBPYmplY3QuZ2V0T3duUHJvcGVydHlEZXNjcmlwdG9ycyhzb3VyY2UpKSA6IG93bktleXMoT2JqZWN0KHNvdXJjZSkpLmZvckVhY2goZnVuY3Rpb24gKGtleSkge1xuICAgICAgICBPYmplY3QuZGVmaW5lUHJvcGVydHkodGFyZ2V0LCBrZXksIE9iamVjdC5nZXRPd25Qcm9wZXJ0eURlc2NyaXB0b3Ioc291cmNlLCBrZXkpKTtcbiAgICAgIH0pO1xuICAgIH1cbiAgICByZXR1cm4gdGFyZ2V0O1xuICB9XG4gIGZ1bmN0aW9uIF90eXBlb2Yob2JqKSB7XG4gICAgXCJAYmFiZWwvaGVscGVycyAtIHR5cGVvZlwiO1xuXG4gICAgcmV0dXJuIF90eXBlb2YgPSBcImZ1bmN0aW9uXCIgPT0gdHlwZW9mIFN5bWJvbCAmJiBcInN5bWJvbFwiID09IHR5cGVvZiBTeW1ib2wuaXRlcmF0b3IgPyBmdW5jdGlvbiAob2JqKSB7XG4gICAgICByZXR1cm4gdHlwZW9mIG9iajtcbiAgICB9IDogZnVuY3Rpb24gKG9iaikge1xuICAgICAgcmV0dXJuIG9iaiAmJiBcImZ1bmN0aW9uXCIgPT0gdHlwZW9mIFN5bWJvbCAmJiBvYmouY29uc3RydWN0b3IgPT09IFN5bWJvbCAmJiBvYmogIT09IFN5bWJvbC5wcm90b3R5cGUgPyBcInN5bWJvbFwiIDogdHlwZW9mIG9iajtcbiAgICB9LCBfdHlwZW9mKG9iaik7XG4gIH1cbiAgZnVuY3Rpb24gX2NsYXNzQ2FsbENoZWNrKGluc3RhbmNlLCBDb25zdHJ1Y3Rvcikge1xuICAgIGlmICghKGluc3RhbmNlIGluc3RhbmNlb2YgQ29uc3RydWN0b3IpKSB7XG4gICAgICB0aHJvdyBuZXcgVHlwZUVycm9yKFwiQ2Fubm90IGNhbGwgYSBjbGFzcyBhcyBhIGZ1bmN0aW9uXCIpO1xuICAgIH1cbiAgfVxuICBmdW5jdGlvbiBfZGVmaW5lUHJvcGVydGllcyh0YXJnZXQsIHByb3BzKSB7XG4gICAgZm9yICh2YXIgaSA9IDA7IGkgPCBwcm9wcy5sZW5ndGg7IGkrKykge1xuICAgICAgdmFyIGRlc2NyaXB0b3IgPSBwcm9wc1tpXTtcbiAgICAgIGRlc2NyaXB0b3IuZW51bWVyYWJsZSA9IGRlc2NyaXB0b3IuZW51bWVyYWJsZSB8fCBmYWxzZTtcbiAgICAgIGRlc2NyaXB0b3IuY29uZmlndXJhYmxlID0gdHJ1ZTtcbiAgICAgIGlmIChcInZhbHVlXCIgaW4gZGVzY3JpcHRvcikgZGVzY3JpcHRvci53cml0YWJsZSA9IHRydWU7XG4gICAgICBPYmplY3QuZGVmaW5lUHJvcGVydHkodGFyZ2V0LCBkZXNjcmlwdG9yLmtleSwgZGVzY3JpcHRvcik7XG4gICAgfVxuICB9XG4gIGZ1bmN0aW9uIF9jcmVhdGVDbGFzcyhDb25zdHJ1Y3RvciwgcHJvdG9Qcm9wcywgc3RhdGljUHJvcHMpIHtcbiAgICBpZiAocHJvdG9Qcm9wcykgX2RlZmluZVByb3BlcnRpZXMoQ29uc3RydWN0b3IucHJvdG90eXBlLCBwcm90b1Byb3BzKTtcbiAgICBpZiAoc3RhdGljUHJvcHMpIF9kZWZpbmVQcm9wZXJ0aWVzKENvbnN0cnVjdG9yLCBzdGF0aWNQcm9wcyk7XG4gICAgT2JqZWN0LmRlZmluZVByb3BlcnR5KENvbnN0cnVjdG9yLCBcInByb3RvdHlwZVwiLCB7XG4gICAgICB3cml0YWJsZTogZmFsc2VcbiAgICB9KTtcbiAgICByZXR1cm4gQ29uc3RydWN0b3I7XG4gIH1cbiAgZnVuY3Rpb24gX2RlZmluZVByb3BlcnR5KG9iaiwga2V5LCB2YWx1ZSkge1xuICAgIGlmIChrZXkgaW4gb2JqKSB7XG4gICAgICBPYmplY3QuZGVmaW5lUHJvcGVydHkob2JqLCBrZXksIHtcbiAgICAgICAgdmFsdWU6IHZhbHVlLFxuICAgICAgICBlbnVtZXJhYmxlOiB0cnVlLFxuICAgICAgICBjb25maWd1cmFibGU6IHRydWUsXG4gICAgICAgIHdyaXRhYmxlOiB0cnVlXG4gICAgICB9KTtcbiAgICB9IGVsc2Uge1xuICAgICAgb2JqW2tleV0gPSB2YWx1ZTtcbiAgICB9XG4gICAgcmV0dXJuIG9iajtcbiAgfVxuICBmdW5jdGlvbiBfdG9Db25zdW1hYmxlQXJyYXkoYXJyKSB7XG4gICAgcmV0dXJuIF9hcnJheVdpdGhvdXRIb2xlcyhhcnIpIHx8IF9pdGVyYWJsZVRvQXJyYXkoYXJyKSB8fCBfdW5zdXBwb3J0ZWRJdGVyYWJsZVRvQXJyYXkoYXJyKSB8fCBfbm9uSXRlcmFibGVTcHJlYWQoKTtcbiAgfVxuICBmdW5jdGlvbiBfYXJyYXlXaXRob3V0SG9sZXMoYXJyKSB7XG4gICAgaWYgKEFycmF5LmlzQXJyYXkoYXJyKSkgcmV0dXJuIF9hcnJheUxpa2VUb0FycmF5KGFycik7XG4gIH1cbiAgZnVuY3Rpb24gX2l0ZXJhYmxlVG9BcnJheShpdGVyKSB7XG4gICAgaWYgKHR5cGVvZiBTeW1ib2wgIT09IFwidW5kZWZpbmVkXCIgJiYgaXRlcltTeW1ib2wuaXRlcmF0b3JdICE9IG51bGwgfHwgaXRlcltcIkBAaXRlcmF0b3JcIl0gIT0gbnVsbCkgcmV0dXJuIEFycmF5LmZyb20oaXRlcik7XG4gIH1cbiAgZnVuY3Rpb24gX3Vuc3VwcG9ydGVkSXRlcmFibGVUb0FycmF5KG8sIG1pbkxlbikge1xuICAgIGlmICghbykgcmV0dXJuO1xuICAgIGlmICh0eXBlb2YgbyA9PT0gXCJzdHJpbmdcIikgcmV0dXJuIF9hcnJheUxpa2VUb0FycmF5KG8sIG1pbkxlbik7XG4gICAgdmFyIG4gPSBPYmplY3QucHJvdG90eXBlLnRvU3RyaW5nLmNhbGwobykuc2xpY2UoOCwgLTEpO1xuICAgIGlmIChuID09PSBcIk9iamVjdFwiICYmIG8uY29uc3RydWN0b3IpIG4gPSBvLmNvbnN0cnVjdG9yLm5hbWU7XG4gICAgaWYgKG4gPT09IFwiTWFwXCIgfHwgbiA9PT0gXCJTZXRcIikgcmV0dXJuIEFycmF5LmZyb20obyk7XG4gICAgaWYgKG4gPT09IFwiQXJndW1lbnRzXCIgfHwgL14oPzpVaXxJKW50KD86OHwxNnwzMikoPzpDbGFtcGVkKT9BcnJheSQvLnRlc3QobikpIHJldHVybiBfYXJyYXlMaWtlVG9BcnJheShvLCBtaW5MZW4pO1xuICB9XG4gIGZ1bmN0aW9uIF9hcnJheUxpa2VUb0FycmF5KGFyciwgbGVuKSB7XG4gICAgaWYgKGxlbiA9PSBudWxsIHx8IGxlbiA+IGFyci5sZW5ndGgpIGxlbiA9IGFyci5sZW5ndGg7XG4gICAgZm9yICh2YXIgaSA9IDAsIGFycjIgPSBuZXcgQXJyYXkobGVuKTsgaSA8IGxlbjsgaSsrKSBhcnIyW2ldID0gYXJyW2ldO1xuICAgIHJldHVybiBhcnIyO1xuICB9XG4gIGZ1bmN0aW9uIF9ub25JdGVyYWJsZVNwcmVhZCgpIHtcbiAgICB0aHJvdyBuZXcgVHlwZUVycm9yKFwiSW52YWxpZCBhdHRlbXB0IHRvIHNwcmVhZCBub24taXRlcmFibGUgaW5zdGFuY2UuXFxuSW4gb3JkZXIgdG8gYmUgaXRlcmFibGUsIG5vbi1hcnJheSBvYmplY3RzIG11c3QgaGF2ZSBhIFtTeW1ib2wuaXRlcmF0b3JdKCkgbWV0aG9kLlwiKTtcbiAgfVxuXG4gIHZhciBJU19CUk9XU0VSID0gdHlwZW9mIHdpbmRvdyAhPT0gJ3VuZGVmaW5lZCcgJiYgdHlwZW9mIHdpbmRvdy5kb2N1bWVudCAhPT0gJ3VuZGVmaW5lZCc7XG4gIHZhciBXSU5ET1cgPSBJU19CUk9XU0VSID8gd2luZG93IDoge307XG4gIHZhciBJU19UT1VDSF9ERVZJQ0UgPSBJU19CUk9XU0VSICYmIFdJTkRPVy5kb2N1bWVudC5kb2N1bWVudEVsZW1lbnQgPyAnb250b3VjaHN0YXJ0JyBpbiBXSU5ET1cuZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50IDogZmFsc2U7XG4gIHZhciBIQVNfUE9JTlRFUl9FVkVOVCA9IElTX0JST1dTRVIgPyAnUG9pbnRlckV2ZW50JyBpbiBXSU5ET1cgOiBmYWxzZTtcbiAgdmFyIE5BTUVTUEFDRSA9ICdjcm9wcGVyJztcblxuICAvLyBBY3Rpb25zXG4gIHZhciBBQ1RJT05fQUxMID0gJ2FsbCc7XG4gIHZhciBBQ1RJT05fQ1JPUCA9ICdjcm9wJztcbiAgdmFyIEFDVElPTl9NT1ZFID0gJ21vdmUnO1xuICB2YXIgQUNUSU9OX1pPT00gPSAnem9vbSc7XG4gIHZhciBBQ1RJT05fRUFTVCA9ICdlJztcbiAgdmFyIEFDVElPTl9XRVNUID0gJ3cnO1xuICB2YXIgQUNUSU9OX1NPVVRIID0gJ3MnO1xuICB2YXIgQUNUSU9OX05PUlRIID0gJ24nO1xuICB2YXIgQUNUSU9OX05PUlRIX0VBU1QgPSAnbmUnO1xuICB2YXIgQUNUSU9OX05PUlRIX1dFU1QgPSAnbncnO1xuICB2YXIgQUNUSU9OX1NPVVRIX0VBU1QgPSAnc2UnO1xuICB2YXIgQUNUSU9OX1NPVVRIX1dFU1QgPSAnc3cnO1xuXG4gIC8vIENsYXNzZXNcbiAgdmFyIENMQVNTX0NST1AgPSBcIlwiLmNvbmNhdChOQU1FU1BBQ0UsIFwiLWNyb3BcIik7XG4gIHZhciBDTEFTU19ESVNBQkxFRCA9IFwiXCIuY29uY2F0KE5BTUVTUEFDRSwgXCItZGlzYWJsZWRcIik7XG4gIHZhciBDTEFTU19ISURERU4gPSBcIlwiLmNvbmNhdChOQU1FU1BBQ0UsIFwiLWhpZGRlblwiKTtcbiAgdmFyIENMQVNTX0hJREUgPSBcIlwiLmNvbmNhdChOQU1FU1BBQ0UsIFwiLWhpZGVcIik7XG4gIHZhciBDTEFTU19JTlZJU0lCTEUgPSBcIlwiLmNvbmNhdChOQU1FU1BBQ0UsIFwiLWludmlzaWJsZVwiKTtcbiAgdmFyIENMQVNTX01PREFMID0gXCJcIi5jb25jYXQoTkFNRVNQQUNFLCBcIi1tb2RhbFwiKTtcbiAgdmFyIENMQVNTX01PVkUgPSBcIlwiLmNvbmNhdChOQU1FU1BBQ0UsIFwiLW1vdmVcIik7XG5cbiAgLy8gRGF0YSBrZXlzXG4gIHZhciBEQVRBX0FDVElPTiA9IFwiXCIuY29uY2F0KE5BTUVTUEFDRSwgXCJBY3Rpb25cIik7XG4gIHZhciBEQVRBX1BSRVZJRVcgPSBcIlwiLmNvbmNhdChOQU1FU1BBQ0UsIFwiUHJldmlld1wiKTtcblxuICAvLyBEcmFnIG1vZGVzXG4gIHZhciBEUkFHX01PREVfQ1JPUCA9ICdjcm9wJztcbiAgdmFyIERSQUdfTU9ERV9NT1ZFID0gJ21vdmUnO1xuICB2YXIgRFJBR19NT0RFX05PTkUgPSAnbm9uZSc7XG5cbiAgLy8gRXZlbnRzXG4gIHZhciBFVkVOVF9DUk9QID0gJ2Nyb3AnO1xuICB2YXIgRVZFTlRfQ1JPUF9FTkQgPSAnY3JvcGVuZCc7XG4gIHZhciBFVkVOVF9DUk9QX01PVkUgPSAnY3JvcG1vdmUnO1xuICB2YXIgRVZFTlRfQ1JPUF9TVEFSVCA9ICdjcm9wc3RhcnQnO1xuICB2YXIgRVZFTlRfREJMQ0xJQ0sgPSAnZGJsY2xpY2snO1xuICB2YXIgRVZFTlRfVE9VQ0hfU1RBUlQgPSBJU19UT1VDSF9ERVZJQ0UgPyAndG91Y2hzdGFydCcgOiAnbW91c2Vkb3duJztcbiAgdmFyIEVWRU5UX1RPVUNIX01PVkUgPSBJU19UT1VDSF9ERVZJQ0UgPyAndG91Y2htb3ZlJyA6ICdtb3VzZW1vdmUnO1xuICB2YXIgRVZFTlRfVE9VQ0hfRU5EID0gSVNfVE9VQ0hfREVWSUNFID8gJ3RvdWNoZW5kIHRvdWNoY2FuY2VsJyA6ICdtb3VzZXVwJztcbiAgdmFyIEVWRU5UX1BPSU5URVJfRE9XTiA9IEhBU19QT0lOVEVSX0VWRU5UID8gJ3BvaW50ZXJkb3duJyA6IEVWRU5UX1RPVUNIX1NUQVJUO1xuICB2YXIgRVZFTlRfUE9JTlRFUl9NT1ZFID0gSEFTX1BPSU5URVJfRVZFTlQgPyAncG9pbnRlcm1vdmUnIDogRVZFTlRfVE9VQ0hfTU9WRTtcbiAgdmFyIEVWRU5UX1BPSU5URVJfVVAgPSBIQVNfUE9JTlRFUl9FVkVOVCA/ICdwb2ludGVydXAgcG9pbnRlcmNhbmNlbCcgOiBFVkVOVF9UT1VDSF9FTkQ7XG4gIHZhciBFVkVOVF9SRUFEWSA9ICdyZWFkeSc7XG4gIHZhciBFVkVOVF9SRVNJWkUgPSAncmVzaXplJztcbiAgdmFyIEVWRU5UX1dIRUVMID0gJ3doZWVsJztcbiAgdmFyIEVWRU5UX1pPT00gPSAnem9vbSc7XG5cbiAgLy8gTWltZSB0eXBlc1xuICB2YXIgTUlNRV9UWVBFX0pQRUcgPSAnaW1hZ2UvanBlZyc7XG5cbiAgLy8gUmVnRXhwc1xuICB2YXIgUkVHRVhQX0FDVElPTlMgPSAvXmV8d3xzfG58c2V8c3d8bmV8bnd8YWxsfGNyb3B8bW92ZXx6b29tJC87XG4gIHZhciBSRUdFWFBfREFUQV9VUkwgPSAvXmRhdGE6LztcbiAgdmFyIFJFR0VYUF9EQVRBX1VSTF9KUEVHID0gL15kYXRhOmltYWdlXFwvanBlZztiYXNlNjQsLztcbiAgdmFyIFJFR0VYUF9UQUdfTkFNRSA9IC9eaW1nfGNhbnZhcyQvaTtcblxuICAvLyBNaXNjXG4gIC8vIEluc3BpcmVkIGJ5IHRoZSBkZWZhdWx0IHdpZHRoIGFuZCBoZWlnaHQgb2YgYSBjYW52YXMgZWxlbWVudC5cbiAgdmFyIE1JTl9DT05UQUlORVJfV0lEVEggPSAyMDA7XG4gIHZhciBNSU5fQ09OVEFJTkVSX0hFSUdIVCA9IDEwMDtcblxuICB2YXIgREVGQVVMVFMgPSB7XG4gICAgLy8gRGVmaW5lIHRoZSB2aWV3IG1vZGUgb2YgdGhlIGNyb3BwZXJcbiAgICB2aWV3TW9kZTogMCxcbiAgICAvLyAwLCAxLCAyLCAzXG5cbiAgICAvLyBEZWZpbmUgdGhlIGRyYWdnaW5nIG1vZGUgb2YgdGhlIGNyb3BwZXJcbiAgICBkcmFnTW9kZTogRFJBR19NT0RFX0NST1AsXG4gICAgLy8gJ2Nyb3AnLCAnbW92ZScgb3IgJ25vbmUnXG5cbiAgICAvLyBEZWZpbmUgdGhlIGluaXRpYWwgYXNwZWN0IHJhdGlvIG9mIHRoZSBjcm9wIGJveFxuICAgIGluaXRpYWxBc3BlY3RSYXRpbzogTmFOLFxuICAgIC8vIERlZmluZSB0aGUgYXNwZWN0IHJhdGlvIG9mIHRoZSBjcm9wIGJveFxuICAgIGFzcGVjdFJhdGlvOiBOYU4sXG4gICAgLy8gQW4gb2JqZWN0IHdpdGggdGhlIHByZXZpb3VzIGNyb3BwaW5nIHJlc3VsdCBkYXRhXG4gICAgZGF0YTogbnVsbCxcbiAgICAvLyBBIHNlbGVjdG9yIGZvciBhZGRpbmcgZXh0cmEgY29udGFpbmVycyB0byBwcmV2aWV3XG4gICAgcHJldmlldzogJycsXG4gICAgLy8gUmUtcmVuZGVyIHRoZSBjcm9wcGVyIHdoZW4gcmVzaXplIHRoZSB3aW5kb3dcbiAgICByZXNwb25zaXZlOiB0cnVlLFxuICAgIC8vIFJlc3RvcmUgdGhlIGNyb3BwZWQgYXJlYSBhZnRlciByZXNpemUgdGhlIHdpbmRvd1xuICAgIHJlc3RvcmU6IHRydWUsXG4gICAgLy8gQ2hlY2sgaWYgdGhlIGN1cnJlbnQgaW1hZ2UgaXMgYSBjcm9zcy1vcmlnaW4gaW1hZ2VcbiAgICBjaGVja0Nyb3NzT3JpZ2luOiB0cnVlLFxuICAgIC8vIENoZWNrIHRoZSBjdXJyZW50IGltYWdlJ3MgRXhpZiBPcmllbnRhdGlvbiBpbmZvcm1hdGlvblxuICAgIGNoZWNrT3JpZW50YXRpb246IHRydWUsXG4gICAgLy8gU2hvdyB0aGUgYmxhY2sgbW9kYWxcbiAgICBtb2RhbDogdHJ1ZSxcbiAgICAvLyBTaG93IHRoZSBkYXNoZWQgbGluZXMgZm9yIGd1aWRpbmdcbiAgICBndWlkZXM6IHRydWUsXG4gICAgLy8gU2hvdyB0aGUgY2VudGVyIGluZGljYXRvciBmb3IgZ3VpZGluZ1xuICAgIGNlbnRlcjogdHJ1ZSxcbiAgICAvLyBTaG93IHRoZSB3aGl0ZSBtb2RhbCB0byBoaWdobGlnaHQgdGhlIGNyb3AgYm94XG4gICAgaGlnaGxpZ2h0OiB0cnVlLFxuICAgIC8vIFNob3cgdGhlIGdyaWQgYmFja2dyb3VuZFxuICAgIGJhY2tncm91bmQ6IHRydWUsXG4gICAgLy8gRW5hYmxlIHRvIGNyb3AgdGhlIGltYWdlIGF1dG9tYXRpY2FsbHkgd2hlbiBpbml0aWFsaXplXG4gICAgYXV0b0Nyb3A6IHRydWUsXG4gICAgLy8gRGVmaW5lIHRoZSBwZXJjZW50YWdlIG9mIGF1dG9tYXRpYyBjcm9wcGluZyBhcmVhIHdoZW4gaW5pdGlhbGl6ZXNcbiAgICBhdXRvQ3JvcEFyZWE6IDAuOCxcbiAgICAvLyBFbmFibGUgdG8gbW92ZSB0aGUgaW1hZ2VcbiAgICBtb3ZhYmxlOiB0cnVlLFxuICAgIC8vIEVuYWJsZSB0byByb3RhdGUgdGhlIGltYWdlXG4gICAgcm90YXRhYmxlOiB0cnVlLFxuICAgIC8vIEVuYWJsZSB0byBzY2FsZSB0aGUgaW1hZ2VcbiAgICBzY2FsYWJsZTogdHJ1ZSxcbiAgICAvLyBFbmFibGUgdG8gem9vbSB0aGUgaW1hZ2VcbiAgICB6b29tYWJsZTogdHJ1ZSxcbiAgICAvLyBFbmFibGUgdG8gem9vbSB0aGUgaW1hZ2UgYnkgZHJhZ2dpbmcgdG91Y2hcbiAgICB6b29tT25Ub3VjaDogdHJ1ZSxcbiAgICAvLyBFbmFibGUgdG8gem9vbSB0aGUgaW1hZ2UgYnkgd2hlZWxpbmcgbW91c2VcbiAgICB6b29tT25XaGVlbDogdHJ1ZSxcbiAgICAvLyBEZWZpbmUgem9vbSByYXRpbyB3aGVuIHpvb20gdGhlIGltYWdlIGJ5IHdoZWVsaW5nIG1vdXNlXG4gICAgd2hlZWxab29tUmF0aW86IDAuMSxcbiAgICAvLyBFbmFibGUgdG8gbW92ZSB0aGUgY3JvcCBib3hcbiAgICBjcm9wQm94TW92YWJsZTogdHJ1ZSxcbiAgICAvLyBFbmFibGUgdG8gcmVzaXplIHRoZSBjcm9wIGJveFxuICAgIGNyb3BCb3hSZXNpemFibGU6IHRydWUsXG4gICAgLy8gVG9nZ2xlIGRyYWcgbW9kZSBiZXR3ZWVuIFwiY3JvcFwiIGFuZCBcIm1vdmVcIiB3aGVuIGNsaWNrIHR3aWNlIG9uIHRoZSBjcm9wcGVyXG4gICAgdG9nZ2xlRHJhZ01vZGVPbkRibGNsaWNrOiB0cnVlLFxuICAgIC8vIFNpemUgbGltaXRhdGlvblxuICAgIG1pbkNhbnZhc1dpZHRoOiAwLFxuICAgIG1pbkNhbnZhc0hlaWdodDogMCxcbiAgICBtaW5Dcm9wQm94V2lkdGg6IDAsXG4gICAgbWluQ3JvcEJveEhlaWdodDogMCxcbiAgICBtaW5Db250YWluZXJXaWR0aDogTUlOX0NPTlRBSU5FUl9XSURUSCxcbiAgICBtaW5Db250YWluZXJIZWlnaHQ6IE1JTl9DT05UQUlORVJfSEVJR0hULFxuICAgIC8vIFNob3J0Y3V0cyBvZiBldmVudHNcbiAgICByZWFkeTogbnVsbCxcbiAgICBjcm9wc3RhcnQ6IG51bGwsXG4gICAgY3JvcG1vdmU6IG51bGwsXG4gICAgY3JvcGVuZDogbnVsbCxcbiAgICBjcm9wOiBudWxsLFxuICAgIHpvb206IG51bGxcbiAgfTtcblxuICB2YXIgVEVNUExBVEUgPSAnPGRpdiBjbGFzcz1cImNyb3BwZXItY29udGFpbmVyXCIgdG91Y2gtYWN0aW9uPVwibm9uZVwiPicgKyAnPGRpdiBjbGFzcz1cImNyb3BwZXItd3JhcC1ib3hcIj4nICsgJzxkaXYgY2xhc3M9XCJjcm9wcGVyLWNhbnZhc1wiPjwvZGl2PicgKyAnPC9kaXY+JyArICc8ZGl2IGNsYXNzPVwiY3JvcHBlci1kcmFnLWJveFwiPjwvZGl2PicgKyAnPGRpdiBjbGFzcz1cImNyb3BwZXItY3JvcC1ib3hcIj4nICsgJzxzcGFuIGNsYXNzPVwiY3JvcHBlci12aWV3LWJveFwiPjwvc3Bhbj4nICsgJzxzcGFuIGNsYXNzPVwiY3JvcHBlci1kYXNoZWQgZGFzaGVkLWhcIj48L3NwYW4+JyArICc8c3BhbiBjbGFzcz1cImNyb3BwZXItZGFzaGVkIGRhc2hlZC12XCI+PC9zcGFuPicgKyAnPHNwYW4gY2xhc3M9XCJjcm9wcGVyLWNlbnRlclwiPjwvc3Bhbj4nICsgJzxzcGFuIGNsYXNzPVwiY3JvcHBlci1mYWNlXCI+PC9zcGFuPicgKyAnPHNwYW4gY2xhc3M9XCJjcm9wcGVyLWxpbmUgbGluZS1lXCIgZGF0YS1jcm9wcGVyLWFjdGlvbj1cImVcIj48L3NwYW4+JyArICc8c3BhbiBjbGFzcz1cImNyb3BwZXItbGluZSBsaW5lLW5cIiBkYXRhLWNyb3BwZXItYWN0aW9uPVwiblwiPjwvc3Bhbj4nICsgJzxzcGFuIGNsYXNzPVwiY3JvcHBlci1saW5lIGxpbmUtd1wiIGRhdGEtY3JvcHBlci1hY3Rpb249XCJ3XCI+PC9zcGFuPicgKyAnPHNwYW4gY2xhc3M9XCJjcm9wcGVyLWxpbmUgbGluZS1zXCIgZGF0YS1jcm9wcGVyLWFjdGlvbj1cInNcIj48L3NwYW4+JyArICc8c3BhbiBjbGFzcz1cImNyb3BwZXItcG9pbnQgcG9pbnQtZVwiIGRhdGEtY3JvcHBlci1hY3Rpb249XCJlXCI+PC9zcGFuPicgKyAnPHNwYW4gY2xhc3M9XCJjcm9wcGVyLXBvaW50IHBvaW50LW5cIiBkYXRhLWNyb3BwZXItYWN0aW9uPVwiblwiPjwvc3Bhbj4nICsgJzxzcGFuIGNsYXNzPVwiY3JvcHBlci1wb2ludCBwb2ludC13XCIgZGF0YS1jcm9wcGVyLWFjdGlvbj1cIndcIj48L3NwYW4+JyArICc8c3BhbiBjbGFzcz1cImNyb3BwZXItcG9pbnQgcG9pbnQtc1wiIGRhdGEtY3JvcHBlci1hY3Rpb249XCJzXCI+PC9zcGFuPicgKyAnPHNwYW4gY2xhc3M9XCJjcm9wcGVyLXBvaW50IHBvaW50LW5lXCIgZGF0YS1jcm9wcGVyLWFjdGlvbj1cIm5lXCI+PC9zcGFuPicgKyAnPHNwYW4gY2xhc3M9XCJjcm9wcGVyLXBvaW50IHBvaW50LW53XCIgZGF0YS1jcm9wcGVyLWFjdGlvbj1cIm53XCI+PC9zcGFuPicgKyAnPHNwYW4gY2xhc3M9XCJjcm9wcGVyLXBvaW50IHBvaW50LXN3XCIgZGF0YS1jcm9wcGVyLWFjdGlvbj1cInN3XCI+PC9zcGFuPicgKyAnPHNwYW4gY2xhc3M9XCJjcm9wcGVyLXBvaW50IHBvaW50LXNlXCIgZGF0YS1jcm9wcGVyLWFjdGlvbj1cInNlXCI+PC9zcGFuPicgKyAnPC9kaXY+JyArICc8L2Rpdj4nO1xuXG4gIC8qKlxuICAgKiBDaGVjayBpZiB0aGUgZ2l2ZW4gdmFsdWUgaXMgbm90IGEgbnVtYmVyLlxuICAgKi9cbiAgdmFyIGlzTmFOID0gTnVtYmVyLmlzTmFOIHx8IFdJTkRPVy5pc05hTjtcblxuICAvKipcbiAgICogQ2hlY2sgaWYgdGhlIGdpdmVuIHZhbHVlIGlzIGEgbnVtYmVyLlxuICAgKiBAcGFyYW0geyp9IHZhbHVlIC0gVGhlIHZhbHVlIHRvIGNoZWNrLlxuICAgKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgdGhlIGdpdmVuIHZhbHVlIGlzIGEgbnVtYmVyLCBlbHNlIGBmYWxzZWAuXG4gICAqL1xuICBmdW5jdGlvbiBpc051bWJlcih2YWx1ZSkge1xuICAgIHJldHVybiB0eXBlb2YgdmFsdWUgPT09ICdudW1iZXInICYmICFpc05hTih2YWx1ZSk7XG4gIH1cblxuICAvKipcbiAgICogQ2hlY2sgaWYgdGhlIGdpdmVuIHZhbHVlIGlzIGEgcG9zaXRpdmUgbnVtYmVyLlxuICAgKiBAcGFyYW0geyp9IHZhbHVlIC0gVGhlIHZhbHVlIHRvIGNoZWNrLlxuICAgKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgdGhlIGdpdmVuIHZhbHVlIGlzIGEgcG9zaXRpdmUgbnVtYmVyLCBlbHNlIGBmYWxzZWAuXG4gICAqL1xuICB2YXIgaXNQb3NpdGl2ZU51bWJlciA9IGZ1bmN0aW9uIGlzUG9zaXRpdmVOdW1iZXIodmFsdWUpIHtcbiAgICByZXR1cm4gdmFsdWUgPiAwICYmIHZhbHVlIDwgSW5maW5pdHk7XG4gIH07XG5cbiAgLyoqXG4gICAqIENoZWNrIGlmIHRoZSBnaXZlbiB2YWx1ZSBpcyB1bmRlZmluZWQuXG4gICAqIEBwYXJhbSB7Kn0gdmFsdWUgLSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gICAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiB0aGUgZ2l2ZW4gdmFsdWUgaXMgdW5kZWZpbmVkLCBlbHNlIGBmYWxzZWAuXG4gICAqL1xuICBmdW5jdGlvbiBpc1VuZGVmaW5lZCh2YWx1ZSkge1xuICAgIHJldHVybiB0eXBlb2YgdmFsdWUgPT09ICd1bmRlZmluZWQnO1xuICB9XG5cbiAgLyoqXG4gICAqIENoZWNrIGlmIHRoZSBnaXZlbiB2YWx1ZSBpcyBhbiBvYmplY3QuXG4gICAqIEBwYXJhbSB7Kn0gdmFsdWUgLSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gICAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiB0aGUgZ2l2ZW4gdmFsdWUgaXMgYW4gb2JqZWN0LCBlbHNlIGBmYWxzZWAuXG4gICAqL1xuICBmdW5jdGlvbiBpc09iamVjdCh2YWx1ZSkge1xuICAgIHJldHVybiBfdHlwZW9mKHZhbHVlKSA9PT0gJ29iamVjdCcgJiYgdmFsdWUgIT09IG51bGw7XG4gIH1cbiAgdmFyIGhhc093blByb3BlcnR5ID0gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eTtcblxuICAvKipcbiAgICogQ2hlY2sgaWYgdGhlIGdpdmVuIHZhbHVlIGlzIGEgcGxhaW4gb2JqZWN0LlxuICAgKiBAcGFyYW0geyp9IHZhbHVlIC0gVGhlIHZhbHVlIHRvIGNoZWNrLlxuICAgKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgdGhlIGdpdmVuIHZhbHVlIGlzIGEgcGxhaW4gb2JqZWN0LCBlbHNlIGBmYWxzZWAuXG4gICAqL1xuICBmdW5jdGlvbiBpc1BsYWluT2JqZWN0KHZhbHVlKSB7XG4gICAgaWYgKCFpc09iamVjdCh2YWx1ZSkpIHtcbiAgICAgIHJldHVybiBmYWxzZTtcbiAgICB9XG4gICAgdHJ5IHtcbiAgICAgIHZhciBfY29uc3RydWN0b3IgPSB2YWx1ZS5jb25zdHJ1Y3RvcjtcbiAgICAgIHZhciBwcm90b3R5cGUgPSBfY29uc3RydWN0b3IucHJvdG90eXBlO1xuICAgICAgcmV0dXJuIF9jb25zdHJ1Y3RvciAmJiBwcm90b3R5cGUgJiYgaGFzT3duUHJvcGVydHkuY2FsbChwcm90b3R5cGUsICdpc1Byb3RvdHlwZU9mJyk7XG4gICAgfSBjYXRjaCAoZXJyb3IpIHtcbiAgICAgIHJldHVybiBmYWxzZTtcbiAgICB9XG4gIH1cblxuICAvKipcbiAgICogQ2hlY2sgaWYgdGhlIGdpdmVuIHZhbHVlIGlzIGEgZnVuY3Rpb24uXG4gICAqIEBwYXJhbSB7Kn0gdmFsdWUgLSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gICAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiB0aGUgZ2l2ZW4gdmFsdWUgaXMgYSBmdW5jdGlvbiwgZWxzZSBgZmFsc2VgLlxuICAgKi9cbiAgZnVuY3Rpb24gaXNGdW5jdGlvbih2YWx1ZSkge1xuICAgIHJldHVybiB0eXBlb2YgdmFsdWUgPT09ICdmdW5jdGlvbic7XG4gIH1cbiAgdmFyIHNsaWNlID0gQXJyYXkucHJvdG90eXBlLnNsaWNlO1xuXG4gIC8qKlxuICAgKiBDb252ZXJ0IGFycmF5LWxpa2Ugb3IgaXRlcmFibGUgb2JqZWN0IHRvIGFuIGFycmF5LlxuICAgKiBAcGFyYW0geyp9IHZhbHVlIC0gVGhlIHZhbHVlIHRvIGNvbnZlcnQuXG4gICAqIEByZXR1cm5zIHtBcnJheX0gUmV0dXJucyBhIG5ldyBhcnJheS5cbiAgICovXG4gIGZ1bmN0aW9uIHRvQXJyYXkodmFsdWUpIHtcbiAgICByZXR1cm4gQXJyYXkuZnJvbSA/IEFycmF5LmZyb20odmFsdWUpIDogc2xpY2UuY2FsbCh2YWx1ZSk7XG4gIH1cblxuICAvKipcbiAgICogSXRlcmF0ZSB0aGUgZ2l2ZW4gZGF0YS5cbiAgICogQHBhcmFtIHsqfSBkYXRhIC0gVGhlIGRhdGEgdG8gaXRlcmF0ZS5cbiAgICogQHBhcmFtIHtGdW5jdGlvbn0gY2FsbGJhY2sgLSBUaGUgcHJvY2VzcyBmdW5jdGlvbiBmb3IgZWFjaCBlbGVtZW50LlxuICAgKiBAcmV0dXJucyB7Kn0gVGhlIG9yaWdpbmFsIGRhdGEuXG4gICAqL1xuICBmdW5jdGlvbiBmb3JFYWNoKGRhdGEsIGNhbGxiYWNrKSB7XG4gICAgaWYgKGRhdGEgJiYgaXNGdW5jdGlvbihjYWxsYmFjaykpIHtcbiAgICAgIGlmIChBcnJheS5pc0FycmF5KGRhdGEpIHx8IGlzTnVtYmVyKGRhdGEubGVuZ3RoKSAvKiBhcnJheS1saWtlICovKSB7XG4gICAgICAgIHRvQXJyYXkoZGF0YSkuZm9yRWFjaChmdW5jdGlvbiAodmFsdWUsIGtleSkge1xuICAgICAgICAgIGNhbGxiYWNrLmNhbGwoZGF0YSwgdmFsdWUsIGtleSwgZGF0YSk7XG4gICAgICAgIH0pO1xuICAgICAgfSBlbHNlIGlmIChpc09iamVjdChkYXRhKSkge1xuICAgICAgICBPYmplY3Qua2V5cyhkYXRhKS5mb3JFYWNoKGZ1bmN0aW9uIChrZXkpIHtcbiAgICAgICAgICBjYWxsYmFjay5jYWxsKGRhdGEsIGRhdGFba2V5XSwga2V5LCBkYXRhKTtcbiAgICAgICAgfSk7XG4gICAgICB9XG4gICAgfVxuICAgIHJldHVybiBkYXRhO1xuICB9XG5cbiAgLyoqXG4gICAqIEV4dGVuZCB0aGUgZ2l2ZW4gb2JqZWN0LlxuICAgKiBAcGFyYW0geyp9IHRhcmdldCAtIFRoZSB0YXJnZXQgb2JqZWN0IHRvIGV4dGVuZC5cbiAgICogQHBhcmFtIHsqfSBhcmdzIC0gVGhlIHJlc3Qgb2JqZWN0cyBmb3IgbWVyZ2luZyB0byB0aGUgdGFyZ2V0IG9iamVjdC5cbiAgICogQHJldHVybnMge09iamVjdH0gVGhlIGV4dGVuZGVkIG9iamVjdC5cbiAgICovXG4gIHZhciBhc3NpZ24gPSBPYmplY3QuYXNzaWduIHx8IGZ1bmN0aW9uIGFzc2lnbih0YXJnZXQpIHtcbiAgICBmb3IgKHZhciBfbGVuID0gYXJndW1lbnRzLmxlbmd0aCwgYXJncyA9IG5ldyBBcnJheShfbGVuID4gMSA/IF9sZW4gLSAxIDogMCksIF9rZXkgPSAxOyBfa2V5IDwgX2xlbjsgX2tleSsrKSB7XG4gICAgICBhcmdzW19rZXkgLSAxXSA9IGFyZ3VtZW50c1tfa2V5XTtcbiAgICB9XG4gICAgaWYgKGlzT2JqZWN0KHRhcmdldCkgJiYgYXJncy5sZW5ndGggPiAwKSB7XG4gICAgICBhcmdzLmZvckVhY2goZnVuY3Rpb24gKGFyZykge1xuICAgICAgICBpZiAoaXNPYmplY3QoYXJnKSkge1xuICAgICAgICAgIE9iamVjdC5rZXlzKGFyZykuZm9yRWFjaChmdW5jdGlvbiAoa2V5KSB7XG4gICAgICAgICAgICB0YXJnZXRba2V5XSA9IGFyZ1trZXldO1xuICAgICAgICAgIH0pO1xuICAgICAgICB9XG4gICAgICB9KTtcbiAgICB9XG4gICAgcmV0dXJuIHRhcmdldDtcbiAgfTtcbiAgdmFyIFJFR0VYUF9ERUNJTUFMUyA9IC9cXC5cXGQqKD86MHw5KXsxMn1cXGQqJC87XG5cbiAgLyoqXG4gICAqIE5vcm1hbGl6ZSBkZWNpbWFsIG51bWJlci5cbiAgICogQ2hlY2sgb3V0IHtAbGluayBodHRwczovLzAuMzAwMDAwMDAwMDAwMDAwMDQuY29tL31cbiAgICogQHBhcmFtIHtudW1iZXJ9IHZhbHVlIC0gVGhlIHZhbHVlIHRvIG5vcm1hbGl6ZS5cbiAgICogQHBhcmFtIHtudW1iZXJ9IFt0aW1lcz0xMDAwMDAwMDAwMDBdIC0gVGhlIHRpbWVzIGZvciBub3JtYWxpemluZy5cbiAgICogQHJldHVybnMge251bWJlcn0gUmV0dXJucyB0aGUgbm9ybWFsaXplZCBudW1iZXIuXG4gICAqL1xuICBmdW5jdGlvbiBub3JtYWxpemVEZWNpbWFsTnVtYmVyKHZhbHVlKSB7XG4gICAgdmFyIHRpbWVzID0gYXJndW1lbnRzLmxlbmd0aCA+IDEgJiYgYXJndW1lbnRzWzFdICE9PSB1bmRlZmluZWQgPyBhcmd1bWVudHNbMV0gOiAxMDAwMDAwMDAwMDA7XG4gICAgcmV0dXJuIFJFR0VYUF9ERUNJTUFMUy50ZXN0KHZhbHVlKSA/IE1hdGgucm91bmQodmFsdWUgKiB0aW1lcykgLyB0aW1lcyA6IHZhbHVlO1xuICB9XG4gIHZhciBSRUdFWFBfU1VGRklYID0gL153aWR0aHxoZWlnaHR8bGVmdHx0b3B8bWFyZ2luTGVmdHxtYXJnaW5Ub3AkLztcblxuICAvKipcbiAgICogQXBwbHkgc3R5bGVzIHRvIHRoZSBnaXZlbiBlbGVtZW50LlxuICAgKiBAcGFyYW0ge0VsZW1lbnR9IGVsZW1lbnQgLSBUaGUgdGFyZ2V0IGVsZW1lbnQuXG4gICAqIEBwYXJhbSB7T2JqZWN0fSBzdHlsZXMgLSBUaGUgc3R5bGVzIGZvciBhcHBseWluZy5cbiAgICovXG4gIGZ1bmN0aW9uIHNldFN0eWxlKGVsZW1lbnQsIHN0eWxlcykge1xuICAgIHZhciBzdHlsZSA9IGVsZW1lbnQuc3R5bGU7XG4gICAgZm9yRWFjaChzdHlsZXMsIGZ1bmN0aW9uICh2YWx1ZSwgcHJvcGVydHkpIHtcbiAgICAgIGlmIChSRUdFWFBfU1VGRklYLnRlc3QocHJvcGVydHkpICYmIGlzTnVtYmVyKHZhbHVlKSkge1xuICAgICAgICB2YWx1ZSA9IFwiXCIuY29uY2F0KHZhbHVlLCBcInB4XCIpO1xuICAgICAgfVxuICAgICAgc3R5bGVbcHJvcGVydHldID0gdmFsdWU7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogQ2hlY2sgaWYgdGhlIGdpdmVuIGVsZW1lbnQgaGFzIGEgc3BlY2lhbCBjbGFzcy5cbiAgICogQHBhcmFtIHtFbGVtZW50fSBlbGVtZW50IC0gVGhlIGVsZW1lbnQgdG8gY2hlY2suXG4gICAqIEBwYXJhbSB7c3RyaW5nfSB2YWx1ZSAtIFRoZSBjbGFzcyB0byBzZWFyY2guXG4gICAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiB0aGUgc3BlY2lhbCBjbGFzcyB3YXMgZm91bmQuXG4gICAqL1xuICBmdW5jdGlvbiBoYXNDbGFzcyhlbGVtZW50LCB2YWx1ZSkge1xuICAgIHJldHVybiBlbGVtZW50LmNsYXNzTGlzdCA/IGVsZW1lbnQuY2xhc3NMaXN0LmNvbnRhaW5zKHZhbHVlKSA6IGVsZW1lbnQuY2xhc3NOYW1lLmluZGV4T2YodmFsdWUpID4gLTE7XG4gIH1cblxuICAvKipcbiAgICogQWRkIGNsYXNzZXMgdG8gdGhlIGdpdmVuIGVsZW1lbnQuXG4gICAqIEBwYXJhbSB7RWxlbWVudH0gZWxlbWVudCAtIFRoZSB0YXJnZXQgZWxlbWVudC5cbiAgICogQHBhcmFtIHtzdHJpbmd9IHZhbHVlIC0gVGhlIGNsYXNzZXMgdG8gYmUgYWRkZWQuXG4gICAqL1xuICBmdW5jdGlvbiBhZGRDbGFzcyhlbGVtZW50LCB2YWx1ZSkge1xuICAgIGlmICghdmFsdWUpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgaWYgKGlzTnVtYmVyKGVsZW1lbnQubGVuZ3RoKSkge1xuICAgICAgZm9yRWFjaChlbGVtZW50LCBmdW5jdGlvbiAoZWxlbSkge1xuICAgICAgICBhZGRDbGFzcyhlbGVtLCB2YWx1ZSk7XG4gICAgICB9KTtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgaWYgKGVsZW1lbnQuY2xhc3NMaXN0KSB7XG4gICAgICBlbGVtZW50LmNsYXNzTGlzdC5hZGQodmFsdWUpO1xuICAgICAgcmV0dXJuO1xuICAgIH1cbiAgICB2YXIgY2xhc3NOYW1lID0gZWxlbWVudC5jbGFzc05hbWUudHJpbSgpO1xuICAgIGlmICghY2xhc3NOYW1lKSB7XG4gICAgICBlbGVtZW50LmNsYXNzTmFtZSA9IHZhbHVlO1xuICAgIH0gZWxzZSBpZiAoY2xhc3NOYW1lLmluZGV4T2YodmFsdWUpIDwgMCkge1xuICAgICAgZWxlbWVudC5jbGFzc05hbWUgPSBcIlwiLmNvbmNhdChjbGFzc05hbWUsIFwiIFwiKS5jb25jYXQodmFsdWUpO1xuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgKiBSZW1vdmUgY2xhc3NlcyBmcm9tIHRoZSBnaXZlbiBlbGVtZW50LlxuICAgKiBAcGFyYW0ge0VsZW1lbnR9IGVsZW1lbnQgLSBUaGUgdGFyZ2V0IGVsZW1lbnQuXG4gICAqIEBwYXJhbSB7c3RyaW5nfSB2YWx1ZSAtIFRoZSBjbGFzc2VzIHRvIGJlIHJlbW92ZWQuXG4gICAqL1xuICBmdW5jdGlvbiByZW1vdmVDbGFzcyhlbGVtZW50LCB2YWx1ZSkge1xuICAgIGlmICghdmFsdWUpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgaWYgKGlzTnVtYmVyKGVsZW1lbnQubGVuZ3RoKSkge1xuICAgICAgZm9yRWFjaChlbGVtZW50LCBmdW5jdGlvbiAoZWxlbSkge1xuICAgICAgICByZW1vdmVDbGFzcyhlbGVtLCB2YWx1ZSk7XG4gICAgICB9KTtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgaWYgKGVsZW1lbnQuY2xhc3NMaXN0KSB7XG4gICAgICBlbGVtZW50LmNsYXNzTGlzdC5yZW1vdmUodmFsdWUpO1xuICAgICAgcmV0dXJuO1xuICAgIH1cbiAgICBpZiAoZWxlbWVudC5jbGFzc05hbWUuaW5kZXhPZih2YWx1ZSkgPj0gMCkge1xuICAgICAgZWxlbWVudC5jbGFzc05hbWUgPSBlbGVtZW50LmNsYXNzTmFtZS5yZXBsYWNlKHZhbHVlLCAnJyk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIEFkZCBvciByZW1vdmUgY2xhc3NlcyBmcm9tIHRoZSBnaXZlbiBlbGVtZW50LlxuICAgKiBAcGFyYW0ge0VsZW1lbnR9IGVsZW1lbnQgLSBUaGUgdGFyZ2V0IGVsZW1lbnQuXG4gICAqIEBwYXJhbSB7c3RyaW5nfSB2YWx1ZSAtIFRoZSBjbGFzc2VzIHRvIGJlIHRvZ2dsZWQuXG4gICAqIEBwYXJhbSB7Ym9vbGVhbn0gYWRkZWQgLSBBZGQgb25seS5cbiAgICovXG4gIGZ1bmN0aW9uIHRvZ2dsZUNsYXNzKGVsZW1lbnQsIHZhbHVlLCBhZGRlZCkge1xuICAgIGlmICghdmFsdWUpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgaWYgKGlzTnVtYmVyKGVsZW1lbnQubGVuZ3RoKSkge1xuICAgICAgZm9yRWFjaChlbGVtZW50LCBmdW5jdGlvbiAoZWxlbSkge1xuICAgICAgICB0b2dnbGVDbGFzcyhlbGVtLCB2YWx1ZSwgYWRkZWQpO1xuICAgICAgfSk7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgLy8gSUUxMC0xMSBkb2Vzbid0IHN1cHBvcnQgdGhlIHNlY29uZCBwYXJhbWV0ZXIgb2YgYGNsYXNzTGlzdC50b2dnbGVgXG4gICAgaWYgKGFkZGVkKSB7XG4gICAgICBhZGRDbGFzcyhlbGVtZW50LCB2YWx1ZSk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHJlbW92ZUNsYXNzKGVsZW1lbnQsIHZhbHVlKTtcbiAgICB9XG4gIH1cbiAgdmFyIFJFR0VYUF9DQU1FTF9DQVNFID0gLyhbYS16XFxkXSkoW0EtWl0pL2c7XG5cbiAgLyoqXG4gICAqIFRyYW5zZm9ybSB0aGUgZ2l2ZW4gc3RyaW5nIGZyb20gY2FtZWxDYXNlIHRvIGtlYmFiLWNhc2VcbiAgICogQHBhcmFtIHtzdHJpbmd9IHZhbHVlIC0gVGhlIHZhbHVlIHRvIHRyYW5zZm9ybS5cbiAgICogQHJldHVybnMge3N0cmluZ30gVGhlIHRyYW5zZm9ybWVkIHZhbHVlLlxuICAgKi9cbiAgZnVuY3Rpb24gdG9QYXJhbUNhc2UodmFsdWUpIHtcbiAgICByZXR1cm4gdmFsdWUucmVwbGFjZShSRUdFWFBfQ0FNRUxfQ0FTRSwgJyQxLSQyJykudG9Mb3dlckNhc2UoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgZGF0YSBmcm9tIHRoZSBnaXZlbiBlbGVtZW50LlxuICAgKiBAcGFyYW0ge0VsZW1lbnR9IGVsZW1lbnQgLSBUaGUgdGFyZ2V0IGVsZW1lbnQuXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBuYW1lIC0gVGhlIGRhdGEga2V5IHRvIGdldC5cbiAgICogQHJldHVybnMge3N0cmluZ30gVGhlIGRhdGEgdmFsdWUuXG4gICAqL1xuICBmdW5jdGlvbiBnZXREYXRhKGVsZW1lbnQsIG5hbWUpIHtcbiAgICBpZiAoaXNPYmplY3QoZWxlbWVudFtuYW1lXSkpIHtcbiAgICAgIHJldHVybiBlbGVtZW50W25hbWVdO1xuICAgIH1cbiAgICBpZiAoZWxlbWVudC5kYXRhc2V0KSB7XG4gICAgICByZXR1cm4gZWxlbWVudC5kYXRhc2V0W25hbWVdO1xuICAgIH1cbiAgICByZXR1cm4gZWxlbWVudC5nZXRBdHRyaWJ1dGUoXCJkYXRhLVwiLmNvbmNhdCh0b1BhcmFtQ2FzZShuYW1lKSkpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNldCBkYXRhIHRvIHRoZSBnaXZlbiBlbGVtZW50LlxuICAgKiBAcGFyYW0ge0VsZW1lbnR9IGVsZW1lbnQgLSBUaGUgdGFyZ2V0IGVsZW1lbnQuXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBuYW1lIC0gVGhlIGRhdGEga2V5IHRvIHNldC5cbiAgICogQHBhcmFtIHtzdHJpbmd9IGRhdGEgLSBUaGUgZGF0YSB2YWx1ZS5cbiAgICovXG4gIGZ1bmN0aW9uIHNldERhdGEoZWxlbWVudCwgbmFtZSwgZGF0YSkge1xuICAgIGlmIChpc09iamVjdChkYXRhKSkge1xuICAgICAgZWxlbWVudFtuYW1lXSA9IGRhdGE7XG4gICAgfSBlbHNlIGlmIChlbGVtZW50LmRhdGFzZXQpIHtcbiAgICAgIGVsZW1lbnQuZGF0YXNldFtuYW1lXSA9IGRhdGE7XG4gICAgfSBlbHNlIHtcbiAgICAgIGVsZW1lbnQuc2V0QXR0cmlidXRlKFwiZGF0YS1cIi5jb25jYXQodG9QYXJhbUNhc2UobmFtZSkpLCBkYXRhKTtcbiAgICB9XG4gIH1cblxuICAvKipcbiAgICogUmVtb3ZlIGRhdGEgZnJvbSB0aGUgZ2l2ZW4gZWxlbWVudC5cbiAgICogQHBhcmFtIHtFbGVtZW50fSBlbGVtZW50IC0gVGhlIHRhcmdldCBlbGVtZW50LlxuICAgKiBAcGFyYW0ge3N0cmluZ30gbmFtZSAtIFRoZSBkYXRhIGtleSB0byByZW1vdmUuXG4gICAqL1xuICBmdW5jdGlvbiByZW1vdmVEYXRhKGVsZW1lbnQsIG5hbWUpIHtcbiAgICBpZiAoaXNPYmplY3QoZWxlbWVudFtuYW1lXSkpIHtcbiAgICAgIHRyeSB7XG4gICAgICAgIGRlbGV0ZSBlbGVtZW50W25hbWVdO1xuICAgICAgfSBjYXRjaCAoZXJyb3IpIHtcbiAgICAgICAgZWxlbWVudFtuYW1lXSA9IHVuZGVmaW5lZDtcbiAgICAgIH1cbiAgICB9IGVsc2UgaWYgKGVsZW1lbnQuZGF0YXNldCkge1xuICAgICAgLy8gIzEyOCBTYWZhcmkgbm90IGFsbG93cyB0byBkZWxldGUgZGF0YXNldCBwcm9wZXJ0eVxuICAgICAgdHJ5IHtcbiAgICAgICAgZGVsZXRlIGVsZW1lbnQuZGF0YXNldFtuYW1lXTtcbiAgICAgIH0gY2F0Y2ggKGVycm9yKSB7XG4gICAgICAgIGVsZW1lbnQuZGF0YXNldFtuYW1lXSA9IHVuZGVmaW5lZDtcbiAgICAgIH1cbiAgICB9IGVsc2Uge1xuICAgICAgZWxlbWVudC5yZW1vdmVBdHRyaWJ1dGUoXCJkYXRhLVwiLmNvbmNhdCh0b1BhcmFtQ2FzZShuYW1lKSkpO1xuICAgIH1cbiAgfVxuICB2YXIgUkVHRVhQX1NQQUNFUyA9IC9cXHNcXHMqLztcbiAgdmFyIG9uY2VTdXBwb3J0ZWQgPSBmdW5jdGlvbiAoKSB7XG4gICAgdmFyIHN1cHBvcnRlZCA9IGZhbHNlO1xuICAgIGlmIChJU19CUk9XU0VSKSB7XG4gICAgICB2YXIgb25jZSA9IGZhbHNlO1xuICAgICAgdmFyIGxpc3RlbmVyID0gZnVuY3Rpb24gbGlzdGVuZXIoKSB7fTtcbiAgICAgIHZhciBvcHRpb25zID0gT2JqZWN0LmRlZmluZVByb3BlcnR5KHt9LCAnb25jZScsIHtcbiAgICAgICAgZ2V0OiBmdW5jdGlvbiBnZXQoKSB7XG4gICAgICAgICAgc3VwcG9ydGVkID0gdHJ1ZTtcbiAgICAgICAgICByZXR1cm4gb25jZTtcbiAgICAgICAgfSxcbiAgICAgICAgLyoqXG4gICAgICAgICAqIFRoaXMgc2V0dGVyIGNhbiBmaXggYSBgVHlwZUVycm9yYCBpbiBzdHJpY3QgbW9kZVxuICAgICAgICAgKiB7QGxpbmsgaHR0cHM6Ly9kZXZlbG9wZXIubW96aWxsYS5vcmcvZW4tVVMvZG9jcy9XZWIvSmF2YVNjcmlwdC9SZWZlcmVuY2UvRXJyb3JzL0dldHRlcl9vbmx5fVxuICAgICAgICAgKiBAcGFyYW0ge2Jvb2xlYW59IHZhbHVlIC0gVGhlIHZhbHVlIHRvIHNldFxuICAgICAgICAgKi9cbiAgICAgICAgc2V0OiBmdW5jdGlvbiBzZXQodmFsdWUpIHtcbiAgICAgICAgICBvbmNlID0gdmFsdWU7XG4gICAgICAgIH1cbiAgICAgIH0pO1xuICAgICAgV0lORE9XLmFkZEV2ZW50TGlzdGVuZXIoJ3Rlc3QnLCBsaXN0ZW5lciwgb3B0aW9ucyk7XG4gICAgICBXSU5ET1cucmVtb3ZlRXZlbnRMaXN0ZW5lcigndGVzdCcsIGxpc3RlbmVyLCBvcHRpb25zKTtcbiAgICB9XG4gICAgcmV0dXJuIHN1cHBvcnRlZDtcbiAgfSgpO1xuXG4gIC8qKlxuICAgKiBSZW1vdmUgZXZlbnQgbGlzdGVuZXIgZnJvbSB0aGUgdGFyZ2V0IGVsZW1lbnQuXG4gICAqIEBwYXJhbSB7RWxlbWVudH0gZWxlbWVudCAtIFRoZSBldmVudCB0YXJnZXQuXG4gICAqIEBwYXJhbSB7c3RyaW5nfSB0eXBlIC0gVGhlIGV2ZW50IHR5cGUocykuXG4gICAqIEBwYXJhbSB7RnVuY3Rpb259IGxpc3RlbmVyIC0gVGhlIGV2ZW50IGxpc3RlbmVyLlxuICAgKiBAcGFyYW0ge09iamVjdH0gb3B0aW9ucyAtIFRoZSBldmVudCBvcHRpb25zLlxuICAgKi9cbiAgZnVuY3Rpb24gcmVtb3ZlTGlzdGVuZXIoZWxlbWVudCwgdHlwZSwgbGlzdGVuZXIpIHtcbiAgICB2YXIgb3B0aW9ucyA9IGFyZ3VtZW50cy5sZW5ndGggPiAzICYmIGFyZ3VtZW50c1szXSAhPT0gdW5kZWZpbmVkID8gYXJndW1lbnRzWzNdIDoge307XG4gICAgdmFyIGhhbmRsZXIgPSBsaXN0ZW5lcjtcbiAgICB0eXBlLnRyaW0oKS5zcGxpdChSRUdFWFBfU1BBQ0VTKS5mb3JFYWNoKGZ1bmN0aW9uIChldmVudCkge1xuICAgICAgaWYgKCFvbmNlU3VwcG9ydGVkKSB7XG4gICAgICAgIHZhciBsaXN0ZW5lcnMgPSBlbGVtZW50Lmxpc3RlbmVycztcbiAgICAgICAgaWYgKGxpc3RlbmVycyAmJiBsaXN0ZW5lcnNbZXZlbnRdICYmIGxpc3RlbmVyc1tldmVudF1bbGlzdGVuZXJdKSB7XG4gICAgICAgICAgaGFuZGxlciA9IGxpc3RlbmVyc1tldmVudF1bbGlzdGVuZXJdO1xuICAgICAgICAgIGRlbGV0ZSBsaXN0ZW5lcnNbZXZlbnRdW2xpc3RlbmVyXTtcbiAgICAgICAgICBpZiAoT2JqZWN0LmtleXMobGlzdGVuZXJzW2V2ZW50XSkubGVuZ3RoID09PSAwKSB7XG4gICAgICAgICAgICBkZWxldGUgbGlzdGVuZXJzW2V2ZW50XTtcbiAgICAgICAgICB9XG4gICAgICAgICAgaWYgKE9iamVjdC5rZXlzKGxpc3RlbmVycykubGVuZ3RoID09PSAwKSB7XG4gICAgICAgICAgICBkZWxldGUgZWxlbWVudC5saXN0ZW5lcnM7XG4gICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICB9XG4gICAgICBlbGVtZW50LnJlbW92ZUV2ZW50TGlzdGVuZXIoZXZlbnQsIGhhbmRsZXIsIG9wdGlvbnMpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEFkZCBldmVudCBsaXN0ZW5lciB0byB0aGUgdGFyZ2V0IGVsZW1lbnQuXG4gICAqIEBwYXJhbSB7RWxlbWVudH0gZWxlbWVudCAtIFRoZSBldmVudCB0YXJnZXQuXG4gICAqIEBwYXJhbSB7c3RyaW5nfSB0eXBlIC0gVGhlIGV2ZW50IHR5cGUocykuXG4gICAqIEBwYXJhbSB7RnVuY3Rpb259IGxpc3RlbmVyIC0gVGhlIGV2ZW50IGxpc3RlbmVyLlxuICAgKiBAcGFyYW0ge09iamVjdH0gb3B0aW9ucyAtIFRoZSBldmVudCBvcHRpb25zLlxuICAgKi9cbiAgZnVuY3Rpb24gYWRkTGlzdGVuZXIoZWxlbWVudCwgdHlwZSwgbGlzdGVuZXIpIHtcbiAgICB2YXIgb3B0aW9ucyA9IGFyZ3VtZW50cy5sZW5ndGggPiAzICYmIGFyZ3VtZW50c1szXSAhPT0gdW5kZWZpbmVkID8gYXJndW1lbnRzWzNdIDoge307XG4gICAgdmFyIF9oYW5kbGVyID0gbGlzdGVuZXI7XG4gICAgdHlwZS50cmltKCkuc3BsaXQoUkVHRVhQX1NQQUNFUykuZm9yRWFjaChmdW5jdGlvbiAoZXZlbnQpIHtcbiAgICAgIGlmIChvcHRpb25zLm9uY2UgJiYgIW9uY2VTdXBwb3J0ZWQpIHtcbiAgICAgICAgdmFyIF9lbGVtZW50JGxpc3RlbmVycyA9IGVsZW1lbnQubGlzdGVuZXJzLFxuICAgICAgICAgIGxpc3RlbmVycyA9IF9lbGVtZW50JGxpc3RlbmVycyA9PT0gdm9pZCAwID8ge30gOiBfZWxlbWVudCRsaXN0ZW5lcnM7XG4gICAgICAgIF9oYW5kbGVyID0gZnVuY3Rpb24gaGFuZGxlcigpIHtcbiAgICAgICAgICBkZWxldGUgbGlzdGVuZXJzW2V2ZW50XVtsaXN0ZW5lcl07XG4gICAgICAgICAgZWxlbWVudC5yZW1vdmVFdmVudExpc3RlbmVyKGV2ZW50LCBfaGFuZGxlciwgb3B0aW9ucyk7XG4gICAgICAgICAgZm9yICh2YXIgX2xlbjIgPSBhcmd1bWVudHMubGVuZ3RoLCBhcmdzID0gbmV3IEFycmF5KF9sZW4yKSwgX2tleTIgPSAwOyBfa2V5MiA8IF9sZW4yOyBfa2V5MisrKSB7XG4gICAgICAgICAgICBhcmdzW19rZXkyXSA9IGFyZ3VtZW50c1tfa2V5Ml07XG4gICAgICAgICAgfVxuICAgICAgICAgIGxpc3RlbmVyLmFwcGx5KGVsZW1lbnQsIGFyZ3MpO1xuICAgICAgICB9O1xuICAgICAgICBpZiAoIWxpc3RlbmVyc1tldmVudF0pIHtcbiAgICAgICAgICBsaXN0ZW5lcnNbZXZlbnRdID0ge307XG4gICAgICAgIH1cbiAgICAgICAgaWYgKGxpc3RlbmVyc1tldmVudF1bbGlzdGVuZXJdKSB7XG4gICAgICAgICAgZWxlbWVudC5yZW1vdmVFdmVudExpc3RlbmVyKGV2ZW50LCBsaXN0ZW5lcnNbZXZlbnRdW2xpc3RlbmVyXSwgb3B0aW9ucyk7XG4gICAgICAgIH1cbiAgICAgICAgbGlzdGVuZXJzW2V2ZW50XVtsaXN0ZW5lcl0gPSBfaGFuZGxlcjtcbiAgICAgICAgZWxlbWVudC5saXN0ZW5lcnMgPSBsaXN0ZW5lcnM7XG4gICAgICB9XG4gICAgICBlbGVtZW50LmFkZEV2ZW50TGlzdGVuZXIoZXZlbnQsIF9oYW5kbGVyLCBvcHRpb25zKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBEaXNwYXRjaCBldmVudCBvbiB0aGUgdGFyZ2V0IGVsZW1lbnQuXG4gICAqIEBwYXJhbSB7RWxlbWVudH0gZWxlbWVudCAtIFRoZSBldmVudCB0YXJnZXQuXG4gICAqIEBwYXJhbSB7c3RyaW5nfSB0eXBlIC0gVGhlIGV2ZW50IHR5cGUocykuXG4gICAqIEBwYXJhbSB7T2JqZWN0fSBkYXRhIC0gVGhlIGFkZGl0aW9uYWwgZXZlbnQgZGF0YS5cbiAgICogQHJldHVybnMge2Jvb2xlYW59IEluZGljYXRlIGlmIHRoZSBldmVudCBpcyBkZWZhdWx0IHByZXZlbnRlZCBvciBub3QuXG4gICAqL1xuICBmdW5jdGlvbiBkaXNwYXRjaEV2ZW50KGVsZW1lbnQsIHR5cGUsIGRhdGEpIHtcbiAgICB2YXIgZXZlbnQ7XG5cbiAgICAvLyBFdmVudCBhbmQgQ3VzdG9tRXZlbnQgb24gSUU5LTExIGFyZSBnbG9iYWwgb2JqZWN0cywgbm90IGNvbnN0cnVjdG9yc1xuICAgIGlmIChpc0Z1bmN0aW9uKEV2ZW50KSAmJiBpc0Z1bmN0aW9uKEN1c3RvbUV2ZW50KSkge1xuICAgICAgZXZlbnQgPSBuZXcgQ3VzdG9tRXZlbnQodHlwZSwge1xuICAgICAgICBkZXRhaWw6IGRhdGEsXG4gICAgICAgIGJ1YmJsZXM6IHRydWUsXG4gICAgICAgIGNhbmNlbGFibGU6IHRydWVcbiAgICAgIH0pO1xuICAgIH0gZWxzZSB7XG4gICAgICBldmVudCA9IGRvY3VtZW50LmNyZWF0ZUV2ZW50KCdDdXN0b21FdmVudCcpO1xuICAgICAgZXZlbnQuaW5pdEN1c3RvbUV2ZW50KHR5cGUsIHRydWUsIHRydWUsIGRhdGEpO1xuICAgIH1cbiAgICByZXR1cm4gZWxlbWVudC5kaXNwYXRjaEV2ZW50KGV2ZW50KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIG9mZnNldCBiYXNlIG9uIHRoZSBkb2N1bWVudC5cbiAgICogQHBhcmFtIHtFbGVtZW50fSBlbGVtZW50IC0gVGhlIHRhcmdldCBlbGVtZW50LlxuICAgKiBAcmV0dXJucyB7T2JqZWN0fSBUaGUgb2Zmc2V0IGRhdGEuXG4gICAqL1xuICBmdW5jdGlvbiBnZXRPZmZzZXQoZWxlbWVudCkge1xuICAgIHZhciBib3ggPSBlbGVtZW50LmdldEJvdW5kaW5nQ2xpZW50UmVjdCgpO1xuICAgIHJldHVybiB7XG4gICAgICBsZWZ0OiBib3gubGVmdCArICh3aW5kb3cucGFnZVhPZmZzZXQgLSBkb2N1bWVudC5kb2N1bWVudEVsZW1lbnQuY2xpZW50TGVmdCksXG4gICAgICB0b3A6IGJveC50b3AgKyAod2luZG93LnBhZ2VZT2Zmc2V0IC0gZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50LmNsaWVudFRvcClcbiAgICB9O1xuICB9XG4gIHZhciBsb2NhdGlvbiA9IFdJTkRPVy5sb2NhdGlvbjtcbiAgdmFyIFJFR0VYUF9PUklHSU5TID0gL14oXFx3KzopXFwvXFwvKFteOi8/I10qKTo/KFxcZCopL2k7XG5cbiAgLyoqXG4gICAqIENoZWNrIGlmIHRoZSBnaXZlbiBVUkwgaXMgYSBjcm9zcyBvcmlnaW4gVVJMLlxuICAgKiBAcGFyYW0ge3N0cmluZ30gdXJsIC0gVGhlIHRhcmdldCBVUkwuXG4gICAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiB0aGUgZ2l2ZW4gVVJMIGlzIGEgY3Jvc3Mgb3JpZ2luIFVSTCwgZWxzZSBgZmFsc2VgLlxuICAgKi9cbiAgZnVuY3Rpb24gaXNDcm9zc09yaWdpblVSTCh1cmwpIHtcbiAgICB2YXIgcGFydHMgPSB1cmwubWF0Y2goUkVHRVhQX09SSUdJTlMpO1xuICAgIHJldHVybiBwYXJ0cyAhPT0gbnVsbCAmJiAocGFydHNbMV0gIT09IGxvY2F0aW9uLnByb3RvY29sIHx8IHBhcnRzWzJdICE9PSBsb2NhdGlvbi5ob3N0bmFtZSB8fCBwYXJ0c1szXSAhPT0gbG9jYXRpb24ucG9ydCk7XG4gIH1cblxuICAvKipcbiAgICogQWRkIHRpbWVzdGFtcCB0byB0aGUgZ2l2ZW4gVVJMLlxuICAgKiBAcGFyYW0ge3N0cmluZ30gdXJsIC0gVGhlIHRhcmdldCBVUkwuXG4gICAqIEByZXR1cm5zIHtzdHJpbmd9IFRoZSByZXN1bHQgVVJMLlxuICAgKi9cbiAgZnVuY3Rpb24gYWRkVGltZXN0YW1wKHVybCkge1xuICAgIHZhciB0aW1lc3RhbXAgPSBcInRpbWVzdGFtcD1cIi5jb25jYXQobmV3IERhdGUoKS5nZXRUaW1lKCkpO1xuICAgIHJldHVybiB1cmwgKyAodXJsLmluZGV4T2YoJz8nKSA9PT0gLTEgPyAnPycgOiAnJicpICsgdGltZXN0YW1wO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0cmFuc2Zvcm1zIGJhc2Ugb24gdGhlIGdpdmVuIG9iamVjdC5cbiAgICogQHBhcmFtIHtPYmplY3R9IG9iaiAtIFRoZSB0YXJnZXQgb2JqZWN0LlxuICAgKiBAcmV0dXJucyB7c3RyaW5nfSBBIHN0cmluZyBjb250YWlucyB0cmFuc2Zvcm0gdmFsdWVzLlxuICAgKi9cbiAgZnVuY3Rpb24gZ2V0VHJhbnNmb3JtcyhfcmVmKSB7XG4gICAgdmFyIHJvdGF0ZSA9IF9yZWYucm90YXRlLFxuICAgICAgc2NhbGVYID0gX3JlZi5zY2FsZVgsXG4gICAgICBzY2FsZVkgPSBfcmVmLnNjYWxlWSxcbiAgICAgIHRyYW5zbGF0ZVggPSBfcmVmLnRyYW5zbGF0ZVgsXG4gICAgICB0cmFuc2xhdGVZID0gX3JlZi50cmFuc2xhdGVZO1xuICAgIHZhciB2YWx1ZXMgPSBbXTtcbiAgICBpZiAoaXNOdW1iZXIodHJhbnNsYXRlWCkgJiYgdHJhbnNsYXRlWCAhPT0gMCkge1xuICAgICAgdmFsdWVzLnB1c2goXCJ0cmFuc2xhdGVYKFwiLmNvbmNhdCh0cmFuc2xhdGVYLCBcInB4KVwiKSk7XG4gICAgfVxuICAgIGlmIChpc051bWJlcih0cmFuc2xhdGVZKSAmJiB0cmFuc2xhdGVZICE9PSAwKSB7XG4gICAgICB2YWx1ZXMucHVzaChcInRyYW5zbGF0ZVkoXCIuY29uY2F0KHRyYW5zbGF0ZVksIFwicHgpXCIpKTtcbiAgICB9XG5cbiAgICAvLyBSb3RhdGUgc2hvdWxkIGNvbWUgZmlyc3QgYmVmb3JlIHNjYWxlIHRvIG1hdGNoIG9yaWVudGF0aW9uIHRyYW5zZm9ybVxuICAgIGlmIChpc051bWJlcihyb3RhdGUpICYmIHJvdGF0ZSAhPT0gMCkge1xuICAgICAgdmFsdWVzLnB1c2goXCJyb3RhdGUoXCIuY29uY2F0KHJvdGF0ZSwgXCJkZWcpXCIpKTtcbiAgICB9XG4gICAgaWYgKGlzTnVtYmVyKHNjYWxlWCkgJiYgc2NhbGVYICE9PSAxKSB7XG4gICAgICB2YWx1ZXMucHVzaChcInNjYWxlWChcIi5jb25jYXQoc2NhbGVYLCBcIilcIikpO1xuICAgIH1cbiAgICBpZiAoaXNOdW1iZXIoc2NhbGVZKSAmJiBzY2FsZVkgIT09IDEpIHtcbiAgICAgIHZhbHVlcy5wdXNoKFwic2NhbGVZKFwiLmNvbmNhdChzY2FsZVksIFwiKVwiKSk7XG4gICAgfVxuICAgIHZhciB0cmFuc2Zvcm0gPSB2YWx1ZXMubGVuZ3RoID8gdmFsdWVzLmpvaW4oJyAnKSA6ICdub25lJztcbiAgICByZXR1cm4ge1xuICAgICAgV2Via2l0VHJhbnNmb3JtOiB0cmFuc2Zvcm0sXG4gICAgICBtc1RyYW5zZm9ybTogdHJhbnNmb3JtLFxuICAgICAgdHJhbnNmb3JtOiB0cmFuc2Zvcm1cbiAgICB9O1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgbWF4IHJhdGlvIG9mIGEgZ3JvdXAgb2YgcG9pbnRlcnMuXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBwb2ludGVycyAtIFRoZSB0YXJnZXQgcG9pbnRlcnMuXG4gICAqIEByZXR1cm5zIHtudW1iZXJ9IFRoZSByZXN1bHQgcmF0aW8uXG4gICAqL1xuICBmdW5jdGlvbiBnZXRNYXhab29tUmF0aW8ocG9pbnRlcnMpIHtcbiAgICB2YXIgcG9pbnRlcnMyID0gX29iamVjdFNwcmVhZDIoe30sIHBvaW50ZXJzKTtcbiAgICB2YXIgbWF4UmF0aW8gPSAwO1xuICAgIGZvckVhY2gocG9pbnRlcnMsIGZ1bmN0aW9uIChwb2ludGVyLCBwb2ludGVySWQpIHtcbiAgICAgIGRlbGV0ZSBwb2ludGVyczJbcG9pbnRlcklkXTtcbiAgICAgIGZvckVhY2gocG9pbnRlcnMyLCBmdW5jdGlvbiAocG9pbnRlcjIpIHtcbiAgICAgICAgdmFyIHgxID0gTWF0aC5hYnMocG9pbnRlci5zdGFydFggLSBwb2ludGVyMi5zdGFydFgpO1xuICAgICAgICB2YXIgeTEgPSBNYXRoLmFicyhwb2ludGVyLnN0YXJ0WSAtIHBvaW50ZXIyLnN0YXJ0WSk7XG4gICAgICAgIHZhciB4MiA9IE1hdGguYWJzKHBvaW50ZXIuZW5kWCAtIHBvaW50ZXIyLmVuZFgpO1xuICAgICAgICB2YXIgeTIgPSBNYXRoLmFicyhwb2ludGVyLmVuZFkgLSBwb2ludGVyMi5lbmRZKTtcbiAgICAgICAgdmFyIHoxID0gTWF0aC5zcXJ0KHgxICogeDEgKyB5MSAqIHkxKTtcbiAgICAgICAgdmFyIHoyID0gTWF0aC5zcXJ0KHgyICogeDIgKyB5MiAqIHkyKTtcbiAgICAgICAgdmFyIHJhdGlvID0gKHoyIC0gejEpIC8gejE7XG4gICAgICAgIGlmIChNYXRoLmFicyhyYXRpbykgPiBNYXRoLmFicyhtYXhSYXRpbykpIHtcbiAgICAgICAgICBtYXhSYXRpbyA9IHJhdGlvO1xuICAgICAgICB9XG4gICAgICB9KTtcbiAgICB9KTtcbiAgICByZXR1cm4gbWF4UmF0aW87XG4gIH1cblxuICAvKipcbiAgICogR2V0IGEgcG9pbnRlciBmcm9tIGFuIGV2ZW50IG9iamVjdC5cbiAgICogQHBhcmFtIHtPYmplY3R9IGV2ZW50IC0gVGhlIHRhcmdldCBldmVudCBvYmplY3QuXG4gICAqIEBwYXJhbSB7Ym9vbGVhbn0gZW5kT25seSAtIEluZGljYXRlcyBpZiBvbmx5IHJldHVybnMgdGhlIGVuZCBwb2ludCBjb29yZGluYXRlIG9yIG5vdC5cbiAgICogQHJldHVybnMge09iamVjdH0gVGhlIHJlc3VsdCBwb2ludGVyIGNvbnRhaW5zIHN0YXJ0IGFuZC9vciBlbmQgcG9pbnQgY29vcmRpbmF0ZXMuXG4gICAqL1xuICBmdW5jdGlvbiBnZXRQb2ludGVyKF9yZWYyLCBlbmRPbmx5KSB7XG4gICAgdmFyIHBhZ2VYID0gX3JlZjIucGFnZVgsXG4gICAgICBwYWdlWSA9IF9yZWYyLnBhZ2VZO1xuICAgIHZhciBlbmQgPSB7XG4gICAgICBlbmRYOiBwYWdlWCxcbiAgICAgIGVuZFk6IHBhZ2VZXG4gICAgfTtcbiAgICByZXR1cm4gZW5kT25seSA/IGVuZCA6IF9vYmplY3RTcHJlYWQyKHtcbiAgICAgIHN0YXJ0WDogcGFnZVgsXG4gICAgICBzdGFydFk6IHBhZ2VZXG4gICAgfSwgZW5kKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIGNlbnRlciBwb2ludCBjb29yZGluYXRlIG9mIGEgZ3JvdXAgb2YgcG9pbnRlcnMuXG4gICAqIEBwYXJhbSB7T2JqZWN0fSBwb2ludGVycyAtIFRoZSB0YXJnZXQgcG9pbnRlcnMuXG4gICAqIEByZXR1cm5zIHtPYmplY3R9IFRoZSBjZW50ZXIgcG9pbnQgY29vcmRpbmF0ZS5cbiAgICovXG4gIGZ1bmN0aW9uIGdldFBvaW50ZXJzQ2VudGVyKHBvaW50ZXJzKSB7XG4gICAgdmFyIHBhZ2VYID0gMDtcbiAgICB2YXIgcGFnZVkgPSAwO1xuICAgIHZhciBjb3VudCA9IDA7XG4gICAgZm9yRWFjaChwb2ludGVycywgZnVuY3Rpb24gKF9yZWYzKSB7XG4gICAgICB2YXIgc3RhcnRYID0gX3JlZjMuc3RhcnRYLFxuICAgICAgICBzdGFydFkgPSBfcmVmMy5zdGFydFk7XG4gICAgICBwYWdlWCArPSBzdGFydFg7XG4gICAgICBwYWdlWSArPSBzdGFydFk7XG4gICAgICBjb3VudCArPSAxO1xuICAgIH0pO1xuICAgIHBhZ2VYIC89IGNvdW50O1xuICAgIHBhZ2VZIC89IGNvdW50O1xuICAgIHJldHVybiB7XG4gICAgICBwYWdlWDogcGFnZVgsXG4gICAgICBwYWdlWTogcGFnZVlcbiAgICB9O1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgbWF4IHNpemVzIGluIGEgcmVjdGFuZ2xlIHVuZGVyIHRoZSBnaXZlbiBhc3BlY3QgcmF0aW8uXG4gICAqIEBwYXJhbSB7T2JqZWN0fSBkYXRhIC0gVGhlIG9yaWdpbmFsIHNpemVzLlxuICAgKiBAcGFyYW0ge3N0cmluZ30gW3R5cGU9J2NvbnRhaW4nXSAtIFRoZSBhZGp1c3QgdHlwZS5cbiAgICogQHJldHVybnMge09iamVjdH0gVGhlIHJlc3VsdCBzaXplcy5cbiAgICovXG4gIGZ1bmN0aW9uIGdldEFkanVzdGVkU2l6ZXMoX3JlZjQpIHtcbiAgICB2YXIgYXNwZWN0UmF0aW8gPSBfcmVmNC5hc3BlY3RSYXRpbyxcbiAgICAgIGhlaWdodCA9IF9yZWY0LmhlaWdodCxcbiAgICAgIHdpZHRoID0gX3JlZjQud2lkdGg7XG4gICAgdmFyIHR5cGUgPSBhcmd1bWVudHMubGVuZ3RoID4gMSAmJiBhcmd1bWVudHNbMV0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1sxXSA6ICdjb250YWluJztcbiAgICB2YXIgaXNWYWxpZFdpZHRoID0gaXNQb3NpdGl2ZU51bWJlcih3aWR0aCk7XG4gICAgdmFyIGlzVmFsaWRIZWlnaHQgPSBpc1Bvc2l0aXZlTnVtYmVyKGhlaWdodCk7XG4gICAgaWYgKGlzVmFsaWRXaWR0aCAmJiBpc1ZhbGlkSGVpZ2h0KSB7XG4gICAgICB2YXIgYWRqdXN0ZWRXaWR0aCA9IGhlaWdodCAqIGFzcGVjdFJhdGlvO1xuICAgICAgaWYgKHR5cGUgPT09ICdjb250YWluJyAmJiBhZGp1c3RlZFdpZHRoID4gd2lkdGggfHwgdHlwZSA9PT0gJ2NvdmVyJyAmJiBhZGp1c3RlZFdpZHRoIDwgd2lkdGgpIHtcbiAgICAgICAgaGVpZ2h0ID0gd2lkdGggLyBhc3BlY3RSYXRpbztcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIHdpZHRoID0gaGVpZ2h0ICogYXNwZWN0UmF0aW87XG4gICAgICB9XG4gICAgfSBlbHNlIGlmIChpc1ZhbGlkV2lkdGgpIHtcbiAgICAgIGhlaWdodCA9IHdpZHRoIC8gYXNwZWN0UmF0aW87XG4gICAgfSBlbHNlIGlmIChpc1ZhbGlkSGVpZ2h0KSB7XG4gICAgICB3aWR0aCA9IGhlaWdodCAqIGFzcGVjdFJhdGlvO1xuICAgIH1cbiAgICByZXR1cm4ge1xuICAgICAgd2lkdGg6IHdpZHRoLFxuICAgICAgaGVpZ2h0OiBoZWlnaHRcbiAgICB9O1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgbmV3IHNpemVzIG9mIGEgcmVjdGFuZ2xlIGFmdGVyIHJvdGF0ZWQuXG4gICAqIEBwYXJhbSB7T2JqZWN0fSBkYXRhIC0gVGhlIG9yaWdpbmFsIHNpemVzLlxuICAgKiBAcmV0dXJucyB7T2JqZWN0fSBUaGUgcmVzdWx0IHNpemVzLlxuICAgKi9cbiAgZnVuY3Rpb24gZ2V0Um90YXRlZFNpemVzKF9yZWY1KSB7XG4gICAgdmFyIHdpZHRoID0gX3JlZjUud2lkdGgsXG4gICAgICBoZWlnaHQgPSBfcmVmNS5oZWlnaHQsXG4gICAgICBkZWdyZWUgPSBfcmVmNS5kZWdyZWU7XG4gICAgZGVncmVlID0gTWF0aC5hYnMoZGVncmVlKSAlIDE4MDtcbiAgICBpZiAoZGVncmVlID09PSA5MCkge1xuICAgICAgcmV0dXJuIHtcbiAgICAgICAgd2lkdGg6IGhlaWdodCxcbiAgICAgICAgaGVpZ2h0OiB3aWR0aFxuICAgICAgfTtcbiAgICB9XG4gICAgdmFyIGFyYyA9IGRlZ3JlZSAlIDkwICogTWF0aC5QSSAvIDE4MDtcbiAgICB2YXIgc2luQXJjID0gTWF0aC5zaW4oYXJjKTtcbiAgICB2YXIgY29zQXJjID0gTWF0aC5jb3MoYXJjKTtcbiAgICB2YXIgbmV3V2lkdGggPSB3aWR0aCAqIGNvc0FyYyArIGhlaWdodCAqIHNpbkFyYztcbiAgICB2YXIgbmV3SGVpZ2h0ID0gd2lkdGggKiBzaW5BcmMgKyBoZWlnaHQgKiBjb3NBcmM7XG4gICAgcmV0dXJuIGRlZ3JlZSA+IDkwID8ge1xuICAgICAgd2lkdGg6IG5ld0hlaWdodCxcbiAgICAgIGhlaWdodDogbmV3V2lkdGhcbiAgICB9IDoge1xuICAgICAgd2lkdGg6IG5ld1dpZHRoLFxuICAgICAgaGVpZ2h0OiBuZXdIZWlnaHRcbiAgICB9O1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCBhIGNhbnZhcyB3aGljaCBkcmV3IHRoZSBnaXZlbiBpbWFnZS5cbiAgICogQHBhcmFtIHtIVE1MSW1hZ2VFbGVtZW50fSBpbWFnZSAtIFRoZSBpbWFnZSBmb3IgZHJhd2luZy5cbiAgICogQHBhcmFtIHtPYmplY3R9IGltYWdlRGF0YSAtIFRoZSBpbWFnZSBkYXRhLlxuICAgKiBAcGFyYW0ge09iamVjdH0gY2FudmFzRGF0YSAtIFRoZSBjYW52YXMgZGF0YS5cbiAgICogQHBhcmFtIHtPYmplY3R9IG9wdGlvbnMgLSBUaGUgb3B0aW9ucy5cbiAgICogQHJldHVybnMge0hUTUxDYW52YXNFbGVtZW50fSBUaGUgcmVzdWx0IGNhbnZhcy5cbiAgICovXG4gIGZ1bmN0aW9uIGdldFNvdXJjZUNhbnZhcyhpbWFnZSwgX3JlZjYsIF9yZWY3LCBfcmVmOCkge1xuICAgIHZhciBpbWFnZUFzcGVjdFJhdGlvID0gX3JlZjYuYXNwZWN0UmF0aW8sXG4gICAgICBpbWFnZU5hdHVyYWxXaWR0aCA9IF9yZWY2Lm5hdHVyYWxXaWR0aCxcbiAgICAgIGltYWdlTmF0dXJhbEhlaWdodCA9IF9yZWY2Lm5hdHVyYWxIZWlnaHQsXG4gICAgICBfcmVmNiRyb3RhdGUgPSBfcmVmNi5yb3RhdGUsXG4gICAgICByb3RhdGUgPSBfcmVmNiRyb3RhdGUgPT09IHZvaWQgMCA/IDAgOiBfcmVmNiRyb3RhdGUsXG4gICAgICBfcmVmNiRzY2FsZVggPSBfcmVmNi5zY2FsZVgsXG4gICAgICBzY2FsZVggPSBfcmVmNiRzY2FsZVggPT09IHZvaWQgMCA/IDEgOiBfcmVmNiRzY2FsZVgsXG4gICAgICBfcmVmNiRzY2FsZVkgPSBfcmVmNi5zY2FsZVksXG4gICAgICBzY2FsZVkgPSBfcmVmNiRzY2FsZVkgPT09IHZvaWQgMCA/IDEgOiBfcmVmNiRzY2FsZVk7XG4gICAgdmFyIGFzcGVjdFJhdGlvID0gX3JlZjcuYXNwZWN0UmF0aW8sXG4gICAgICBuYXR1cmFsV2lkdGggPSBfcmVmNy5uYXR1cmFsV2lkdGgsXG4gICAgICBuYXR1cmFsSGVpZ2h0ID0gX3JlZjcubmF0dXJhbEhlaWdodDtcbiAgICB2YXIgX3JlZjgkZmlsbENvbG9yID0gX3JlZjguZmlsbENvbG9yLFxuICAgICAgZmlsbENvbG9yID0gX3JlZjgkZmlsbENvbG9yID09PSB2b2lkIDAgPyAndHJhbnNwYXJlbnQnIDogX3JlZjgkZmlsbENvbG9yLFxuICAgICAgX3JlZjgkaW1hZ2VTbW9vdGhpbmdFID0gX3JlZjguaW1hZ2VTbW9vdGhpbmdFbmFibGVkLFxuICAgICAgaW1hZ2VTbW9vdGhpbmdFbmFibGVkID0gX3JlZjgkaW1hZ2VTbW9vdGhpbmdFID09PSB2b2lkIDAgPyB0cnVlIDogX3JlZjgkaW1hZ2VTbW9vdGhpbmdFLFxuICAgICAgX3JlZjgkaW1hZ2VTbW9vdGhpbmdRID0gX3JlZjguaW1hZ2VTbW9vdGhpbmdRdWFsaXR5LFxuICAgICAgaW1hZ2VTbW9vdGhpbmdRdWFsaXR5ID0gX3JlZjgkaW1hZ2VTbW9vdGhpbmdRID09PSB2b2lkIDAgPyAnbG93JyA6IF9yZWY4JGltYWdlU21vb3RoaW5nUSxcbiAgICAgIF9yZWY4JG1heFdpZHRoID0gX3JlZjgubWF4V2lkdGgsXG4gICAgICBtYXhXaWR0aCA9IF9yZWY4JG1heFdpZHRoID09PSB2b2lkIDAgPyBJbmZpbml0eSA6IF9yZWY4JG1heFdpZHRoLFxuICAgICAgX3JlZjgkbWF4SGVpZ2h0ID0gX3JlZjgubWF4SGVpZ2h0LFxuICAgICAgbWF4SGVpZ2h0ID0gX3JlZjgkbWF4SGVpZ2h0ID09PSB2b2lkIDAgPyBJbmZpbml0eSA6IF9yZWY4JG1heEhlaWdodCxcbiAgICAgIF9yZWY4JG1pbldpZHRoID0gX3JlZjgubWluV2lkdGgsXG4gICAgICBtaW5XaWR0aCA9IF9yZWY4JG1pbldpZHRoID09PSB2b2lkIDAgPyAwIDogX3JlZjgkbWluV2lkdGgsXG4gICAgICBfcmVmOCRtaW5IZWlnaHQgPSBfcmVmOC5taW5IZWlnaHQsXG4gICAgICBtaW5IZWlnaHQgPSBfcmVmOCRtaW5IZWlnaHQgPT09IHZvaWQgMCA/IDAgOiBfcmVmOCRtaW5IZWlnaHQ7XG4gICAgdmFyIGNhbnZhcyA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2NhbnZhcycpO1xuICAgIHZhciBjb250ZXh0ID0gY2FudmFzLmdldENvbnRleHQoJzJkJyk7XG4gICAgdmFyIG1heFNpemVzID0gZ2V0QWRqdXN0ZWRTaXplcyh7XG4gICAgICBhc3BlY3RSYXRpbzogYXNwZWN0UmF0aW8sXG4gICAgICB3aWR0aDogbWF4V2lkdGgsXG4gICAgICBoZWlnaHQ6IG1heEhlaWdodFxuICAgIH0pO1xuICAgIHZhciBtaW5TaXplcyA9IGdldEFkanVzdGVkU2l6ZXMoe1xuICAgICAgYXNwZWN0UmF0aW86IGFzcGVjdFJhdGlvLFxuICAgICAgd2lkdGg6IG1pbldpZHRoLFxuICAgICAgaGVpZ2h0OiBtaW5IZWlnaHRcbiAgICB9LCAnY292ZXInKTtcbiAgICB2YXIgd2lkdGggPSBNYXRoLm1pbihtYXhTaXplcy53aWR0aCwgTWF0aC5tYXgobWluU2l6ZXMud2lkdGgsIG5hdHVyYWxXaWR0aCkpO1xuICAgIHZhciBoZWlnaHQgPSBNYXRoLm1pbihtYXhTaXplcy5oZWlnaHQsIE1hdGgubWF4KG1pblNpemVzLmhlaWdodCwgbmF0dXJhbEhlaWdodCkpO1xuXG4gICAgLy8gTm90ZTogc2hvdWxkIGFsd2F5cyB1c2UgaW1hZ2UncyBuYXR1cmFsIHNpemVzIGZvciBkcmF3aW5nIGFzXG4gICAgLy8gaW1hZ2VEYXRhLm5hdHVyYWxXaWR0aCA9PT0gY2FudmFzRGF0YS5uYXR1cmFsSGVpZ2h0IHdoZW4gcm90YXRlICUgMTgwID09PSA5MFxuICAgIHZhciBkZXN0TWF4U2l6ZXMgPSBnZXRBZGp1c3RlZFNpemVzKHtcbiAgICAgIGFzcGVjdFJhdGlvOiBpbWFnZUFzcGVjdFJhdGlvLFxuICAgICAgd2lkdGg6IG1heFdpZHRoLFxuICAgICAgaGVpZ2h0OiBtYXhIZWlnaHRcbiAgICB9KTtcbiAgICB2YXIgZGVzdE1pblNpemVzID0gZ2V0QWRqdXN0ZWRTaXplcyh7XG4gICAgICBhc3BlY3RSYXRpbzogaW1hZ2VBc3BlY3RSYXRpbyxcbiAgICAgIHdpZHRoOiBtaW5XaWR0aCxcbiAgICAgIGhlaWdodDogbWluSGVpZ2h0XG4gICAgfSwgJ2NvdmVyJyk7XG4gICAgdmFyIGRlc3RXaWR0aCA9IE1hdGgubWluKGRlc3RNYXhTaXplcy53aWR0aCwgTWF0aC5tYXgoZGVzdE1pblNpemVzLndpZHRoLCBpbWFnZU5hdHVyYWxXaWR0aCkpO1xuICAgIHZhciBkZXN0SGVpZ2h0ID0gTWF0aC5taW4oZGVzdE1heFNpemVzLmhlaWdodCwgTWF0aC5tYXgoZGVzdE1pblNpemVzLmhlaWdodCwgaW1hZ2VOYXR1cmFsSGVpZ2h0KSk7XG4gICAgdmFyIHBhcmFtcyA9IFstZGVzdFdpZHRoIC8gMiwgLWRlc3RIZWlnaHQgLyAyLCBkZXN0V2lkdGgsIGRlc3RIZWlnaHRdO1xuICAgIGNhbnZhcy53aWR0aCA9IG5vcm1hbGl6ZURlY2ltYWxOdW1iZXIod2lkdGgpO1xuICAgIGNhbnZhcy5oZWlnaHQgPSBub3JtYWxpemVEZWNpbWFsTnVtYmVyKGhlaWdodCk7XG4gICAgY29udGV4dC5maWxsU3R5bGUgPSBmaWxsQ29sb3I7XG4gICAgY29udGV4dC5maWxsUmVjdCgwLCAwLCB3aWR0aCwgaGVpZ2h0KTtcbiAgICBjb250ZXh0LnNhdmUoKTtcbiAgICBjb250ZXh0LnRyYW5zbGF0ZSh3aWR0aCAvIDIsIGhlaWdodCAvIDIpO1xuICAgIGNvbnRleHQucm90YXRlKHJvdGF0ZSAqIE1hdGguUEkgLyAxODApO1xuICAgIGNvbnRleHQuc2NhbGUoc2NhbGVYLCBzY2FsZVkpO1xuICAgIGNvbnRleHQuaW1hZ2VTbW9vdGhpbmdFbmFibGVkID0gaW1hZ2VTbW9vdGhpbmdFbmFibGVkO1xuICAgIGNvbnRleHQuaW1hZ2VTbW9vdGhpbmdRdWFsaXR5ID0gaW1hZ2VTbW9vdGhpbmdRdWFsaXR5O1xuICAgIGNvbnRleHQuZHJhd0ltYWdlLmFwcGx5KGNvbnRleHQsIFtpbWFnZV0uY29uY2F0KF90b0NvbnN1bWFibGVBcnJheShwYXJhbXMubWFwKGZ1bmN0aW9uIChwYXJhbSkge1xuICAgICAgcmV0dXJuIE1hdGguZmxvb3Iobm9ybWFsaXplRGVjaW1hbE51bWJlcihwYXJhbSkpO1xuICAgIH0pKSkpO1xuICAgIGNvbnRleHQucmVzdG9yZSgpO1xuICAgIHJldHVybiBjYW52YXM7XG4gIH1cbiAgdmFyIGZyb21DaGFyQ29kZSA9IFN0cmluZy5mcm9tQ2hhckNvZGU7XG5cbiAgLyoqXG4gICAqIEdldCBzdHJpbmcgZnJvbSBjaGFyIGNvZGUgaW4gZGF0YSB2aWV3LlxuICAgKiBAcGFyYW0ge0RhdGFWaWV3fSBkYXRhVmlldyAtIFRoZSBkYXRhIHZpZXcgZm9yIHJlYWQuXG4gICAqIEBwYXJhbSB7bnVtYmVyfSBzdGFydCAtIFRoZSBzdGFydCBpbmRleC5cbiAgICogQHBhcmFtIHtudW1iZXJ9IGxlbmd0aCAtIFRoZSByZWFkIGxlbmd0aC5cbiAgICogQHJldHVybnMge3N0cmluZ30gVGhlIHJlYWQgcmVzdWx0LlxuICAgKi9cbiAgZnVuY3Rpb24gZ2V0U3RyaW5nRnJvbUNoYXJDb2RlKGRhdGFWaWV3LCBzdGFydCwgbGVuZ3RoKSB7XG4gICAgdmFyIHN0ciA9ICcnO1xuICAgIGxlbmd0aCArPSBzdGFydDtcbiAgICBmb3IgKHZhciBpID0gc3RhcnQ7IGkgPCBsZW5ndGg7IGkgKz0gMSkge1xuICAgICAgc3RyICs9IGZyb21DaGFyQ29kZShkYXRhVmlldy5nZXRVaW50OChpKSk7XG4gICAgfVxuICAgIHJldHVybiBzdHI7XG4gIH1cbiAgdmFyIFJFR0VYUF9EQVRBX1VSTF9IRUFEID0gL15kYXRhOi4qLC87XG5cbiAgLyoqXG4gICAqIFRyYW5zZm9ybSBEYXRhIFVSTCB0byBhcnJheSBidWZmZXIuXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBkYXRhVVJMIC0gVGhlIERhdGEgVVJMIHRvIHRyYW5zZm9ybS5cbiAgICogQHJldHVybnMge0FycmF5QnVmZmVyfSBUaGUgcmVzdWx0IGFycmF5IGJ1ZmZlci5cbiAgICovXG4gIGZ1bmN0aW9uIGRhdGFVUkxUb0FycmF5QnVmZmVyKGRhdGFVUkwpIHtcbiAgICB2YXIgYmFzZTY0ID0gZGF0YVVSTC5yZXBsYWNlKFJFR0VYUF9EQVRBX1VSTF9IRUFELCAnJyk7XG4gICAgdmFyIGJpbmFyeSA9IGF0b2IoYmFzZTY0KTtcbiAgICB2YXIgYXJyYXlCdWZmZXIgPSBuZXcgQXJyYXlCdWZmZXIoYmluYXJ5Lmxlbmd0aCk7XG4gICAgdmFyIHVpbnQ4ID0gbmV3IFVpbnQ4QXJyYXkoYXJyYXlCdWZmZXIpO1xuICAgIGZvckVhY2godWludDgsIGZ1bmN0aW9uICh2YWx1ZSwgaSkge1xuICAgICAgdWludDhbaV0gPSBiaW5hcnkuY2hhckNvZGVBdChpKTtcbiAgICB9KTtcbiAgICByZXR1cm4gYXJyYXlCdWZmZXI7XG4gIH1cblxuICAvKipcbiAgICogVHJhbnNmb3JtIGFycmF5IGJ1ZmZlciB0byBEYXRhIFVSTC5cbiAgICogQHBhcmFtIHtBcnJheUJ1ZmZlcn0gYXJyYXlCdWZmZXIgLSBUaGUgYXJyYXkgYnVmZmVyIHRvIHRyYW5zZm9ybS5cbiAgICogQHBhcmFtIHtzdHJpbmd9IG1pbWVUeXBlIC0gVGhlIG1pbWUgdHlwZSBvZiB0aGUgRGF0YSBVUkwuXG4gICAqIEByZXR1cm5zIHtzdHJpbmd9IFRoZSByZXN1bHQgRGF0YSBVUkwuXG4gICAqL1xuICBmdW5jdGlvbiBhcnJheUJ1ZmZlclRvRGF0YVVSTChhcnJheUJ1ZmZlciwgbWltZVR5cGUpIHtcbiAgICB2YXIgY2h1bmtzID0gW107XG5cbiAgICAvLyBDaHVuayBUeXBlZCBBcnJheSBmb3IgYmV0dGVyIHBlcmZvcm1hbmNlICgjNDM1KVxuICAgIHZhciBjaHVua1NpemUgPSA4MTkyO1xuICAgIHZhciB1aW50OCA9IG5ldyBVaW50OEFycmF5KGFycmF5QnVmZmVyKTtcbiAgICB3aGlsZSAodWludDgubGVuZ3RoID4gMCkge1xuICAgICAgLy8gWFhYOiBCYWJlbCdzIGB0b0NvbnN1bWFibGVBcnJheWAgaGVscGVyIHdpbGwgdGhyb3cgZXJyb3IgaW4gSUUgb3IgU2FmYXJpIDlcbiAgICAgIC8vIGVzbGludC1kaXNhYmxlLW5leHQtbGluZSBwcmVmZXItc3ByZWFkXG4gICAgICBjaHVua3MucHVzaChmcm9tQ2hhckNvZGUuYXBwbHkobnVsbCwgdG9BcnJheSh1aW50OC5zdWJhcnJheSgwLCBjaHVua1NpemUpKSkpO1xuICAgICAgdWludDggPSB1aW50OC5zdWJhcnJheShjaHVua1NpemUpO1xuICAgIH1cbiAgICByZXR1cm4gXCJkYXRhOlwiLmNvbmNhdChtaW1lVHlwZSwgXCI7YmFzZTY0LFwiKS5jb25jYXQoYnRvYShjaHVua3Muam9pbignJykpKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgb3JpZW50YXRpb24gdmFsdWUgZnJvbSBnaXZlbiBhcnJheSBidWZmZXIuXG4gICAqIEBwYXJhbSB7QXJyYXlCdWZmZXJ9IGFycmF5QnVmZmVyIC0gVGhlIGFycmF5IGJ1ZmZlciB0byByZWFkLlxuICAgKiBAcmV0dXJucyB7bnVtYmVyfSBUaGUgcmVhZCBvcmllbnRhdGlvbiB2YWx1ZS5cbiAgICovXG4gIGZ1bmN0aW9uIHJlc2V0QW5kR2V0T3JpZW50YXRpb24oYXJyYXlCdWZmZXIpIHtcbiAgICB2YXIgZGF0YVZpZXcgPSBuZXcgRGF0YVZpZXcoYXJyYXlCdWZmZXIpO1xuICAgIHZhciBvcmllbnRhdGlvbjtcblxuICAgIC8vIElnbm9yZXMgcmFuZ2UgZXJyb3Igd2hlbiB0aGUgaW1hZ2UgZG9lcyBub3QgaGF2ZSBjb3JyZWN0IEV4aWYgaW5mb3JtYXRpb25cbiAgICB0cnkge1xuICAgICAgdmFyIGxpdHRsZUVuZGlhbjtcbiAgICAgIHZhciBhcHAxU3RhcnQ7XG4gICAgICB2YXIgaWZkU3RhcnQ7XG5cbiAgICAgIC8vIE9ubHkgaGFuZGxlIEpQRUcgaW1hZ2UgKHN0YXJ0IGJ5IDB4RkZEOClcbiAgICAgIGlmIChkYXRhVmlldy5nZXRVaW50OCgwKSA9PT0gMHhGRiAmJiBkYXRhVmlldy5nZXRVaW50OCgxKSA9PT0gMHhEOCkge1xuICAgICAgICB2YXIgbGVuZ3RoID0gZGF0YVZpZXcuYnl0ZUxlbmd0aDtcbiAgICAgICAgdmFyIG9mZnNldCA9IDI7XG4gICAgICAgIHdoaWxlIChvZmZzZXQgKyAxIDwgbGVuZ3RoKSB7XG4gICAgICAgICAgaWYgKGRhdGFWaWV3LmdldFVpbnQ4KG9mZnNldCkgPT09IDB4RkYgJiYgZGF0YVZpZXcuZ2V0VWludDgob2Zmc2V0ICsgMSkgPT09IDB4RTEpIHtcbiAgICAgICAgICAgIGFwcDFTdGFydCA9IG9mZnNldDtcbiAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgIH1cbiAgICAgICAgICBvZmZzZXQgKz0gMTtcbiAgICAgICAgfVxuICAgICAgfVxuICAgICAgaWYgKGFwcDFTdGFydCkge1xuICAgICAgICB2YXIgZXhpZklEQ29kZSA9IGFwcDFTdGFydCArIDQ7XG4gICAgICAgIHZhciB0aWZmT2Zmc2V0ID0gYXBwMVN0YXJ0ICsgMTA7XG4gICAgICAgIGlmIChnZXRTdHJpbmdGcm9tQ2hhckNvZGUoZGF0YVZpZXcsIGV4aWZJRENvZGUsIDQpID09PSAnRXhpZicpIHtcbiAgICAgICAgICB2YXIgZW5kaWFubmVzcyA9IGRhdGFWaWV3LmdldFVpbnQxNih0aWZmT2Zmc2V0KTtcbiAgICAgICAgICBsaXR0bGVFbmRpYW4gPSBlbmRpYW5uZXNzID09PSAweDQ5NDk7XG4gICAgICAgICAgaWYgKGxpdHRsZUVuZGlhbiB8fCBlbmRpYW5uZXNzID09PSAweDRENEQgLyogYmlnRW5kaWFuICovKSB7XG4gICAgICAgICAgICBpZiAoZGF0YVZpZXcuZ2V0VWludDE2KHRpZmZPZmZzZXQgKyAyLCBsaXR0bGVFbmRpYW4pID09PSAweDAwMkEpIHtcbiAgICAgICAgICAgICAgdmFyIGZpcnN0SUZET2Zmc2V0ID0gZGF0YVZpZXcuZ2V0VWludDMyKHRpZmZPZmZzZXQgKyA0LCBsaXR0bGVFbmRpYW4pO1xuICAgICAgICAgICAgICBpZiAoZmlyc3RJRkRPZmZzZXQgPj0gMHgwMDAwMDAwOCkge1xuICAgICAgICAgICAgICAgIGlmZFN0YXJ0ID0gdGlmZk9mZnNldCArIGZpcnN0SUZET2Zmc2V0O1xuICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICB9XG4gICAgICBpZiAoaWZkU3RhcnQpIHtcbiAgICAgICAgdmFyIF9sZW5ndGggPSBkYXRhVmlldy5nZXRVaW50MTYoaWZkU3RhcnQsIGxpdHRsZUVuZGlhbik7XG4gICAgICAgIHZhciBfb2Zmc2V0O1xuICAgICAgICB2YXIgaTtcbiAgICAgICAgZm9yIChpID0gMDsgaSA8IF9sZW5ndGg7IGkgKz0gMSkge1xuICAgICAgICAgIF9vZmZzZXQgPSBpZmRTdGFydCArIGkgKiAxMiArIDI7XG4gICAgICAgICAgaWYgKGRhdGFWaWV3LmdldFVpbnQxNihfb2Zmc2V0LCBsaXR0bGVFbmRpYW4pID09PSAweDAxMTIgLyogT3JpZW50YXRpb24gKi8pIHtcbiAgICAgICAgICAgIC8vIDggaXMgdGhlIG9mZnNldCBvZiB0aGUgY3VycmVudCB0YWcncyB2YWx1ZVxuICAgICAgICAgICAgX29mZnNldCArPSA4O1xuXG4gICAgICAgICAgICAvLyBHZXQgdGhlIG9yaWdpbmFsIG9yaWVudGF0aW9uIHZhbHVlXG4gICAgICAgICAgICBvcmllbnRhdGlvbiA9IGRhdGFWaWV3LmdldFVpbnQxNihfb2Zmc2V0LCBsaXR0bGVFbmRpYW4pO1xuXG4gICAgICAgICAgICAvLyBPdmVycmlkZSB0aGUgb3JpZW50YXRpb24gd2l0aCBpdHMgZGVmYXVsdCB2YWx1ZVxuICAgICAgICAgICAgZGF0YVZpZXcuc2V0VWludDE2KF9vZmZzZXQsIDEsIGxpdHRsZUVuZGlhbik7XG4gICAgICAgICAgICBicmVhaztcbiAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICB9IGNhdGNoIChlcnJvcikge1xuICAgICAgb3JpZW50YXRpb24gPSAxO1xuICAgIH1cbiAgICByZXR1cm4gb3JpZW50YXRpb247XG4gIH1cblxuICAvKipcbiAgICogUGFyc2UgRXhpZiBPcmllbnRhdGlvbiB2YWx1ZS5cbiAgICogQHBhcmFtIHtudW1iZXJ9IG9yaWVudGF0aW9uIC0gVGhlIG9yaWVudGF0aW9uIHRvIHBhcnNlLlxuICAgKiBAcmV0dXJucyB7T2JqZWN0fSBUaGUgcGFyc2VkIHJlc3VsdC5cbiAgICovXG4gIGZ1bmN0aW9uIHBhcnNlT3JpZW50YXRpb24ob3JpZW50YXRpb24pIHtcbiAgICB2YXIgcm90YXRlID0gMDtcbiAgICB2YXIgc2NhbGVYID0gMTtcbiAgICB2YXIgc2NhbGVZID0gMTtcbiAgICBzd2l0Y2ggKG9yaWVudGF0aW9uKSB7XG4gICAgICAvLyBGbGlwIGhvcml6b250YWxcbiAgICAgIGNhc2UgMjpcbiAgICAgICAgc2NhbGVYID0gLTE7XG4gICAgICAgIGJyZWFrO1xuXG4gICAgICAvLyBSb3RhdGUgbGVmdCAxODBcdTAwQjBcbiAgICAgIGNhc2UgMzpcbiAgICAgICAgcm90YXRlID0gLTE4MDtcbiAgICAgICAgYnJlYWs7XG5cbiAgICAgIC8vIEZsaXAgdmVydGljYWxcbiAgICAgIGNhc2UgNDpcbiAgICAgICAgc2NhbGVZID0gLTE7XG4gICAgICAgIGJyZWFrO1xuXG4gICAgICAvLyBGbGlwIHZlcnRpY2FsIGFuZCByb3RhdGUgcmlnaHQgOTBcdTAwQjBcbiAgICAgIGNhc2UgNTpcbiAgICAgICAgcm90YXRlID0gOTA7XG4gICAgICAgIHNjYWxlWSA9IC0xO1xuICAgICAgICBicmVhaztcblxuICAgICAgLy8gUm90YXRlIHJpZ2h0IDkwXHUwMEIwXG4gICAgICBjYXNlIDY6XG4gICAgICAgIHJvdGF0ZSA9IDkwO1xuICAgICAgICBicmVhaztcblxuICAgICAgLy8gRmxpcCBob3Jpem9udGFsIGFuZCByb3RhdGUgcmlnaHQgOTBcdTAwQjBcbiAgICAgIGNhc2UgNzpcbiAgICAgICAgcm90YXRlID0gOTA7XG4gICAgICAgIHNjYWxlWCA9IC0xO1xuICAgICAgICBicmVhaztcblxuICAgICAgLy8gUm90YXRlIGxlZnQgOTBcdTAwQjBcbiAgICAgIGNhc2UgODpcbiAgICAgICAgcm90YXRlID0gLTkwO1xuICAgICAgICBicmVhaztcbiAgICB9XG4gICAgcmV0dXJuIHtcbiAgICAgIHJvdGF0ZTogcm90YXRlLFxuICAgICAgc2NhbGVYOiBzY2FsZVgsXG4gICAgICBzY2FsZVk6IHNjYWxlWVxuICAgIH07XG4gIH1cblxuICB2YXIgcmVuZGVyID0ge1xuICAgIHJlbmRlcjogZnVuY3Rpb24gcmVuZGVyKCkge1xuICAgICAgdGhpcy5pbml0Q29udGFpbmVyKCk7XG4gICAgICB0aGlzLmluaXRDYW52YXMoKTtcbiAgICAgIHRoaXMuaW5pdENyb3BCb3goKTtcbiAgICAgIHRoaXMucmVuZGVyQ2FudmFzKCk7XG4gICAgICBpZiAodGhpcy5jcm9wcGVkKSB7XG4gICAgICAgIHRoaXMucmVuZGVyQ3JvcEJveCgpO1xuICAgICAgfVxuICAgIH0sXG4gICAgaW5pdENvbnRhaW5lcjogZnVuY3Rpb24gaW5pdENvbnRhaW5lcigpIHtcbiAgICAgIHZhciBlbGVtZW50ID0gdGhpcy5lbGVtZW50LFxuICAgICAgICBvcHRpb25zID0gdGhpcy5vcHRpb25zLFxuICAgICAgICBjb250YWluZXIgPSB0aGlzLmNvbnRhaW5lcixcbiAgICAgICAgY3JvcHBlciA9IHRoaXMuY3JvcHBlcjtcbiAgICAgIHZhciBtaW5XaWR0aCA9IE51bWJlcihvcHRpb25zLm1pbkNvbnRhaW5lcldpZHRoKTtcbiAgICAgIHZhciBtaW5IZWlnaHQgPSBOdW1iZXIob3B0aW9ucy5taW5Db250YWluZXJIZWlnaHQpO1xuICAgICAgYWRkQ2xhc3MoY3JvcHBlciwgQ0xBU1NfSElEREVOKTtcbiAgICAgIHJlbW92ZUNsYXNzKGVsZW1lbnQsIENMQVNTX0hJRERFTik7XG4gICAgICB2YXIgY29udGFpbmVyRGF0YSA9IHtcbiAgICAgICAgd2lkdGg6IE1hdGgubWF4KGNvbnRhaW5lci5vZmZzZXRXaWR0aCwgbWluV2lkdGggPj0gMCA/IG1pbldpZHRoIDogTUlOX0NPTlRBSU5FUl9XSURUSCksXG4gICAgICAgIGhlaWdodDogTWF0aC5tYXgoY29udGFpbmVyLm9mZnNldEhlaWdodCwgbWluSGVpZ2h0ID49IDAgPyBtaW5IZWlnaHQgOiBNSU5fQ09OVEFJTkVSX0hFSUdIVClcbiAgICAgIH07XG4gICAgICB0aGlzLmNvbnRhaW5lckRhdGEgPSBjb250YWluZXJEYXRhO1xuICAgICAgc2V0U3R5bGUoY3JvcHBlciwge1xuICAgICAgICB3aWR0aDogY29udGFpbmVyRGF0YS53aWR0aCxcbiAgICAgICAgaGVpZ2h0OiBjb250YWluZXJEYXRhLmhlaWdodFxuICAgICAgfSk7XG4gICAgICBhZGRDbGFzcyhlbGVtZW50LCBDTEFTU19ISURERU4pO1xuICAgICAgcmVtb3ZlQ2xhc3MoY3JvcHBlciwgQ0xBU1NfSElEREVOKTtcbiAgICB9LFxuICAgIC8vIENhbnZhcyAoaW1hZ2Ugd3JhcHBlcilcbiAgICBpbml0Q2FudmFzOiBmdW5jdGlvbiBpbml0Q2FudmFzKCkge1xuICAgICAgdmFyIGNvbnRhaW5lckRhdGEgPSB0aGlzLmNvbnRhaW5lckRhdGEsXG4gICAgICAgIGltYWdlRGF0YSA9IHRoaXMuaW1hZ2VEYXRhO1xuICAgICAgdmFyIHZpZXdNb2RlID0gdGhpcy5vcHRpb25zLnZpZXdNb2RlO1xuICAgICAgdmFyIHJvdGF0ZWQgPSBNYXRoLmFicyhpbWFnZURhdGEucm90YXRlKSAlIDE4MCA9PT0gOTA7XG4gICAgICB2YXIgbmF0dXJhbFdpZHRoID0gcm90YXRlZCA/IGltYWdlRGF0YS5uYXR1cmFsSGVpZ2h0IDogaW1hZ2VEYXRhLm5hdHVyYWxXaWR0aDtcbiAgICAgIHZhciBuYXR1cmFsSGVpZ2h0ID0gcm90YXRlZCA/IGltYWdlRGF0YS5uYXR1cmFsV2lkdGggOiBpbWFnZURhdGEubmF0dXJhbEhlaWdodDtcbiAgICAgIHZhciBhc3BlY3RSYXRpbyA9IG5hdHVyYWxXaWR0aCAvIG5hdHVyYWxIZWlnaHQ7XG4gICAgICB2YXIgY2FudmFzV2lkdGggPSBjb250YWluZXJEYXRhLndpZHRoO1xuICAgICAgdmFyIGNhbnZhc0hlaWdodCA9IGNvbnRhaW5lckRhdGEuaGVpZ2h0O1xuICAgICAgaWYgKGNvbnRhaW5lckRhdGEuaGVpZ2h0ICogYXNwZWN0UmF0aW8gPiBjb250YWluZXJEYXRhLndpZHRoKSB7XG4gICAgICAgIGlmICh2aWV3TW9kZSA9PT0gMykge1xuICAgICAgICAgIGNhbnZhc1dpZHRoID0gY29udGFpbmVyRGF0YS5oZWlnaHQgKiBhc3BlY3RSYXRpbztcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICBjYW52YXNIZWlnaHQgPSBjb250YWluZXJEYXRhLndpZHRoIC8gYXNwZWN0UmF0aW87XG4gICAgICAgIH1cbiAgICAgIH0gZWxzZSBpZiAodmlld01vZGUgPT09IDMpIHtcbiAgICAgICAgY2FudmFzSGVpZ2h0ID0gY29udGFpbmVyRGF0YS53aWR0aCAvIGFzcGVjdFJhdGlvO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgY2FudmFzV2lkdGggPSBjb250YWluZXJEYXRhLmhlaWdodCAqIGFzcGVjdFJhdGlvO1xuICAgICAgfVxuICAgICAgdmFyIGNhbnZhc0RhdGEgPSB7XG4gICAgICAgIGFzcGVjdFJhdGlvOiBhc3BlY3RSYXRpbyxcbiAgICAgICAgbmF0dXJhbFdpZHRoOiBuYXR1cmFsV2lkdGgsXG4gICAgICAgIG5hdHVyYWxIZWlnaHQ6IG5hdHVyYWxIZWlnaHQsXG4gICAgICAgIHdpZHRoOiBjYW52YXNXaWR0aCxcbiAgICAgICAgaGVpZ2h0OiBjYW52YXNIZWlnaHRcbiAgICAgIH07XG4gICAgICB0aGlzLmNhbnZhc0RhdGEgPSBjYW52YXNEYXRhO1xuICAgICAgdGhpcy5saW1pdGVkID0gdmlld01vZGUgPT09IDEgfHwgdmlld01vZGUgPT09IDI7XG4gICAgICB0aGlzLmxpbWl0Q2FudmFzKHRydWUsIHRydWUpO1xuICAgICAgY2FudmFzRGF0YS53aWR0aCA9IE1hdGgubWluKE1hdGgubWF4KGNhbnZhc0RhdGEud2lkdGgsIGNhbnZhc0RhdGEubWluV2lkdGgpLCBjYW52YXNEYXRhLm1heFdpZHRoKTtcbiAgICAgIGNhbnZhc0RhdGEuaGVpZ2h0ID0gTWF0aC5taW4oTWF0aC5tYXgoY2FudmFzRGF0YS5oZWlnaHQsIGNhbnZhc0RhdGEubWluSGVpZ2h0KSwgY2FudmFzRGF0YS5tYXhIZWlnaHQpO1xuICAgICAgY2FudmFzRGF0YS5sZWZ0ID0gKGNvbnRhaW5lckRhdGEud2lkdGggLSBjYW52YXNEYXRhLndpZHRoKSAvIDI7XG4gICAgICBjYW52YXNEYXRhLnRvcCA9IChjb250YWluZXJEYXRhLmhlaWdodCAtIGNhbnZhc0RhdGEuaGVpZ2h0KSAvIDI7XG4gICAgICBjYW52YXNEYXRhLm9sZExlZnQgPSBjYW52YXNEYXRhLmxlZnQ7XG4gICAgICBjYW52YXNEYXRhLm9sZFRvcCA9IGNhbnZhc0RhdGEudG9wO1xuICAgICAgdGhpcy5pbml0aWFsQ2FudmFzRGF0YSA9IGFzc2lnbih7fSwgY2FudmFzRGF0YSk7XG4gICAgfSxcbiAgICBsaW1pdENhbnZhczogZnVuY3Rpb24gbGltaXRDYW52YXMoc2l6ZUxpbWl0ZWQsIHBvc2l0aW9uTGltaXRlZCkge1xuICAgICAgdmFyIG9wdGlvbnMgPSB0aGlzLm9wdGlvbnMsXG4gICAgICAgIGNvbnRhaW5lckRhdGEgPSB0aGlzLmNvbnRhaW5lckRhdGEsXG4gICAgICAgIGNhbnZhc0RhdGEgPSB0aGlzLmNhbnZhc0RhdGEsXG4gICAgICAgIGNyb3BCb3hEYXRhID0gdGhpcy5jcm9wQm94RGF0YTtcbiAgICAgIHZhciB2aWV3TW9kZSA9IG9wdGlvbnMudmlld01vZGU7XG4gICAgICB2YXIgYXNwZWN0UmF0aW8gPSBjYW52YXNEYXRhLmFzcGVjdFJhdGlvO1xuICAgICAgdmFyIGNyb3BwZWQgPSB0aGlzLmNyb3BwZWQgJiYgY3JvcEJveERhdGE7XG4gICAgICBpZiAoc2l6ZUxpbWl0ZWQpIHtcbiAgICAgICAgdmFyIG1pbkNhbnZhc1dpZHRoID0gTnVtYmVyKG9wdGlvbnMubWluQ2FudmFzV2lkdGgpIHx8IDA7XG4gICAgICAgIHZhciBtaW5DYW52YXNIZWlnaHQgPSBOdW1iZXIob3B0aW9ucy5taW5DYW52YXNIZWlnaHQpIHx8IDA7XG4gICAgICAgIGlmICh2aWV3TW9kZSA+IDEpIHtcbiAgICAgICAgICBtaW5DYW52YXNXaWR0aCA9IE1hdGgubWF4KG1pbkNhbnZhc1dpZHRoLCBjb250YWluZXJEYXRhLndpZHRoKTtcbiAgICAgICAgICBtaW5DYW52YXNIZWlnaHQgPSBNYXRoLm1heChtaW5DYW52YXNIZWlnaHQsIGNvbnRhaW5lckRhdGEuaGVpZ2h0KTtcbiAgICAgICAgICBpZiAodmlld01vZGUgPT09IDMpIHtcbiAgICAgICAgICAgIGlmIChtaW5DYW52YXNIZWlnaHQgKiBhc3BlY3RSYXRpbyA+IG1pbkNhbnZhc1dpZHRoKSB7XG4gICAgICAgICAgICAgIG1pbkNhbnZhc1dpZHRoID0gbWluQ2FudmFzSGVpZ2h0ICogYXNwZWN0UmF0aW87XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICBtaW5DYW52YXNIZWlnaHQgPSBtaW5DYW52YXNXaWR0aCAvIGFzcGVjdFJhdGlvO1xuICAgICAgICAgICAgfVxuICAgICAgICAgIH1cbiAgICAgICAgfSBlbHNlIGlmICh2aWV3TW9kZSA+IDApIHtcbiAgICAgICAgICBpZiAobWluQ2FudmFzV2lkdGgpIHtcbiAgICAgICAgICAgIG1pbkNhbnZhc1dpZHRoID0gTWF0aC5tYXgobWluQ2FudmFzV2lkdGgsIGNyb3BwZWQgPyBjcm9wQm94RGF0YS53aWR0aCA6IDApO1xuICAgICAgICAgIH0gZWxzZSBpZiAobWluQ2FudmFzSGVpZ2h0KSB7XG4gICAgICAgICAgICBtaW5DYW52YXNIZWlnaHQgPSBNYXRoLm1heChtaW5DYW52YXNIZWlnaHQsIGNyb3BwZWQgPyBjcm9wQm94RGF0YS5oZWlnaHQgOiAwKTtcbiAgICAgICAgICB9IGVsc2UgaWYgKGNyb3BwZWQpIHtcbiAgICAgICAgICAgIG1pbkNhbnZhc1dpZHRoID0gY3JvcEJveERhdGEud2lkdGg7XG4gICAgICAgICAgICBtaW5DYW52YXNIZWlnaHQgPSBjcm9wQm94RGF0YS5oZWlnaHQ7XG4gICAgICAgICAgICBpZiAobWluQ2FudmFzSGVpZ2h0ICogYXNwZWN0UmF0aW8gPiBtaW5DYW52YXNXaWR0aCkge1xuICAgICAgICAgICAgICBtaW5DYW52YXNXaWR0aCA9IG1pbkNhbnZhc0hlaWdodCAqIGFzcGVjdFJhdGlvO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgbWluQ2FudmFzSGVpZ2h0ID0gbWluQ2FudmFzV2lkdGggLyBhc3BlY3RSYXRpbztcbiAgICAgICAgICAgIH1cbiAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgICAgdmFyIF9nZXRBZGp1c3RlZFNpemVzID0gZ2V0QWRqdXN0ZWRTaXplcyh7XG4gICAgICAgICAgYXNwZWN0UmF0aW86IGFzcGVjdFJhdGlvLFxuICAgICAgICAgIHdpZHRoOiBtaW5DYW52YXNXaWR0aCxcbiAgICAgICAgICBoZWlnaHQ6IG1pbkNhbnZhc0hlaWdodFxuICAgICAgICB9KTtcbiAgICAgICAgbWluQ2FudmFzV2lkdGggPSBfZ2V0QWRqdXN0ZWRTaXplcy53aWR0aDtcbiAgICAgICAgbWluQ2FudmFzSGVpZ2h0ID0gX2dldEFkanVzdGVkU2l6ZXMuaGVpZ2h0O1xuICAgICAgICBjYW52YXNEYXRhLm1pbldpZHRoID0gbWluQ2FudmFzV2lkdGg7XG4gICAgICAgIGNhbnZhc0RhdGEubWluSGVpZ2h0ID0gbWluQ2FudmFzSGVpZ2h0O1xuICAgICAgICBjYW52YXNEYXRhLm1heFdpZHRoID0gSW5maW5pdHk7XG4gICAgICAgIGNhbnZhc0RhdGEubWF4SGVpZ2h0ID0gSW5maW5pdHk7XG4gICAgICB9XG4gICAgICBpZiAocG9zaXRpb25MaW1pdGVkKSB7XG4gICAgICAgIGlmICh2aWV3TW9kZSA+IChjcm9wcGVkID8gMCA6IDEpKSB7XG4gICAgICAgICAgdmFyIG5ld0NhbnZhc0xlZnQgPSBjb250YWluZXJEYXRhLndpZHRoIC0gY2FudmFzRGF0YS53aWR0aDtcbiAgICAgICAgICB2YXIgbmV3Q2FudmFzVG9wID0gY29udGFpbmVyRGF0YS5oZWlnaHQgLSBjYW52YXNEYXRhLmhlaWdodDtcbiAgICAgICAgICBjYW52YXNEYXRhLm1pbkxlZnQgPSBNYXRoLm1pbigwLCBuZXdDYW52YXNMZWZ0KTtcbiAgICAgICAgICBjYW52YXNEYXRhLm1pblRvcCA9IE1hdGgubWluKDAsIG5ld0NhbnZhc1RvcCk7XG4gICAgICAgICAgY2FudmFzRGF0YS5tYXhMZWZ0ID0gTWF0aC5tYXgoMCwgbmV3Q2FudmFzTGVmdCk7XG4gICAgICAgICAgY2FudmFzRGF0YS5tYXhUb3AgPSBNYXRoLm1heCgwLCBuZXdDYW52YXNUb3ApO1xuICAgICAgICAgIGlmIChjcm9wcGVkICYmIHRoaXMubGltaXRlZCkge1xuICAgICAgICAgICAgY2FudmFzRGF0YS5taW5MZWZ0ID0gTWF0aC5taW4oY3JvcEJveERhdGEubGVmdCwgY3JvcEJveERhdGEubGVmdCArIChjcm9wQm94RGF0YS53aWR0aCAtIGNhbnZhc0RhdGEud2lkdGgpKTtcbiAgICAgICAgICAgIGNhbnZhc0RhdGEubWluVG9wID0gTWF0aC5taW4oY3JvcEJveERhdGEudG9wLCBjcm9wQm94RGF0YS50b3AgKyAoY3JvcEJveERhdGEuaGVpZ2h0IC0gY2FudmFzRGF0YS5oZWlnaHQpKTtcbiAgICAgICAgICAgIGNhbnZhc0RhdGEubWF4TGVmdCA9IGNyb3BCb3hEYXRhLmxlZnQ7XG4gICAgICAgICAgICBjYW52YXNEYXRhLm1heFRvcCA9IGNyb3BCb3hEYXRhLnRvcDtcbiAgICAgICAgICAgIGlmICh2aWV3TW9kZSA9PT0gMikge1xuICAgICAgICAgICAgICBpZiAoY2FudmFzRGF0YS53aWR0aCA+PSBjb250YWluZXJEYXRhLndpZHRoKSB7XG4gICAgICAgICAgICAgICAgY2FudmFzRGF0YS5taW5MZWZ0ID0gTWF0aC5taW4oMCwgbmV3Q2FudmFzTGVmdCk7XG4gICAgICAgICAgICAgICAgY2FudmFzRGF0YS5tYXhMZWZ0ID0gTWF0aC5tYXgoMCwgbmV3Q2FudmFzTGVmdCk7XG4gICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgaWYgKGNhbnZhc0RhdGEuaGVpZ2h0ID49IGNvbnRhaW5lckRhdGEuaGVpZ2h0KSB7XG4gICAgICAgICAgICAgICAgY2FudmFzRGF0YS5taW5Ub3AgPSBNYXRoLm1pbigwLCBuZXdDYW52YXNUb3ApO1xuICAgICAgICAgICAgICAgIGNhbnZhc0RhdGEubWF4VG9wID0gTWF0aC5tYXgoMCwgbmV3Q2FudmFzVG9wKTtcbiAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICAgIH1cbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICBjYW52YXNEYXRhLm1pbkxlZnQgPSAtY2FudmFzRGF0YS53aWR0aDtcbiAgICAgICAgICBjYW52YXNEYXRhLm1pblRvcCA9IC1jYW52YXNEYXRhLmhlaWdodDtcbiAgICAgICAgICBjYW52YXNEYXRhLm1heExlZnQgPSBjb250YWluZXJEYXRhLndpZHRoO1xuICAgICAgICAgIGNhbnZhc0RhdGEubWF4VG9wID0gY29udGFpbmVyRGF0YS5oZWlnaHQ7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICB9LFxuICAgIHJlbmRlckNhbnZhczogZnVuY3Rpb24gcmVuZGVyQ2FudmFzKGNoYW5nZWQsIHRyYW5zZm9ybWVkKSB7XG4gICAgICB2YXIgY2FudmFzRGF0YSA9IHRoaXMuY2FudmFzRGF0YSxcbiAgICAgICAgaW1hZ2VEYXRhID0gdGhpcy5pbWFnZURhdGE7XG4gICAgICBpZiAodHJhbnNmb3JtZWQpIHtcbiAgICAgICAgdmFyIF9nZXRSb3RhdGVkU2l6ZXMgPSBnZXRSb3RhdGVkU2l6ZXMoe1xuICAgICAgICAgICAgd2lkdGg6IGltYWdlRGF0YS5uYXR1cmFsV2lkdGggKiBNYXRoLmFicyhpbWFnZURhdGEuc2NhbGVYIHx8IDEpLFxuICAgICAgICAgICAgaGVpZ2h0OiBpbWFnZURhdGEubmF0dXJhbEhlaWdodCAqIE1hdGguYWJzKGltYWdlRGF0YS5zY2FsZVkgfHwgMSksXG4gICAgICAgICAgICBkZWdyZWU6IGltYWdlRGF0YS5yb3RhdGUgfHwgMFxuICAgICAgICAgIH0pLFxuICAgICAgICAgIG5hdHVyYWxXaWR0aCA9IF9nZXRSb3RhdGVkU2l6ZXMud2lkdGgsXG4gICAgICAgICAgbmF0dXJhbEhlaWdodCA9IF9nZXRSb3RhdGVkU2l6ZXMuaGVpZ2h0O1xuICAgICAgICB2YXIgd2lkdGggPSBjYW52YXNEYXRhLndpZHRoICogKG5hdHVyYWxXaWR0aCAvIGNhbnZhc0RhdGEubmF0dXJhbFdpZHRoKTtcbiAgICAgICAgdmFyIGhlaWdodCA9IGNhbnZhc0RhdGEuaGVpZ2h0ICogKG5hdHVyYWxIZWlnaHQgLyBjYW52YXNEYXRhLm5hdHVyYWxIZWlnaHQpO1xuICAgICAgICBjYW52YXNEYXRhLmxlZnQgLT0gKHdpZHRoIC0gY2FudmFzRGF0YS53aWR0aCkgLyAyO1xuICAgICAgICBjYW52YXNEYXRhLnRvcCAtPSAoaGVpZ2h0IC0gY2FudmFzRGF0YS5oZWlnaHQpIC8gMjtcbiAgICAgICAgY2FudmFzRGF0YS53aWR0aCA9IHdpZHRoO1xuICAgICAgICBjYW52YXNEYXRhLmhlaWdodCA9IGhlaWdodDtcbiAgICAgICAgY2FudmFzRGF0YS5hc3BlY3RSYXRpbyA9IG5hdHVyYWxXaWR0aCAvIG5hdHVyYWxIZWlnaHQ7XG4gICAgICAgIGNhbnZhc0RhdGEubmF0dXJhbFdpZHRoID0gbmF0dXJhbFdpZHRoO1xuICAgICAgICBjYW52YXNEYXRhLm5hdHVyYWxIZWlnaHQgPSBuYXR1cmFsSGVpZ2h0O1xuICAgICAgICB0aGlzLmxpbWl0Q2FudmFzKHRydWUsIGZhbHNlKTtcbiAgICAgIH1cbiAgICAgIGlmIChjYW52YXNEYXRhLndpZHRoID4gY2FudmFzRGF0YS5tYXhXaWR0aCB8fCBjYW52YXNEYXRhLndpZHRoIDwgY2FudmFzRGF0YS5taW5XaWR0aCkge1xuICAgICAgICBjYW52YXNEYXRhLmxlZnQgPSBjYW52YXNEYXRhLm9sZExlZnQ7XG4gICAgICB9XG4gICAgICBpZiAoY2FudmFzRGF0YS5oZWlnaHQgPiBjYW52YXNEYXRhLm1heEhlaWdodCB8fCBjYW52YXNEYXRhLmhlaWdodCA8IGNhbnZhc0RhdGEubWluSGVpZ2h0KSB7XG4gICAgICAgIGNhbnZhc0RhdGEudG9wID0gY2FudmFzRGF0YS5vbGRUb3A7XG4gICAgICB9XG4gICAgICBjYW52YXNEYXRhLndpZHRoID0gTWF0aC5taW4oTWF0aC5tYXgoY2FudmFzRGF0YS53aWR0aCwgY2FudmFzRGF0YS5taW5XaWR0aCksIGNhbnZhc0RhdGEubWF4V2lkdGgpO1xuICAgICAgY2FudmFzRGF0YS5oZWlnaHQgPSBNYXRoLm1pbihNYXRoLm1heChjYW52YXNEYXRhLmhlaWdodCwgY2FudmFzRGF0YS5taW5IZWlnaHQpLCBjYW52YXNEYXRhLm1heEhlaWdodCk7XG4gICAgICB0aGlzLmxpbWl0Q2FudmFzKGZhbHNlLCB0cnVlKTtcbiAgICAgIGNhbnZhc0RhdGEubGVmdCA9IE1hdGgubWluKE1hdGgubWF4KGNhbnZhc0RhdGEubGVmdCwgY2FudmFzRGF0YS5taW5MZWZ0KSwgY2FudmFzRGF0YS5tYXhMZWZ0KTtcbiAgICAgIGNhbnZhc0RhdGEudG9wID0gTWF0aC5taW4oTWF0aC5tYXgoY2FudmFzRGF0YS50b3AsIGNhbnZhc0RhdGEubWluVG9wKSwgY2FudmFzRGF0YS5tYXhUb3ApO1xuICAgICAgY2FudmFzRGF0YS5vbGRMZWZ0ID0gY2FudmFzRGF0YS5sZWZ0O1xuICAgICAgY2FudmFzRGF0YS5vbGRUb3AgPSBjYW52YXNEYXRhLnRvcDtcbiAgICAgIHNldFN0eWxlKHRoaXMuY2FudmFzLCBhc3NpZ24oe1xuICAgICAgICB3aWR0aDogY2FudmFzRGF0YS53aWR0aCxcbiAgICAgICAgaGVpZ2h0OiBjYW52YXNEYXRhLmhlaWdodFxuICAgICAgfSwgZ2V0VHJhbnNmb3Jtcyh7XG4gICAgICAgIHRyYW5zbGF0ZVg6IGNhbnZhc0RhdGEubGVmdCxcbiAgICAgICAgdHJhbnNsYXRlWTogY2FudmFzRGF0YS50b3BcbiAgICAgIH0pKSk7XG4gICAgICB0aGlzLnJlbmRlckltYWdlKGNoYW5nZWQpO1xuICAgICAgaWYgKHRoaXMuY3JvcHBlZCAmJiB0aGlzLmxpbWl0ZWQpIHtcbiAgICAgICAgdGhpcy5saW1pdENyb3BCb3godHJ1ZSwgdHJ1ZSk7XG4gICAgICB9XG4gICAgfSxcbiAgICByZW5kZXJJbWFnZTogZnVuY3Rpb24gcmVuZGVySW1hZ2UoY2hhbmdlZCkge1xuICAgICAgdmFyIGNhbnZhc0RhdGEgPSB0aGlzLmNhbnZhc0RhdGEsXG4gICAgICAgIGltYWdlRGF0YSA9IHRoaXMuaW1hZ2VEYXRhO1xuICAgICAgdmFyIHdpZHRoID0gaW1hZ2VEYXRhLm5hdHVyYWxXaWR0aCAqIChjYW52YXNEYXRhLndpZHRoIC8gY2FudmFzRGF0YS5uYXR1cmFsV2lkdGgpO1xuICAgICAgdmFyIGhlaWdodCA9IGltYWdlRGF0YS5uYXR1cmFsSGVpZ2h0ICogKGNhbnZhc0RhdGEuaGVpZ2h0IC8gY2FudmFzRGF0YS5uYXR1cmFsSGVpZ2h0KTtcbiAgICAgIGFzc2lnbihpbWFnZURhdGEsIHtcbiAgICAgICAgd2lkdGg6IHdpZHRoLFxuICAgICAgICBoZWlnaHQ6IGhlaWdodCxcbiAgICAgICAgbGVmdDogKGNhbnZhc0RhdGEud2lkdGggLSB3aWR0aCkgLyAyLFxuICAgICAgICB0b3A6IChjYW52YXNEYXRhLmhlaWdodCAtIGhlaWdodCkgLyAyXG4gICAgICB9KTtcbiAgICAgIHNldFN0eWxlKHRoaXMuaW1hZ2UsIGFzc2lnbih7XG4gICAgICAgIHdpZHRoOiBpbWFnZURhdGEud2lkdGgsXG4gICAgICAgIGhlaWdodDogaW1hZ2VEYXRhLmhlaWdodFxuICAgICAgfSwgZ2V0VHJhbnNmb3Jtcyhhc3NpZ24oe1xuICAgICAgICB0cmFuc2xhdGVYOiBpbWFnZURhdGEubGVmdCxcbiAgICAgICAgdHJhbnNsYXRlWTogaW1hZ2VEYXRhLnRvcFxuICAgICAgfSwgaW1hZ2VEYXRhKSkpKTtcbiAgICAgIGlmIChjaGFuZ2VkKSB7XG4gICAgICAgIHRoaXMub3V0cHV0KCk7XG4gICAgICB9XG4gICAgfSxcbiAgICBpbml0Q3JvcEJveDogZnVuY3Rpb24gaW5pdENyb3BCb3goKSB7XG4gICAgICB2YXIgb3B0aW9ucyA9IHRoaXMub3B0aW9ucyxcbiAgICAgICAgY2FudmFzRGF0YSA9IHRoaXMuY2FudmFzRGF0YTtcbiAgICAgIHZhciBhc3BlY3RSYXRpbyA9IG9wdGlvbnMuYXNwZWN0UmF0aW8gfHwgb3B0aW9ucy5pbml0aWFsQXNwZWN0UmF0aW87XG4gICAgICB2YXIgYXV0b0Nyb3BBcmVhID0gTnVtYmVyKG9wdGlvbnMuYXV0b0Nyb3BBcmVhKSB8fCAwLjg7XG4gICAgICB2YXIgY3JvcEJveERhdGEgPSB7XG4gICAgICAgIHdpZHRoOiBjYW52YXNEYXRhLndpZHRoLFxuICAgICAgICBoZWlnaHQ6IGNhbnZhc0RhdGEuaGVpZ2h0XG4gICAgICB9O1xuICAgICAgaWYgKGFzcGVjdFJhdGlvKSB7XG4gICAgICAgIGlmIChjYW52YXNEYXRhLmhlaWdodCAqIGFzcGVjdFJhdGlvID4gY2FudmFzRGF0YS53aWR0aCkge1xuICAgICAgICAgIGNyb3BCb3hEYXRhLmhlaWdodCA9IGNyb3BCb3hEYXRhLndpZHRoIC8gYXNwZWN0UmF0aW87XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgY3JvcEJveERhdGEud2lkdGggPSBjcm9wQm94RGF0YS5oZWlnaHQgKiBhc3BlY3RSYXRpbztcbiAgICAgICAgfVxuICAgICAgfVxuICAgICAgdGhpcy5jcm9wQm94RGF0YSA9IGNyb3BCb3hEYXRhO1xuICAgICAgdGhpcy5saW1pdENyb3BCb3godHJ1ZSwgdHJ1ZSk7XG5cbiAgICAgIC8vIEluaXRpYWxpemUgYXV0byBjcm9wIGFyZWFcbiAgICAgIGNyb3BCb3hEYXRhLndpZHRoID0gTWF0aC5taW4oTWF0aC5tYXgoY3JvcEJveERhdGEud2lkdGgsIGNyb3BCb3hEYXRhLm1pbldpZHRoKSwgY3JvcEJveERhdGEubWF4V2lkdGgpO1xuICAgICAgY3JvcEJveERhdGEuaGVpZ2h0ID0gTWF0aC5taW4oTWF0aC5tYXgoY3JvcEJveERhdGEuaGVpZ2h0LCBjcm9wQm94RGF0YS5taW5IZWlnaHQpLCBjcm9wQm94RGF0YS5tYXhIZWlnaHQpO1xuXG4gICAgICAvLyBUaGUgd2lkdGgvaGVpZ2h0IG9mIGF1dG8gY3JvcCBhcmVhIG11c3QgbGFyZ2UgdGhhbiBcIm1pbldpZHRoL0hlaWdodFwiXG4gICAgICBjcm9wQm94RGF0YS53aWR0aCA9IE1hdGgubWF4KGNyb3BCb3hEYXRhLm1pbldpZHRoLCBjcm9wQm94RGF0YS53aWR0aCAqIGF1dG9Dcm9wQXJlYSk7XG4gICAgICBjcm9wQm94RGF0YS5oZWlnaHQgPSBNYXRoLm1heChjcm9wQm94RGF0YS5taW5IZWlnaHQsIGNyb3BCb3hEYXRhLmhlaWdodCAqIGF1dG9Dcm9wQXJlYSk7XG4gICAgICBjcm9wQm94RGF0YS5sZWZ0ID0gY2FudmFzRGF0YS5sZWZ0ICsgKGNhbnZhc0RhdGEud2lkdGggLSBjcm9wQm94RGF0YS53aWR0aCkgLyAyO1xuICAgICAgY3JvcEJveERhdGEudG9wID0gY2FudmFzRGF0YS50b3AgKyAoY2FudmFzRGF0YS5oZWlnaHQgLSBjcm9wQm94RGF0YS5oZWlnaHQpIC8gMjtcbiAgICAgIGNyb3BCb3hEYXRhLm9sZExlZnQgPSBjcm9wQm94RGF0YS5sZWZ0O1xuICAgICAgY3JvcEJveERhdGEub2xkVG9wID0gY3JvcEJveERhdGEudG9wO1xuICAgICAgdGhpcy5pbml0aWFsQ3JvcEJveERhdGEgPSBhc3NpZ24oe30sIGNyb3BCb3hEYXRhKTtcbiAgICB9LFxuICAgIGxpbWl0Q3JvcEJveDogZnVuY3Rpb24gbGltaXRDcm9wQm94KHNpemVMaW1pdGVkLCBwb3NpdGlvbkxpbWl0ZWQpIHtcbiAgICAgIHZhciBvcHRpb25zID0gdGhpcy5vcHRpb25zLFxuICAgICAgICBjb250YWluZXJEYXRhID0gdGhpcy5jb250YWluZXJEYXRhLFxuICAgICAgICBjYW52YXNEYXRhID0gdGhpcy5jYW52YXNEYXRhLFxuICAgICAgICBjcm9wQm94RGF0YSA9IHRoaXMuY3JvcEJveERhdGEsXG4gICAgICAgIGxpbWl0ZWQgPSB0aGlzLmxpbWl0ZWQ7XG4gICAgICB2YXIgYXNwZWN0UmF0aW8gPSBvcHRpb25zLmFzcGVjdFJhdGlvO1xuICAgICAgaWYgKHNpemVMaW1pdGVkKSB7XG4gICAgICAgIHZhciBtaW5Dcm9wQm94V2lkdGggPSBOdW1iZXIob3B0aW9ucy5taW5Dcm9wQm94V2lkdGgpIHx8IDA7XG4gICAgICAgIHZhciBtaW5Dcm9wQm94SGVpZ2h0ID0gTnVtYmVyKG9wdGlvbnMubWluQ3JvcEJveEhlaWdodCkgfHwgMDtcbiAgICAgICAgdmFyIG1heENyb3BCb3hXaWR0aCA9IGxpbWl0ZWQgPyBNYXRoLm1pbihjb250YWluZXJEYXRhLndpZHRoLCBjYW52YXNEYXRhLndpZHRoLCBjYW52YXNEYXRhLndpZHRoICsgY2FudmFzRGF0YS5sZWZ0LCBjb250YWluZXJEYXRhLndpZHRoIC0gY2FudmFzRGF0YS5sZWZ0KSA6IGNvbnRhaW5lckRhdGEud2lkdGg7XG4gICAgICAgIHZhciBtYXhDcm9wQm94SGVpZ2h0ID0gbGltaXRlZCA/IE1hdGgubWluKGNvbnRhaW5lckRhdGEuaGVpZ2h0LCBjYW52YXNEYXRhLmhlaWdodCwgY2FudmFzRGF0YS5oZWlnaHQgKyBjYW52YXNEYXRhLnRvcCwgY29udGFpbmVyRGF0YS5oZWlnaHQgLSBjYW52YXNEYXRhLnRvcCkgOiBjb250YWluZXJEYXRhLmhlaWdodDtcblxuICAgICAgICAvLyBUaGUgbWluL21heENyb3BCb3hXaWR0aC9IZWlnaHQgbXVzdCBiZSBsZXNzIHRoYW4gY29udGFpbmVyJ3Mgd2lkdGgvaGVpZ2h0XG4gICAgICAgIG1pbkNyb3BCb3hXaWR0aCA9IE1hdGgubWluKG1pbkNyb3BCb3hXaWR0aCwgY29udGFpbmVyRGF0YS53aWR0aCk7XG4gICAgICAgIG1pbkNyb3BCb3hIZWlnaHQgPSBNYXRoLm1pbihtaW5Dcm9wQm94SGVpZ2h0LCBjb250YWluZXJEYXRhLmhlaWdodCk7XG4gICAgICAgIGlmIChhc3BlY3RSYXRpbykge1xuICAgICAgICAgIGlmIChtaW5Dcm9wQm94V2lkdGggJiYgbWluQ3JvcEJveEhlaWdodCkge1xuICAgICAgICAgICAgaWYgKG1pbkNyb3BCb3hIZWlnaHQgKiBhc3BlY3RSYXRpbyA+IG1pbkNyb3BCb3hXaWR0aCkge1xuICAgICAgICAgICAgICBtaW5Dcm9wQm94SGVpZ2h0ID0gbWluQ3JvcEJveFdpZHRoIC8gYXNwZWN0UmF0aW87XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICBtaW5Dcm9wQm94V2lkdGggPSBtaW5Dcm9wQm94SGVpZ2h0ICogYXNwZWN0UmF0aW87XG4gICAgICAgICAgICB9XG4gICAgICAgICAgfSBlbHNlIGlmIChtaW5Dcm9wQm94V2lkdGgpIHtcbiAgICAgICAgICAgIG1pbkNyb3BCb3hIZWlnaHQgPSBtaW5Dcm9wQm94V2lkdGggLyBhc3BlY3RSYXRpbztcbiAgICAgICAgICB9IGVsc2UgaWYgKG1pbkNyb3BCb3hIZWlnaHQpIHtcbiAgICAgICAgICAgIG1pbkNyb3BCb3hXaWR0aCA9IG1pbkNyb3BCb3hIZWlnaHQgKiBhc3BlY3RSYXRpbztcbiAgICAgICAgICB9XG4gICAgICAgICAgaWYgKG1heENyb3BCb3hIZWlnaHQgKiBhc3BlY3RSYXRpbyA+IG1heENyb3BCb3hXaWR0aCkge1xuICAgICAgICAgICAgbWF4Q3JvcEJveEhlaWdodCA9IG1heENyb3BCb3hXaWR0aCAvIGFzcGVjdFJhdGlvO1xuICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICBtYXhDcm9wQm94V2lkdGggPSBtYXhDcm9wQm94SGVpZ2h0ICogYXNwZWN0UmF0aW87XG4gICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgLy8gVGhlIG1pbldpZHRoL0hlaWdodCBtdXN0IGJlIGxlc3MgdGhhbiBtYXhXaWR0aC9IZWlnaHRcbiAgICAgICAgY3JvcEJveERhdGEubWluV2lkdGggPSBNYXRoLm1pbihtaW5Dcm9wQm94V2lkdGgsIG1heENyb3BCb3hXaWR0aCk7XG4gICAgICAgIGNyb3BCb3hEYXRhLm1pbkhlaWdodCA9IE1hdGgubWluKG1pbkNyb3BCb3hIZWlnaHQsIG1heENyb3BCb3hIZWlnaHQpO1xuICAgICAgICBjcm9wQm94RGF0YS5tYXhXaWR0aCA9IG1heENyb3BCb3hXaWR0aDtcbiAgICAgICAgY3JvcEJveERhdGEubWF4SGVpZ2h0ID0gbWF4Q3JvcEJveEhlaWdodDtcbiAgICAgIH1cbiAgICAgIGlmIChwb3NpdGlvbkxpbWl0ZWQpIHtcbiAgICAgICAgaWYgKGxpbWl0ZWQpIHtcbiAgICAgICAgICBjcm9wQm94RGF0YS5taW5MZWZ0ID0gTWF0aC5tYXgoMCwgY2FudmFzRGF0YS5sZWZ0KTtcbiAgICAgICAgICBjcm9wQm94RGF0YS5taW5Ub3AgPSBNYXRoLm1heCgwLCBjYW52YXNEYXRhLnRvcCk7XG4gICAgICAgICAgY3JvcEJveERhdGEubWF4TGVmdCA9IE1hdGgubWluKGNvbnRhaW5lckRhdGEud2lkdGgsIGNhbnZhc0RhdGEubGVmdCArIGNhbnZhc0RhdGEud2lkdGgpIC0gY3JvcEJveERhdGEud2lkdGg7XG4gICAgICAgICAgY3JvcEJveERhdGEubWF4VG9wID0gTWF0aC5taW4oY29udGFpbmVyRGF0YS5oZWlnaHQsIGNhbnZhc0RhdGEudG9wICsgY2FudmFzRGF0YS5oZWlnaHQpIC0gY3JvcEJveERhdGEuaGVpZ2h0O1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgIGNyb3BCb3hEYXRhLm1pbkxlZnQgPSAwO1xuICAgICAgICAgIGNyb3BCb3hEYXRhLm1pblRvcCA9IDA7XG4gICAgICAgICAgY3JvcEJveERhdGEubWF4TGVmdCA9IGNvbnRhaW5lckRhdGEud2lkdGggLSBjcm9wQm94RGF0YS53aWR0aDtcbiAgICAgICAgICBjcm9wQm94RGF0YS5tYXhUb3AgPSBjb250YWluZXJEYXRhLmhlaWdodCAtIGNyb3BCb3hEYXRhLmhlaWdodDtcbiAgICAgICAgfVxuICAgICAgfVxuICAgIH0sXG4gICAgcmVuZGVyQ3JvcEJveDogZnVuY3Rpb24gcmVuZGVyQ3JvcEJveCgpIHtcbiAgICAgIHZhciBvcHRpb25zID0gdGhpcy5vcHRpb25zLFxuICAgICAgICBjb250YWluZXJEYXRhID0gdGhpcy5jb250YWluZXJEYXRhLFxuICAgICAgICBjcm9wQm94RGF0YSA9IHRoaXMuY3JvcEJveERhdGE7XG4gICAgICBpZiAoY3JvcEJveERhdGEud2lkdGggPiBjcm9wQm94RGF0YS5tYXhXaWR0aCB8fCBjcm9wQm94RGF0YS53aWR0aCA8IGNyb3BCb3hEYXRhLm1pbldpZHRoKSB7XG4gICAgICAgIGNyb3BCb3hEYXRhLmxlZnQgPSBjcm9wQm94RGF0YS5vbGRMZWZ0O1xuICAgICAgfVxuICAgICAgaWYgKGNyb3BCb3hEYXRhLmhlaWdodCA+IGNyb3BCb3hEYXRhLm1heEhlaWdodCB8fCBjcm9wQm94RGF0YS5oZWlnaHQgPCBjcm9wQm94RGF0YS5taW5IZWlnaHQpIHtcbiAgICAgICAgY3JvcEJveERhdGEudG9wID0gY3JvcEJveERhdGEub2xkVG9wO1xuICAgICAgfVxuICAgICAgY3JvcEJveERhdGEud2lkdGggPSBNYXRoLm1pbihNYXRoLm1heChjcm9wQm94RGF0YS53aWR0aCwgY3JvcEJveERhdGEubWluV2lkdGgpLCBjcm9wQm94RGF0YS5tYXhXaWR0aCk7XG4gICAgICBjcm9wQm94RGF0YS5oZWlnaHQgPSBNYXRoLm1pbihNYXRoLm1heChjcm9wQm94RGF0YS5oZWlnaHQsIGNyb3BCb3hEYXRhLm1pbkhlaWdodCksIGNyb3BCb3hEYXRhLm1heEhlaWdodCk7XG4gICAgICB0aGlzLmxpbWl0Q3JvcEJveChmYWxzZSwgdHJ1ZSk7XG4gICAgICBjcm9wQm94RGF0YS5sZWZ0ID0gTWF0aC5taW4oTWF0aC5tYXgoY3JvcEJveERhdGEubGVmdCwgY3JvcEJveERhdGEubWluTGVmdCksIGNyb3BCb3hEYXRhLm1heExlZnQpO1xuICAgICAgY3JvcEJveERhdGEudG9wID0gTWF0aC5taW4oTWF0aC5tYXgoY3JvcEJveERhdGEudG9wLCBjcm9wQm94RGF0YS5taW5Ub3ApLCBjcm9wQm94RGF0YS5tYXhUb3ApO1xuICAgICAgY3JvcEJveERhdGEub2xkTGVmdCA9IGNyb3BCb3hEYXRhLmxlZnQ7XG4gICAgICBjcm9wQm94RGF0YS5vbGRUb3AgPSBjcm9wQm94RGF0YS50b3A7XG4gICAgICBpZiAob3B0aW9ucy5tb3ZhYmxlICYmIG9wdGlvbnMuY3JvcEJveE1vdmFibGUpIHtcbiAgICAgICAgLy8gVHVybiB0byBtb3ZlIHRoZSBjYW52YXMgd2hlbiB0aGUgY3JvcCBib3ggaXMgZXF1YWwgdG8gdGhlIGNvbnRhaW5lclxuICAgICAgICBzZXREYXRhKHRoaXMuZmFjZSwgREFUQV9BQ1RJT04sIGNyb3BCb3hEYXRhLndpZHRoID49IGNvbnRhaW5lckRhdGEud2lkdGggJiYgY3JvcEJveERhdGEuaGVpZ2h0ID49IGNvbnRhaW5lckRhdGEuaGVpZ2h0ID8gQUNUSU9OX01PVkUgOiBBQ1RJT05fQUxMKTtcbiAgICAgIH1cbiAgICAgIHNldFN0eWxlKHRoaXMuY3JvcEJveCwgYXNzaWduKHtcbiAgICAgICAgd2lkdGg6IGNyb3BCb3hEYXRhLndpZHRoLFxuICAgICAgICBoZWlnaHQ6IGNyb3BCb3hEYXRhLmhlaWdodFxuICAgICAgfSwgZ2V0VHJhbnNmb3Jtcyh7XG4gICAgICAgIHRyYW5zbGF0ZVg6IGNyb3BCb3hEYXRhLmxlZnQsXG4gICAgICAgIHRyYW5zbGF0ZVk6IGNyb3BCb3hEYXRhLnRvcFxuICAgICAgfSkpKTtcbiAgICAgIGlmICh0aGlzLmNyb3BwZWQgJiYgdGhpcy5saW1pdGVkKSB7XG4gICAgICAgIHRoaXMubGltaXRDYW52YXModHJ1ZSwgdHJ1ZSk7XG4gICAgICB9XG4gICAgICBpZiAoIXRoaXMuZGlzYWJsZWQpIHtcbiAgICAgICAgdGhpcy5vdXRwdXQoKTtcbiAgICAgIH1cbiAgICB9LFxuICAgIG91dHB1dDogZnVuY3Rpb24gb3V0cHV0KCkge1xuICAgICAgdGhpcy5wcmV2aWV3KCk7XG4gICAgICBkaXNwYXRjaEV2ZW50KHRoaXMuZWxlbWVudCwgRVZFTlRfQ1JPUCwgdGhpcy5nZXREYXRhKCkpO1xuICAgIH1cbiAgfTtcblxuICB2YXIgcHJldmlldyA9IHtcbiAgICBpbml0UHJldmlldzogZnVuY3Rpb24gaW5pdFByZXZpZXcoKSB7XG4gICAgICB2YXIgZWxlbWVudCA9IHRoaXMuZWxlbWVudCxcbiAgICAgICAgY3Jvc3NPcmlnaW4gPSB0aGlzLmNyb3NzT3JpZ2luO1xuICAgICAgdmFyIHByZXZpZXcgPSB0aGlzLm9wdGlvbnMucHJldmlldztcbiAgICAgIHZhciB1cmwgPSBjcm9zc09yaWdpbiA/IHRoaXMuY3Jvc3NPcmlnaW5VcmwgOiB0aGlzLnVybDtcbiAgICAgIHZhciBhbHQgPSBlbGVtZW50LmFsdCB8fCAnVGhlIGltYWdlIHRvIHByZXZpZXcnO1xuICAgICAgdmFyIGltYWdlID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnaW1nJyk7XG4gICAgICBpZiAoY3Jvc3NPcmlnaW4pIHtcbiAgICAgICAgaW1hZ2UuY3Jvc3NPcmlnaW4gPSBjcm9zc09yaWdpbjtcbiAgICAgIH1cbiAgICAgIGltYWdlLnNyYyA9IHVybDtcbiAgICAgIGltYWdlLmFsdCA9IGFsdDtcbiAgICAgIHRoaXMudmlld0JveC5hcHBlbmRDaGlsZChpbWFnZSk7XG4gICAgICB0aGlzLnZpZXdCb3hJbWFnZSA9IGltYWdlO1xuICAgICAgaWYgKCFwcmV2aWV3KSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIHZhciBwcmV2aWV3cyA9IHByZXZpZXc7XG4gICAgICBpZiAodHlwZW9mIHByZXZpZXcgPT09ICdzdHJpbmcnKSB7XG4gICAgICAgIHByZXZpZXdzID0gZWxlbWVudC5vd25lckRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwocHJldmlldyk7XG4gICAgICB9IGVsc2UgaWYgKHByZXZpZXcucXVlcnlTZWxlY3Rvcikge1xuICAgICAgICBwcmV2aWV3cyA9IFtwcmV2aWV3XTtcbiAgICAgIH1cbiAgICAgIHRoaXMucHJldmlld3MgPSBwcmV2aWV3cztcbiAgICAgIGZvckVhY2gocHJldmlld3MsIGZ1bmN0aW9uIChlbCkge1xuICAgICAgICB2YXIgaW1nID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnaW1nJyk7XG5cbiAgICAgICAgLy8gU2F2ZSB0aGUgb3JpZ2luYWwgc2l6ZSBmb3IgcmVjb3ZlclxuICAgICAgICBzZXREYXRhKGVsLCBEQVRBX1BSRVZJRVcsIHtcbiAgICAgICAgICB3aWR0aDogZWwub2Zmc2V0V2lkdGgsXG4gICAgICAgICAgaGVpZ2h0OiBlbC5vZmZzZXRIZWlnaHQsXG4gICAgICAgICAgaHRtbDogZWwuaW5uZXJIVE1MXG4gICAgICAgIH0pO1xuICAgICAgICBpZiAoY3Jvc3NPcmlnaW4pIHtcbiAgICAgICAgICBpbWcuY3Jvc3NPcmlnaW4gPSBjcm9zc09yaWdpbjtcbiAgICAgICAgfVxuICAgICAgICBpbWcuc3JjID0gdXJsO1xuICAgICAgICBpbWcuYWx0ID0gYWx0O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBPdmVycmlkZSBpbWcgZWxlbWVudCBzdHlsZXNcbiAgICAgICAgICogQWRkIGBkaXNwbGF5OmJsb2NrYCB0byBhdm9pZCBtYXJnaW4gdG9wIGlzc3VlXG4gICAgICAgICAqIEFkZCBgaGVpZ2h0OmF1dG9gIHRvIG92ZXJyaWRlIGBoZWlnaHRgIGF0dHJpYnV0ZSBvbiBJRThcbiAgICAgICAgICogKE9jY3VyIG9ubHkgd2hlbiBtYXJnaW4tdG9wIDw9IC1oZWlnaHQpXG4gICAgICAgICAqL1xuICAgICAgICBpbWcuc3R5bGUuY3NzVGV4dCA9ICdkaXNwbGF5OmJsb2NrOycgKyAnd2lkdGg6MTAwJTsnICsgJ2hlaWdodDphdXRvOycgKyAnbWluLXdpZHRoOjAhaW1wb3J0YW50OycgKyAnbWluLWhlaWdodDowIWltcG9ydGFudDsnICsgJ21heC13aWR0aDpub25lIWltcG9ydGFudDsnICsgJ21heC1oZWlnaHQ6bm9uZSFpbXBvcnRhbnQ7JyArICdpbWFnZS1vcmllbnRhdGlvbjowZGVnIWltcG9ydGFudDtcIic7XG4gICAgICAgIGVsLmlubmVySFRNTCA9ICcnO1xuICAgICAgICBlbC5hcHBlbmRDaGlsZChpbWcpO1xuICAgICAgfSk7XG4gICAgfSxcbiAgICByZXNldFByZXZpZXc6IGZ1bmN0aW9uIHJlc2V0UHJldmlldygpIHtcbiAgICAgIGZvckVhY2godGhpcy5wcmV2aWV3cywgZnVuY3Rpb24gKGVsZW1lbnQpIHtcbiAgICAgICAgdmFyIGRhdGEgPSBnZXREYXRhKGVsZW1lbnQsIERBVEFfUFJFVklFVyk7XG4gICAgICAgIHNldFN0eWxlKGVsZW1lbnQsIHtcbiAgICAgICAgICB3aWR0aDogZGF0YS53aWR0aCxcbiAgICAgICAgICBoZWlnaHQ6IGRhdGEuaGVpZ2h0XG4gICAgICAgIH0pO1xuICAgICAgICBlbGVtZW50LmlubmVySFRNTCA9IGRhdGEuaHRtbDtcbiAgICAgICAgcmVtb3ZlRGF0YShlbGVtZW50LCBEQVRBX1BSRVZJRVcpO1xuICAgICAgfSk7XG4gICAgfSxcbiAgICBwcmV2aWV3OiBmdW5jdGlvbiBwcmV2aWV3KCkge1xuICAgICAgdmFyIGltYWdlRGF0YSA9IHRoaXMuaW1hZ2VEYXRhLFxuICAgICAgICBjYW52YXNEYXRhID0gdGhpcy5jYW52YXNEYXRhLFxuICAgICAgICBjcm9wQm94RGF0YSA9IHRoaXMuY3JvcEJveERhdGE7XG4gICAgICB2YXIgY3JvcEJveFdpZHRoID0gY3JvcEJveERhdGEud2lkdGgsXG4gICAgICAgIGNyb3BCb3hIZWlnaHQgPSBjcm9wQm94RGF0YS5oZWlnaHQ7XG4gICAgICB2YXIgd2lkdGggPSBpbWFnZURhdGEud2lkdGgsXG4gICAgICAgIGhlaWdodCA9IGltYWdlRGF0YS5oZWlnaHQ7XG4gICAgICB2YXIgbGVmdCA9IGNyb3BCb3hEYXRhLmxlZnQgLSBjYW52YXNEYXRhLmxlZnQgLSBpbWFnZURhdGEubGVmdDtcbiAgICAgIHZhciB0b3AgPSBjcm9wQm94RGF0YS50b3AgLSBjYW52YXNEYXRhLnRvcCAtIGltYWdlRGF0YS50b3A7XG4gICAgICBpZiAoIXRoaXMuY3JvcHBlZCB8fCB0aGlzLmRpc2FibGVkKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIHNldFN0eWxlKHRoaXMudmlld0JveEltYWdlLCBhc3NpZ24oe1xuICAgICAgICB3aWR0aDogd2lkdGgsXG4gICAgICAgIGhlaWdodDogaGVpZ2h0XG4gICAgICB9LCBnZXRUcmFuc2Zvcm1zKGFzc2lnbih7XG4gICAgICAgIHRyYW5zbGF0ZVg6IC1sZWZ0LFxuICAgICAgICB0cmFuc2xhdGVZOiAtdG9wXG4gICAgICB9LCBpbWFnZURhdGEpKSkpO1xuICAgICAgZm9yRWFjaCh0aGlzLnByZXZpZXdzLCBmdW5jdGlvbiAoZWxlbWVudCkge1xuICAgICAgICB2YXIgZGF0YSA9IGdldERhdGEoZWxlbWVudCwgREFUQV9QUkVWSUVXKTtcbiAgICAgICAgdmFyIG9yaWdpbmFsV2lkdGggPSBkYXRhLndpZHRoO1xuICAgICAgICB2YXIgb3JpZ2luYWxIZWlnaHQgPSBkYXRhLmhlaWdodDtcbiAgICAgICAgdmFyIG5ld1dpZHRoID0gb3JpZ2luYWxXaWR0aDtcbiAgICAgICAgdmFyIG5ld0hlaWdodCA9IG9yaWdpbmFsSGVpZ2h0O1xuICAgICAgICB2YXIgcmF0aW8gPSAxO1xuICAgICAgICBpZiAoY3JvcEJveFdpZHRoKSB7XG4gICAgICAgICAgcmF0aW8gPSBvcmlnaW5hbFdpZHRoIC8gY3JvcEJveFdpZHRoO1xuICAgICAgICAgIG5ld0hlaWdodCA9IGNyb3BCb3hIZWlnaHQgKiByYXRpbztcbiAgICAgICAgfVxuICAgICAgICBpZiAoY3JvcEJveEhlaWdodCAmJiBuZXdIZWlnaHQgPiBvcmlnaW5hbEhlaWdodCkge1xuICAgICAgICAgIHJhdGlvID0gb3JpZ2luYWxIZWlnaHQgLyBjcm9wQm94SGVpZ2h0O1xuICAgICAgICAgIG5ld1dpZHRoID0gY3JvcEJveFdpZHRoICogcmF0aW87XG4gICAgICAgICAgbmV3SGVpZ2h0ID0gb3JpZ2luYWxIZWlnaHQ7XG4gICAgICAgIH1cbiAgICAgICAgc2V0U3R5bGUoZWxlbWVudCwge1xuICAgICAgICAgIHdpZHRoOiBuZXdXaWR0aCxcbiAgICAgICAgICBoZWlnaHQ6IG5ld0hlaWdodFxuICAgICAgICB9KTtcbiAgICAgICAgc2V0U3R5bGUoZWxlbWVudC5nZXRFbGVtZW50c0J5VGFnTmFtZSgnaW1nJylbMF0sIGFzc2lnbih7XG4gICAgICAgICAgd2lkdGg6IHdpZHRoICogcmF0aW8sXG4gICAgICAgICAgaGVpZ2h0OiBoZWlnaHQgKiByYXRpb1xuICAgICAgICB9LCBnZXRUcmFuc2Zvcm1zKGFzc2lnbih7XG4gICAgICAgICAgdHJhbnNsYXRlWDogLWxlZnQgKiByYXRpbyxcbiAgICAgICAgICB0cmFuc2xhdGVZOiAtdG9wICogcmF0aW9cbiAgICAgICAgfSwgaW1hZ2VEYXRhKSkpKTtcbiAgICAgIH0pO1xuICAgIH1cbiAgfTtcblxuICB2YXIgZXZlbnRzID0ge1xuICAgIGJpbmQ6IGZ1bmN0aW9uIGJpbmQoKSB7XG4gICAgICB2YXIgZWxlbWVudCA9IHRoaXMuZWxlbWVudCxcbiAgICAgICAgb3B0aW9ucyA9IHRoaXMub3B0aW9ucyxcbiAgICAgICAgY3JvcHBlciA9IHRoaXMuY3JvcHBlcjtcbiAgICAgIGlmIChpc0Z1bmN0aW9uKG9wdGlvbnMuY3JvcHN0YXJ0KSkge1xuICAgICAgICBhZGRMaXN0ZW5lcihlbGVtZW50LCBFVkVOVF9DUk9QX1NUQVJULCBvcHRpb25zLmNyb3BzdGFydCk7XG4gICAgICB9XG4gICAgICBpZiAoaXNGdW5jdGlvbihvcHRpb25zLmNyb3Btb3ZlKSkge1xuICAgICAgICBhZGRMaXN0ZW5lcihlbGVtZW50LCBFVkVOVF9DUk9QX01PVkUsIG9wdGlvbnMuY3JvcG1vdmUpO1xuICAgICAgfVxuICAgICAgaWYgKGlzRnVuY3Rpb24ob3B0aW9ucy5jcm9wZW5kKSkge1xuICAgICAgICBhZGRMaXN0ZW5lcihlbGVtZW50LCBFVkVOVF9DUk9QX0VORCwgb3B0aW9ucy5jcm9wZW5kKTtcbiAgICAgIH1cbiAgICAgIGlmIChpc0Z1bmN0aW9uKG9wdGlvbnMuY3JvcCkpIHtcbiAgICAgICAgYWRkTGlzdGVuZXIoZWxlbWVudCwgRVZFTlRfQ1JPUCwgb3B0aW9ucy5jcm9wKTtcbiAgICAgIH1cbiAgICAgIGlmIChpc0Z1bmN0aW9uKG9wdGlvbnMuem9vbSkpIHtcbiAgICAgICAgYWRkTGlzdGVuZXIoZWxlbWVudCwgRVZFTlRfWk9PTSwgb3B0aW9ucy56b29tKTtcbiAgICAgIH1cbiAgICAgIGFkZExpc3RlbmVyKGNyb3BwZXIsIEVWRU5UX1BPSU5URVJfRE9XTiwgdGhpcy5vbkNyb3BTdGFydCA9IHRoaXMuY3JvcFN0YXJ0LmJpbmQodGhpcykpO1xuICAgICAgaWYgKG9wdGlvbnMuem9vbWFibGUgJiYgb3B0aW9ucy56b29tT25XaGVlbCkge1xuICAgICAgICBhZGRMaXN0ZW5lcihjcm9wcGVyLCBFVkVOVF9XSEVFTCwgdGhpcy5vbldoZWVsID0gdGhpcy53aGVlbC5iaW5kKHRoaXMpLCB7XG4gICAgICAgICAgcGFzc2l2ZTogZmFsc2UsXG4gICAgICAgICAgY2FwdHVyZTogdHJ1ZVxuICAgICAgICB9KTtcbiAgICAgIH1cbiAgICAgIGlmIChvcHRpb25zLnRvZ2dsZURyYWdNb2RlT25EYmxjbGljaykge1xuICAgICAgICBhZGRMaXN0ZW5lcihjcm9wcGVyLCBFVkVOVF9EQkxDTElDSywgdGhpcy5vbkRibGNsaWNrID0gdGhpcy5kYmxjbGljay5iaW5kKHRoaXMpKTtcbiAgICAgIH1cbiAgICAgIGFkZExpc3RlbmVyKGVsZW1lbnQub3duZXJEb2N1bWVudCwgRVZFTlRfUE9JTlRFUl9NT1ZFLCB0aGlzLm9uQ3JvcE1vdmUgPSB0aGlzLmNyb3BNb3ZlLmJpbmQodGhpcykpO1xuICAgICAgYWRkTGlzdGVuZXIoZWxlbWVudC5vd25lckRvY3VtZW50LCBFVkVOVF9QT0lOVEVSX1VQLCB0aGlzLm9uQ3JvcEVuZCA9IHRoaXMuY3JvcEVuZC5iaW5kKHRoaXMpKTtcbiAgICAgIGlmIChvcHRpb25zLnJlc3BvbnNpdmUpIHtcbiAgICAgICAgYWRkTGlzdGVuZXIod2luZG93LCBFVkVOVF9SRVNJWkUsIHRoaXMub25SZXNpemUgPSB0aGlzLnJlc2l6ZS5iaW5kKHRoaXMpKTtcbiAgICAgIH1cbiAgICB9LFxuICAgIHVuYmluZDogZnVuY3Rpb24gdW5iaW5kKCkge1xuICAgICAgdmFyIGVsZW1lbnQgPSB0aGlzLmVsZW1lbnQsXG4gICAgICAgIG9wdGlvbnMgPSB0aGlzLm9wdGlvbnMsXG4gICAgICAgIGNyb3BwZXIgPSB0aGlzLmNyb3BwZXI7XG4gICAgICBpZiAoaXNGdW5jdGlvbihvcHRpb25zLmNyb3BzdGFydCkpIHtcbiAgICAgICAgcmVtb3ZlTGlzdGVuZXIoZWxlbWVudCwgRVZFTlRfQ1JPUF9TVEFSVCwgb3B0aW9ucy5jcm9wc3RhcnQpO1xuICAgICAgfVxuICAgICAgaWYgKGlzRnVuY3Rpb24ob3B0aW9ucy5jcm9wbW92ZSkpIHtcbiAgICAgICAgcmVtb3ZlTGlzdGVuZXIoZWxlbWVudCwgRVZFTlRfQ1JPUF9NT1ZFLCBvcHRpb25zLmNyb3Btb3ZlKTtcbiAgICAgIH1cbiAgICAgIGlmIChpc0Z1bmN0aW9uKG9wdGlvbnMuY3JvcGVuZCkpIHtcbiAgICAgICAgcmVtb3ZlTGlzdGVuZXIoZWxlbWVudCwgRVZFTlRfQ1JPUF9FTkQsIG9wdGlvbnMuY3JvcGVuZCk7XG4gICAgICB9XG4gICAgICBpZiAoaXNGdW5jdGlvbihvcHRpb25zLmNyb3ApKSB7XG4gICAgICAgIHJlbW92ZUxpc3RlbmVyKGVsZW1lbnQsIEVWRU5UX0NST1AsIG9wdGlvbnMuY3JvcCk7XG4gICAgICB9XG4gICAgICBpZiAoaXNGdW5jdGlvbihvcHRpb25zLnpvb20pKSB7XG4gICAgICAgIHJlbW92ZUxpc3RlbmVyKGVsZW1lbnQsIEVWRU5UX1pPT00sIG9wdGlvbnMuem9vbSk7XG4gICAgICB9XG4gICAgICByZW1vdmVMaXN0ZW5lcihjcm9wcGVyLCBFVkVOVF9QT0lOVEVSX0RPV04sIHRoaXMub25Dcm9wU3RhcnQpO1xuICAgICAgaWYgKG9wdGlvbnMuem9vbWFibGUgJiYgb3B0aW9ucy56b29tT25XaGVlbCkge1xuICAgICAgICByZW1vdmVMaXN0ZW5lcihjcm9wcGVyLCBFVkVOVF9XSEVFTCwgdGhpcy5vbldoZWVsLCB7XG4gICAgICAgICAgcGFzc2l2ZTogZmFsc2UsXG4gICAgICAgICAgY2FwdHVyZTogdHJ1ZVxuICAgICAgICB9KTtcbiAgICAgIH1cbiAgICAgIGlmIChvcHRpb25zLnRvZ2dsZURyYWdNb2RlT25EYmxjbGljaykge1xuICAgICAgICByZW1vdmVMaXN0ZW5lcihjcm9wcGVyLCBFVkVOVF9EQkxDTElDSywgdGhpcy5vbkRibGNsaWNrKTtcbiAgICAgIH1cbiAgICAgIHJlbW92ZUxpc3RlbmVyKGVsZW1lbnQub3duZXJEb2N1bWVudCwgRVZFTlRfUE9JTlRFUl9NT1ZFLCB0aGlzLm9uQ3JvcE1vdmUpO1xuICAgICAgcmVtb3ZlTGlzdGVuZXIoZWxlbWVudC5vd25lckRvY3VtZW50LCBFVkVOVF9QT0lOVEVSX1VQLCB0aGlzLm9uQ3JvcEVuZCk7XG4gICAgICBpZiAob3B0aW9ucy5yZXNwb25zaXZlKSB7XG4gICAgICAgIHJlbW92ZUxpc3RlbmVyKHdpbmRvdywgRVZFTlRfUkVTSVpFLCB0aGlzLm9uUmVzaXplKTtcbiAgICAgIH1cbiAgICB9XG4gIH07XG5cbiAgdmFyIGhhbmRsZXJzID0ge1xuICAgIHJlc2l6ZTogZnVuY3Rpb24gcmVzaXplKCkge1xuICAgICAgaWYgKHRoaXMuZGlzYWJsZWQpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgICAgdmFyIG9wdGlvbnMgPSB0aGlzLm9wdGlvbnMsXG4gICAgICAgIGNvbnRhaW5lciA9IHRoaXMuY29udGFpbmVyLFxuICAgICAgICBjb250YWluZXJEYXRhID0gdGhpcy5jb250YWluZXJEYXRhO1xuICAgICAgdmFyIHJhdGlvWCA9IGNvbnRhaW5lci5vZmZzZXRXaWR0aCAvIGNvbnRhaW5lckRhdGEud2lkdGg7XG4gICAgICB2YXIgcmF0aW9ZID0gY29udGFpbmVyLm9mZnNldEhlaWdodCAvIGNvbnRhaW5lckRhdGEuaGVpZ2h0O1xuICAgICAgdmFyIHJhdGlvID0gTWF0aC5hYnMocmF0aW9YIC0gMSkgPiBNYXRoLmFicyhyYXRpb1kgLSAxKSA/IHJhdGlvWCA6IHJhdGlvWTtcblxuICAgICAgLy8gUmVzaXplIHdoZW4gd2lkdGggY2hhbmdlZCBvciBoZWlnaHQgY2hhbmdlZFxuICAgICAgaWYgKHJhdGlvICE9PSAxKSB7XG4gICAgICAgIHZhciBjYW52YXNEYXRhO1xuICAgICAgICB2YXIgY3JvcEJveERhdGE7XG4gICAgICAgIGlmIChvcHRpb25zLnJlc3RvcmUpIHtcbiAgICAgICAgICBjYW52YXNEYXRhID0gdGhpcy5nZXRDYW52YXNEYXRhKCk7XG4gICAgICAgICAgY3JvcEJveERhdGEgPSB0aGlzLmdldENyb3BCb3hEYXRhKCk7XG4gICAgICAgIH1cbiAgICAgICAgdGhpcy5yZW5kZXIoKTtcbiAgICAgICAgaWYgKG9wdGlvbnMucmVzdG9yZSkge1xuICAgICAgICAgIHRoaXMuc2V0Q2FudmFzRGF0YShmb3JFYWNoKGNhbnZhc0RhdGEsIGZ1bmN0aW9uIChuLCBpKSB7XG4gICAgICAgICAgICBjYW52YXNEYXRhW2ldID0gbiAqIHJhdGlvO1xuICAgICAgICAgIH0pKTtcbiAgICAgICAgICB0aGlzLnNldENyb3BCb3hEYXRhKGZvckVhY2goY3JvcEJveERhdGEsIGZ1bmN0aW9uIChuLCBpKSB7XG4gICAgICAgICAgICBjcm9wQm94RGF0YVtpXSA9IG4gKiByYXRpbztcbiAgICAgICAgICB9KSk7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICB9LFxuICAgIGRibGNsaWNrOiBmdW5jdGlvbiBkYmxjbGljaygpIHtcbiAgICAgIGlmICh0aGlzLmRpc2FibGVkIHx8IHRoaXMub3B0aW9ucy5kcmFnTW9kZSA9PT0gRFJBR19NT0RFX05PTkUpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgICAgdGhpcy5zZXREcmFnTW9kZShoYXNDbGFzcyh0aGlzLmRyYWdCb3gsIENMQVNTX0NST1ApID8gRFJBR19NT0RFX01PVkUgOiBEUkFHX01PREVfQ1JPUCk7XG4gICAgfSxcbiAgICB3aGVlbDogZnVuY3Rpb24gd2hlZWwoZXZlbnQpIHtcbiAgICAgIHZhciBfdGhpcyA9IHRoaXM7XG4gICAgICB2YXIgcmF0aW8gPSBOdW1iZXIodGhpcy5vcHRpb25zLndoZWVsWm9vbVJhdGlvKSB8fCAwLjE7XG4gICAgICB2YXIgZGVsdGEgPSAxO1xuICAgICAgaWYgKHRoaXMuZGlzYWJsZWQpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgLy8gTGltaXQgd2hlZWwgc3BlZWQgdG8gcHJldmVudCB6b29tIHRvbyBmYXN0ICgjMjEpXG4gICAgICBpZiAodGhpcy53aGVlbGluZykge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICB0aGlzLndoZWVsaW5nID0gdHJ1ZTtcbiAgICAgIHNldFRpbWVvdXQoZnVuY3Rpb24gKCkge1xuICAgICAgICBfdGhpcy53aGVlbGluZyA9IGZhbHNlO1xuICAgICAgfSwgNTApO1xuICAgICAgaWYgKGV2ZW50LmRlbHRhWSkge1xuICAgICAgICBkZWx0YSA9IGV2ZW50LmRlbHRhWSA+IDAgPyAxIDogLTE7XG4gICAgICB9IGVsc2UgaWYgKGV2ZW50LndoZWVsRGVsdGEpIHtcbiAgICAgICAgZGVsdGEgPSAtZXZlbnQud2hlZWxEZWx0YSAvIDEyMDtcbiAgICAgIH0gZWxzZSBpZiAoZXZlbnQuZGV0YWlsKSB7XG4gICAgICAgIGRlbHRhID0gZXZlbnQuZGV0YWlsID4gMCA/IDEgOiAtMTtcbiAgICAgIH1cbiAgICAgIHRoaXMuem9vbSgtZGVsdGEgKiByYXRpbywgZXZlbnQpO1xuICAgIH0sXG4gICAgY3JvcFN0YXJ0OiBmdW5jdGlvbiBjcm9wU3RhcnQoZXZlbnQpIHtcbiAgICAgIHZhciBidXR0b25zID0gZXZlbnQuYnV0dG9ucyxcbiAgICAgICAgYnV0dG9uID0gZXZlbnQuYnV0dG9uO1xuICAgICAgaWYgKHRoaXMuZGlzYWJsZWRcblxuICAgICAgLy8gSGFuZGxlIG1vdXNlIGV2ZW50IGFuZCBwb2ludGVyIGV2ZW50IGFuZCBpZ25vcmUgdG91Y2ggZXZlbnRcbiAgICAgIHx8IChldmVudC50eXBlID09PSAnbW91c2Vkb3duJyB8fCBldmVudC50eXBlID09PSAncG9pbnRlcmRvd24nICYmIGV2ZW50LnBvaW50ZXJUeXBlID09PSAnbW91c2UnKSAmJiAoXG4gICAgICAvLyBObyBwcmltYXJ5IGJ1dHRvbiAoVXN1YWxseSB0aGUgbGVmdCBidXR0b24pXG4gICAgICBpc051bWJlcihidXR0b25zKSAmJiBidXR0b25zICE9PSAxIHx8IGlzTnVtYmVyKGJ1dHRvbikgJiYgYnV0dG9uICE9PSAwXG5cbiAgICAgIC8vIE9wZW4gY29udGV4dCBtZW51XG4gICAgICB8fCBldmVudC5jdHJsS2V5KSkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICB2YXIgb3B0aW9ucyA9IHRoaXMub3B0aW9ucyxcbiAgICAgICAgcG9pbnRlcnMgPSB0aGlzLnBvaW50ZXJzO1xuICAgICAgdmFyIGFjdGlvbjtcbiAgICAgIGlmIChldmVudC5jaGFuZ2VkVG91Y2hlcykge1xuICAgICAgICAvLyBIYW5kbGUgdG91Y2ggZXZlbnRcbiAgICAgICAgZm9yRWFjaChldmVudC5jaGFuZ2VkVG91Y2hlcywgZnVuY3Rpb24gKHRvdWNoKSB7XG4gICAgICAgICAgcG9pbnRlcnNbdG91Y2guaWRlbnRpZmllcl0gPSBnZXRQb2ludGVyKHRvdWNoKTtcbiAgICAgICAgfSk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICAvLyBIYW5kbGUgbW91c2UgZXZlbnQgYW5kIHBvaW50ZXIgZXZlbnRcbiAgICAgICAgcG9pbnRlcnNbZXZlbnQucG9pbnRlcklkIHx8IDBdID0gZ2V0UG9pbnRlcihldmVudCk7XG4gICAgICB9XG4gICAgICBpZiAoT2JqZWN0LmtleXMocG9pbnRlcnMpLmxlbmd0aCA+IDEgJiYgb3B0aW9ucy56b29tYWJsZSAmJiBvcHRpb25zLnpvb21PblRvdWNoKSB7XG4gICAgICAgIGFjdGlvbiA9IEFDVElPTl9aT09NO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgYWN0aW9uID0gZ2V0RGF0YShldmVudC50YXJnZXQsIERBVEFfQUNUSU9OKTtcbiAgICAgIH1cbiAgICAgIGlmICghUkVHRVhQX0FDVElPTlMudGVzdChhY3Rpb24pKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIGlmIChkaXNwYXRjaEV2ZW50KHRoaXMuZWxlbWVudCwgRVZFTlRfQ1JPUF9TVEFSVCwge1xuICAgICAgICBvcmlnaW5hbEV2ZW50OiBldmVudCxcbiAgICAgICAgYWN0aW9uOiBhY3Rpb25cbiAgICAgIH0pID09PSBmYWxzZSkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG5cbiAgICAgIC8vIFRoaXMgbGluZSBpcyByZXF1aXJlZCBmb3IgcHJldmVudGluZyBwYWdlIHpvb21pbmcgaW4gaU9TIGJyb3dzZXJzXG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgdGhpcy5hY3Rpb24gPSBhY3Rpb247XG4gICAgICB0aGlzLmNyb3BwaW5nID0gZmFsc2U7XG4gICAgICBpZiAoYWN0aW9uID09PSBBQ1RJT05fQ1JPUCkge1xuICAgICAgICB0aGlzLmNyb3BwaW5nID0gdHJ1ZTtcbiAgICAgICAgYWRkQ2xhc3ModGhpcy5kcmFnQm94LCBDTEFTU19NT0RBTCk7XG4gICAgICB9XG4gICAgfSxcbiAgICBjcm9wTW92ZTogZnVuY3Rpb24gY3JvcE1vdmUoZXZlbnQpIHtcbiAgICAgIHZhciBhY3Rpb24gPSB0aGlzLmFjdGlvbjtcbiAgICAgIGlmICh0aGlzLmRpc2FibGVkIHx8ICFhY3Rpb24pIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgICAgdmFyIHBvaW50ZXJzID0gdGhpcy5wb2ludGVycztcbiAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICBpZiAoZGlzcGF0Y2hFdmVudCh0aGlzLmVsZW1lbnQsIEVWRU5UX0NST1BfTU9WRSwge1xuICAgICAgICBvcmlnaW5hbEV2ZW50OiBldmVudCxcbiAgICAgICAgYWN0aW9uOiBhY3Rpb25cbiAgICAgIH0pID09PSBmYWxzZSkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICBpZiAoZXZlbnQuY2hhbmdlZFRvdWNoZXMpIHtcbiAgICAgICAgZm9yRWFjaChldmVudC5jaGFuZ2VkVG91Y2hlcywgZnVuY3Rpb24gKHRvdWNoKSB7XG4gICAgICAgICAgLy8gVGhlIGZpcnN0IHBhcmFtZXRlciBzaG91bGQgbm90IGJlIHVuZGVmaW5lZCAoIzQzMilcbiAgICAgICAgICBhc3NpZ24ocG9pbnRlcnNbdG91Y2guaWRlbnRpZmllcl0gfHwge30sIGdldFBvaW50ZXIodG91Y2gsIHRydWUpKTtcbiAgICAgICAgfSk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBhc3NpZ24ocG9pbnRlcnNbZXZlbnQucG9pbnRlcklkIHx8IDBdIHx8IHt9LCBnZXRQb2ludGVyKGV2ZW50LCB0cnVlKSk7XG4gICAgICB9XG4gICAgICB0aGlzLmNoYW5nZShldmVudCk7XG4gICAgfSxcbiAgICBjcm9wRW5kOiBmdW5jdGlvbiBjcm9wRW5kKGV2ZW50KSB7XG4gICAgICBpZiAodGhpcy5kaXNhYmxlZCkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICB2YXIgYWN0aW9uID0gdGhpcy5hY3Rpb24sXG4gICAgICAgIHBvaW50ZXJzID0gdGhpcy5wb2ludGVycztcbiAgICAgIGlmIChldmVudC5jaGFuZ2VkVG91Y2hlcykge1xuICAgICAgICBmb3JFYWNoKGV2ZW50LmNoYW5nZWRUb3VjaGVzLCBmdW5jdGlvbiAodG91Y2gpIHtcbiAgICAgICAgICBkZWxldGUgcG9pbnRlcnNbdG91Y2guaWRlbnRpZmllcl07XG4gICAgICAgIH0pO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgZGVsZXRlIHBvaW50ZXJzW2V2ZW50LnBvaW50ZXJJZCB8fCAwXTtcbiAgICAgIH1cbiAgICAgIGlmICghYWN0aW9uKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICBpZiAoIU9iamVjdC5rZXlzKHBvaW50ZXJzKS5sZW5ndGgpIHtcbiAgICAgICAgdGhpcy5hY3Rpb24gPSAnJztcbiAgICAgIH1cbiAgICAgIGlmICh0aGlzLmNyb3BwaW5nKSB7XG4gICAgICAgIHRoaXMuY3JvcHBpbmcgPSBmYWxzZTtcbiAgICAgICAgdG9nZ2xlQ2xhc3ModGhpcy5kcmFnQm94LCBDTEFTU19NT0RBTCwgdGhpcy5jcm9wcGVkICYmIHRoaXMub3B0aW9ucy5tb2RhbCk7XG4gICAgICB9XG4gICAgICBkaXNwYXRjaEV2ZW50KHRoaXMuZWxlbWVudCwgRVZFTlRfQ1JPUF9FTkQsIHtcbiAgICAgICAgb3JpZ2luYWxFdmVudDogZXZlbnQsXG4gICAgICAgIGFjdGlvbjogYWN0aW9uXG4gICAgICB9KTtcbiAgICB9XG4gIH07XG5cbiAgdmFyIGNoYW5nZSA9IHtcbiAgICBjaGFuZ2U6IGZ1bmN0aW9uIGNoYW5nZShldmVudCkge1xuICAgICAgdmFyIG9wdGlvbnMgPSB0aGlzLm9wdGlvbnMsXG4gICAgICAgIGNhbnZhc0RhdGEgPSB0aGlzLmNhbnZhc0RhdGEsXG4gICAgICAgIGNvbnRhaW5lckRhdGEgPSB0aGlzLmNvbnRhaW5lckRhdGEsXG4gICAgICAgIGNyb3BCb3hEYXRhID0gdGhpcy5jcm9wQm94RGF0YSxcbiAgICAgICAgcG9pbnRlcnMgPSB0aGlzLnBvaW50ZXJzO1xuICAgICAgdmFyIGFjdGlvbiA9IHRoaXMuYWN0aW9uO1xuICAgICAgdmFyIGFzcGVjdFJhdGlvID0gb3B0aW9ucy5hc3BlY3RSYXRpbztcbiAgICAgIHZhciBsZWZ0ID0gY3JvcEJveERhdGEubGVmdCxcbiAgICAgICAgdG9wID0gY3JvcEJveERhdGEudG9wLFxuICAgICAgICB3aWR0aCA9IGNyb3BCb3hEYXRhLndpZHRoLFxuICAgICAgICBoZWlnaHQgPSBjcm9wQm94RGF0YS5oZWlnaHQ7XG4gICAgICB2YXIgcmlnaHQgPSBsZWZ0ICsgd2lkdGg7XG4gICAgICB2YXIgYm90dG9tID0gdG9wICsgaGVpZ2h0O1xuICAgICAgdmFyIG1pbkxlZnQgPSAwO1xuICAgICAgdmFyIG1pblRvcCA9IDA7XG4gICAgICB2YXIgbWF4V2lkdGggPSBjb250YWluZXJEYXRhLndpZHRoO1xuICAgICAgdmFyIG1heEhlaWdodCA9IGNvbnRhaW5lckRhdGEuaGVpZ2h0O1xuICAgICAgdmFyIHJlbmRlcmFibGUgPSB0cnVlO1xuICAgICAgdmFyIG9mZnNldDtcblxuICAgICAgLy8gTG9ja2luZyBhc3BlY3QgcmF0aW8gaW4gXCJmcmVlIG1vZGVcIiBieSBob2xkaW5nIHNoaWZ0IGtleVxuICAgICAgaWYgKCFhc3BlY3RSYXRpbyAmJiBldmVudC5zaGlmdEtleSkge1xuICAgICAgICBhc3BlY3RSYXRpbyA9IHdpZHRoICYmIGhlaWdodCA/IHdpZHRoIC8gaGVpZ2h0IDogMTtcbiAgICAgIH1cbiAgICAgIGlmICh0aGlzLmxpbWl0ZWQpIHtcbiAgICAgICAgbWluTGVmdCA9IGNyb3BCb3hEYXRhLm1pbkxlZnQ7XG4gICAgICAgIG1pblRvcCA9IGNyb3BCb3hEYXRhLm1pblRvcDtcbiAgICAgICAgbWF4V2lkdGggPSBtaW5MZWZ0ICsgTWF0aC5taW4oY29udGFpbmVyRGF0YS53aWR0aCwgY2FudmFzRGF0YS53aWR0aCwgY2FudmFzRGF0YS5sZWZ0ICsgY2FudmFzRGF0YS53aWR0aCk7XG4gICAgICAgIG1heEhlaWdodCA9IG1pblRvcCArIE1hdGgubWluKGNvbnRhaW5lckRhdGEuaGVpZ2h0LCBjYW52YXNEYXRhLmhlaWdodCwgY2FudmFzRGF0YS50b3AgKyBjYW52YXNEYXRhLmhlaWdodCk7XG4gICAgICB9XG4gICAgICB2YXIgcG9pbnRlciA9IHBvaW50ZXJzW09iamVjdC5rZXlzKHBvaW50ZXJzKVswXV07XG4gICAgICB2YXIgcmFuZ2UgPSB7XG4gICAgICAgIHg6IHBvaW50ZXIuZW5kWCAtIHBvaW50ZXIuc3RhcnRYLFxuICAgICAgICB5OiBwb2ludGVyLmVuZFkgLSBwb2ludGVyLnN0YXJ0WVxuICAgICAgfTtcbiAgICAgIHZhciBjaGVjayA9IGZ1bmN0aW9uIGNoZWNrKHNpZGUpIHtcbiAgICAgICAgc3dpdGNoIChzaWRlKSB7XG4gICAgICAgICAgY2FzZSBBQ1RJT05fRUFTVDpcbiAgICAgICAgICAgIGlmIChyaWdodCArIHJhbmdlLnggPiBtYXhXaWR0aCkge1xuICAgICAgICAgICAgICByYW5nZS54ID0gbWF4V2lkdGggLSByaWdodDtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgIGNhc2UgQUNUSU9OX1dFU1Q6XG4gICAgICAgICAgICBpZiAobGVmdCArIHJhbmdlLnggPCBtaW5MZWZ0KSB7XG4gICAgICAgICAgICAgIHJhbmdlLnggPSBtaW5MZWZ0IC0gbGVmdDtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgIGNhc2UgQUNUSU9OX05PUlRIOlxuICAgICAgICAgICAgaWYgKHRvcCArIHJhbmdlLnkgPCBtaW5Ub3ApIHtcbiAgICAgICAgICAgICAgcmFuZ2UueSA9IG1pblRvcCAtIHRvcDtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgIGNhc2UgQUNUSU9OX1NPVVRIOlxuICAgICAgICAgICAgaWYgKGJvdHRvbSArIHJhbmdlLnkgPiBtYXhIZWlnaHQpIHtcbiAgICAgICAgICAgICAgcmFuZ2UueSA9IG1heEhlaWdodCAtIGJvdHRvbTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICB9XG4gICAgICB9O1xuICAgICAgc3dpdGNoIChhY3Rpb24pIHtcbiAgICAgICAgLy8gTW92ZSBjcm9wIGJveFxuICAgICAgICBjYXNlIEFDVElPTl9BTEw6XG4gICAgICAgICAgbGVmdCArPSByYW5nZS54O1xuICAgICAgICAgIHRvcCArPSByYW5nZS55O1xuICAgICAgICAgIGJyZWFrO1xuXG4gICAgICAgIC8vIFJlc2l6ZSBjcm9wIGJveFxuICAgICAgICBjYXNlIEFDVElPTl9FQVNUOlxuICAgICAgICAgIGlmIChyYW5nZS54ID49IDAgJiYgKHJpZ2h0ID49IG1heFdpZHRoIHx8IGFzcGVjdFJhdGlvICYmICh0b3AgPD0gbWluVG9wIHx8IGJvdHRvbSA+PSBtYXhIZWlnaHQpKSkge1xuICAgICAgICAgICAgcmVuZGVyYWJsZSA9IGZhbHNlO1xuICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgfVxuICAgICAgICAgIGNoZWNrKEFDVElPTl9FQVNUKTtcbiAgICAgICAgICB3aWR0aCArPSByYW5nZS54O1xuICAgICAgICAgIGlmICh3aWR0aCA8IDApIHtcbiAgICAgICAgICAgIGFjdGlvbiA9IEFDVElPTl9XRVNUO1xuICAgICAgICAgICAgd2lkdGggPSAtd2lkdGg7XG4gICAgICAgICAgICBsZWZ0IC09IHdpZHRoO1xuICAgICAgICAgIH1cbiAgICAgICAgICBpZiAoYXNwZWN0UmF0aW8pIHtcbiAgICAgICAgICAgIGhlaWdodCA9IHdpZHRoIC8gYXNwZWN0UmF0aW87XG4gICAgICAgICAgICB0b3AgKz0gKGNyb3BCb3hEYXRhLmhlaWdodCAtIGhlaWdodCkgLyAyO1xuICAgICAgICAgIH1cbiAgICAgICAgICBicmVhaztcbiAgICAgICAgY2FzZSBBQ1RJT05fTk9SVEg6XG4gICAgICAgICAgaWYgKHJhbmdlLnkgPD0gMCAmJiAodG9wIDw9IG1pblRvcCB8fCBhc3BlY3RSYXRpbyAmJiAobGVmdCA8PSBtaW5MZWZ0IHx8IHJpZ2h0ID49IG1heFdpZHRoKSkpIHtcbiAgICAgICAgICAgIHJlbmRlcmFibGUgPSBmYWxzZTtcbiAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgIH1cbiAgICAgICAgICBjaGVjayhBQ1RJT05fTk9SVEgpO1xuICAgICAgICAgIGhlaWdodCAtPSByYW5nZS55O1xuICAgICAgICAgIHRvcCArPSByYW5nZS55O1xuICAgICAgICAgIGlmIChoZWlnaHQgPCAwKSB7XG4gICAgICAgICAgICBhY3Rpb24gPSBBQ1RJT05fU09VVEg7XG4gICAgICAgICAgICBoZWlnaHQgPSAtaGVpZ2h0O1xuICAgICAgICAgICAgdG9wIC09IGhlaWdodDtcbiAgICAgICAgICB9XG4gICAgICAgICAgaWYgKGFzcGVjdFJhdGlvKSB7XG4gICAgICAgICAgICB3aWR0aCA9IGhlaWdodCAqIGFzcGVjdFJhdGlvO1xuICAgICAgICAgICAgbGVmdCArPSAoY3JvcEJveERhdGEud2lkdGggLSB3aWR0aCkgLyAyO1xuICAgICAgICAgIH1cbiAgICAgICAgICBicmVhaztcbiAgICAgICAgY2FzZSBBQ1RJT05fV0VTVDpcbiAgICAgICAgICBpZiAocmFuZ2UueCA8PSAwICYmIChsZWZ0IDw9IG1pbkxlZnQgfHwgYXNwZWN0UmF0aW8gJiYgKHRvcCA8PSBtaW5Ub3AgfHwgYm90dG9tID49IG1heEhlaWdodCkpKSB7XG4gICAgICAgICAgICByZW5kZXJhYmxlID0gZmFsc2U7XG4gICAgICAgICAgICBicmVhaztcbiAgICAgICAgICB9XG4gICAgICAgICAgY2hlY2soQUNUSU9OX1dFU1QpO1xuICAgICAgICAgIHdpZHRoIC09IHJhbmdlLng7XG4gICAgICAgICAgbGVmdCArPSByYW5nZS54O1xuICAgICAgICAgIGlmICh3aWR0aCA8IDApIHtcbiAgICAgICAgICAgIGFjdGlvbiA9IEFDVElPTl9FQVNUO1xuICAgICAgICAgICAgd2lkdGggPSAtd2lkdGg7XG4gICAgICAgICAgICBsZWZ0IC09IHdpZHRoO1xuICAgICAgICAgIH1cbiAgICAgICAgICBpZiAoYXNwZWN0UmF0aW8pIHtcbiAgICAgICAgICAgIGhlaWdodCA9IHdpZHRoIC8gYXNwZWN0UmF0aW87XG4gICAgICAgICAgICB0b3AgKz0gKGNyb3BCb3hEYXRhLmhlaWdodCAtIGhlaWdodCkgLyAyO1xuICAgICAgICAgIH1cbiAgICAgICAgICBicmVhaztcbiAgICAgICAgY2FzZSBBQ1RJT05fU09VVEg6XG4gICAgICAgICAgaWYgKHJhbmdlLnkgPj0gMCAmJiAoYm90dG9tID49IG1heEhlaWdodCB8fCBhc3BlY3RSYXRpbyAmJiAobGVmdCA8PSBtaW5MZWZ0IHx8IHJpZ2h0ID49IG1heFdpZHRoKSkpIHtcbiAgICAgICAgICAgIHJlbmRlcmFibGUgPSBmYWxzZTtcbiAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgIH1cbiAgICAgICAgICBjaGVjayhBQ1RJT05fU09VVEgpO1xuICAgICAgICAgIGhlaWdodCArPSByYW5nZS55O1xuICAgICAgICAgIGlmIChoZWlnaHQgPCAwKSB7XG4gICAgICAgICAgICBhY3Rpb24gPSBBQ1RJT05fTk9SVEg7XG4gICAgICAgICAgICBoZWlnaHQgPSAtaGVpZ2h0O1xuICAgICAgICAgICAgdG9wIC09IGhlaWdodDtcbiAgICAgICAgICB9XG4gICAgICAgICAgaWYgKGFzcGVjdFJhdGlvKSB7XG4gICAgICAgICAgICB3aWR0aCA9IGhlaWdodCAqIGFzcGVjdFJhdGlvO1xuICAgICAgICAgICAgbGVmdCArPSAoY3JvcEJveERhdGEud2lkdGggLSB3aWR0aCkgLyAyO1xuICAgICAgICAgIH1cbiAgICAgICAgICBicmVhaztcbiAgICAgICAgY2FzZSBBQ1RJT05fTk9SVEhfRUFTVDpcbiAgICAgICAgICBpZiAoYXNwZWN0UmF0aW8pIHtcbiAgICAgICAgICAgIGlmIChyYW5nZS55IDw9IDAgJiYgKHRvcCA8PSBtaW5Ub3AgfHwgcmlnaHQgPj0gbWF4V2lkdGgpKSB7XG4gICAgICAgICAgICAgIHJlbmRlcmFibGUgPSBmYWxzZTtcbiAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBjaGVjayhBQ1RJT05fTk9SVEgpO1xuICAgICAgICAgICAgaGVpZ2h0IC09IHJhbmdlLnk7XG4gICAgICAgICAgICB0b3AgKz0gcmFuZ2UueTtcbiAgICAgICAgICAgIHdpZHRoID0gaGVpZ2h0ICogYXNwZWN0UmF0aW87XG4gICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIGNoZWNrKEFDVElPTl9OT1JUSCk7XG4gICAgICAgICAgICBjaGVjayhBQ1RJT05fRUFTVCk7XG4gICAgICAgICAgICBpZiAocmFuZ2UueCA+PSAwKSB7XG4gICAgICAgICAgICAgIGlmIChyaWdodCA8IG1heFdpZHRoKSB7XG4gICAgICAgICAgICAgICAgd2lkdGggKz0gcmFuZ2UueDtcbiAgICAgICAgICAgICAgfSBlbHNlIGlmIChyYW5nZS55IDw9IDAgJiYgdG9wIDw9IG1pblRvcCkge1xuICAgICAgICAgICAgICAgIHJlbmRlcmFibGUgPSBmYWxzZTtcbiAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgd2lkdGggKz0gcmFuZ2UueDtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGlmIChyYW5nZS55IDw9IDApIHtcbiAgICAgICAgICAgICAgaWYgKHRvcCA+IG1pblRvcCkge1xuICAgICAgICAgICAgICAgIGhlaWdodCAtPSByYW5nZS55O1xuICAgICAgICAgICAgICAgIHRvcCArPSByYW5nZS55O1xuICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICBoZWlnaHQgLT0gcmFuZ2UueTtcbiAgICAgICAgICAgICAgdG9wICs9IHJhbmdlLnk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgfVxuICAgICAgICAgIGlmICh3aWR0aCA8IDAgJiYgaGVpZ2h0IDwgMCkge1xuICAgICAgICAgICAgYWN0aW9uID0gQUNUSU9OX1NPVVRIX1dFU1Q7XG4gICAgICAgICAgICBoZWlnaHQgPSAtaGVpZ2h0O1xuICAgICAgICAgICAgd2lkdGggPSAtd2lkdGg7XG4gICAgICAgICAgICB0b3AgLT0gaGVpZ2h0O1xuICAgICAgICAgICAgbGVmdCAtPSB3aWR0aDtcbiAgICAgICAgICB9IGVsc2UgaWYgKHdpZHRoIDwgMCkge1xuICAgICAgICAgICAgYWN0aW9uID0gQUNUSU9OX05PUlRIX1dFU1Q7XG4gICAgICAgICAgICB3aWR0aCA9IC13aWR0aDtcbiAgICAgICAgICAgIGxlZnQgLT0gd2lkdGg7XG4gICAgICAgICAgfSBlbHNlIGlmIChoZWlnaHQgPCAwKSB7XG4gICAgICAgICAgICBhY3Rpb24gPSBBQ1RJT05fU09VVEhfRUFTVDtcbiAgICAgICAgICAgIGhlaWdodCA9IC1oZWlnaHQ7XG4gICAgICAgICAgICB0b3AgLT0gaGVpZ2h0O1xuICAgICAgICAgIH1cbiAgICAgICAgICBicmVhaztcbiAgICAgICAgY2FzZSBBQ1RJT05fTk9SVEhfV0VTVDpcbiAgICAgICAgICBpZiAoYXNwZWN0UmF0aW8pIHtcbiAgICAgICAgICAgIGlmIChyYW5nZS55IDw9IDAgJiYgKHRvcCA8PSBtaW5Ub3AgfHwgbGVmdCA8PSBtaW5MZWZ0KSkge1xuICAgICAgICAgICAgICByZW5kZXJhYmxlID0gZmFsc2U7XG4gICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgY2hlY2soQUNUSU9OX05PUlRIKTtcbiAgICAgICAgICAgIGhlaWdodCAtPSByYW5nZS55O1xuICAgICAgICAgICAgdG9wICs9IHJhbmdlLnk7XG4gICAgICAgICAgICB3aWR0aCA9IGhlaWdodCAqIGFzcGVjdFJhdGlvO1xuICAgICAgICAgICAgbGVmdCArPSBjcm9wQm94RGF0YS53aWR0aCAtIHdpZHRoO1xuICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICBjaGVjayhBQ1RJT05fTk9SVEgpO1xuICAgICAgICAgICAgY2hlY2soQUNUSU9OX1dFU1QpO1xuICAgICAgICAgICAgaWYgKHJhbmdlLnggPD0gMCkge1xuICAgICAgICAgICAgICBpZiAobGVmdCA+IG1pbkxlZnQpIHtcbiAgICAgICAgICAgICAgICB3aWR0aCAtPSByYW5nZS54O1xuICAgICAgICAgICAgICAgIGxlZnQgKz0gcmFuZ2UueDtcbiAgICAgICAgICAgICAgfSBlbHNlIGlmIChyYW5nZS55IDw9IDAgJiYgdG9wIDw9IG1pblRvcCkge1xuICAgICAgICAgICAgICAgIHJlbmRlcmFibGUgPSBmYWxzZTtcbiAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgd2lkdGggLT0gcmFuZ2UueDtcbiAgICAgICAgICAgICAgbGVmdCArPSByYW5nZS54O1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgaWYgKHJhbmdlLnkgPD0gMCkge1xuICAgICAgICAgICAgICBpZiAodG9wID4gbWluVG9wKSB7XG4gICAgICAgICAgICAgICAgaGVpZ2h0IC09IHJhbmdlLnk7XG4gICAgICAgICAgICAgICAgdG9wICs9IHJhbmdlLnk7XG4gICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgIGhlaWdodCAtPSByYW5nZS55O1xuICAgICAgICAgICAgICB0b3AgKz0gcmFuZ2UueTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICB9XG4gICAgICAgICAgaWYgKHdpZHRoIDwgMCAmJiBoZWlnaHQgPCAwKSB7XG4gICAgICAgICAgICBhY3Rpb24gPSBBQ1RJT05fU09VVEhfRUFTVDtcbiAgICAgICAgICAgIGhlaWdodCA9IC1oZWlnaHQ7XG4gICAgICAgICAgICB3aWR0aCA9IC13aWR0aDtcbiAgICAgICAgICAgIHRvcCAtPSBoZWlnaHQ7XG4gICAgICAgICAgICBsZWZ0IC09IHdpZHRoO1xuICAgICAgICAgIH0gZWxzZSBpZiAod2lkdGggPCAwKSB7XG4gICAgICAgICAgICBhY3Rpb24gPSBBQ1RJT05fTk9SVEhfRUFTVDtcbiAgICAgICAgICAgIHdpZHRoID0gLXdpZHRoO1xuICAgICAgICAgICAgbGVmdCAtPSB3aWR0aDtcbiAgICAgICAgICB9IGVsc2UgaWYgKGhlaWdodCA8IDApIHtcbiAgICAgICAgICAgIGFjdGlvbiA9IEFDVElPTl9TT1VUSF9XRVNUO1xuICAgICAgICAgICAgaGVpZ2h0ID0gLWhlaWdodDtcbiAgICAgICAgICAgIHRvcCAtPSBoZWlnaHQ7XG4gICAgICAgICAgfVxuICAgICAgICAgIGJyZWFrO1xuICAgICAgICBjYXNlIEFDVElPTl9TT1VUSF9XRVNUOlxuICAgICAgICAgIGlmIChhc3BlY3RSYXRpbykge1xuICAgICAgICAgICAgaWYgKHJhbmdlLnggPD0gMCAmJiAobGVmdCA8PSBtaW5MZWZ0IHx8IGJvdHRvbSA+PSBtYXhIZWlnaHQpKSB7XG4gICAgICAgICAgICAgIHJlbmRlcmFibGUgPSBmYWxzZTtcbiAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBjaGVjayhBQ1RJT05fV0VTVCk7XG4gICAgICAgICAgICB3aWR0aCAtPSByYW5nZS54O1xuICAgICAgICAgICAgbGVmdCArPSByYW5nZS54O1xuICAgICAgICAgICAgaGVpZ2h0ID0gd2lkdGggLyBhc3BlY3RSYXRpbztcbiAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgY2hlY2soQUNUSU9OX1NPVVRIKTtcbiAgICAgICAgICAgIGNoZWNrKEFDVElPTl9XRVNUKTtcbiAgICAgICAgICAgIGlmIChyYW5nZS54IDw9IDApIHtcbiAgICAgICAgICAgICAgaWYgKGxlZnQgPiBtaW5MZWZ0KSB7XG4gICAgICAgICAgICAgICAgd2lkdGggLT0gcmFuZ2UueDtcbiAgICAgICAgICAgICAgICBsZWZ0ICs9IHJhbmdlLng7XG4gICAgICAgICAgICAgIH0gZWxzZSBpZiAocmFuZ2UueSA+PSAwICYmIGJvdHRvbSA+PSBtYXhIZWlnaHQpIHtcbiAgICAgICAgICAgICAgICByZW5kZXJhYmxlID0gZmFsc2U7XG4gICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgIHdpZHRoIC09IHJhbmdlLng7XG4gICAgICAgICAgICAgIGxlZnQgKz0gcmFuZ2UueDtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGlmIChyYW5nZS55ID49IDApIHtcbiAgICAgICAgICAgICAgaWYgKGJvdHRvbSA8IG1heEhlaWdodCkge1xuICAgICAgICAgICAgICAgIGhlaWdodCArPSByYW5nZS55O1xuICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICBoZWlnaHQgKz0gcmFuZ2UueTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICB9XG4gICAgICAgICAgaWYgKHdpZHRoIDwgMCAmJiBoZWlnaHQgPCAwKSB7XG4gICAgICAgICAgICBhY3Rpb24gPSBBQ1RJT05fTk9SVEhfRUFTVDtcbiAgICAgICAgICAgIGhlaWdodCA9IC1oZWlnaHQ7XG4gICAgICAgICAgICB3aWR0aCA9IC13aWR0aDtcbiAgICAgICAgICAgIHRvcCAtPSBoZWlnaHQ7XG4gICAgICAgICAgICBsZWZ0IC09IHdpZHRoO1xuICAgICAgICAgIH0gZWxzZSBpZiAod2lkdGggPCAwKSB7XG4gICAgICAgICAgICBhY3Rpb24gPSBBQ1RJT05fU09VVEhfRUFTVDtcbiAgICAgICAgICAgIHdpZHRoID0gLXdpZHRoO1xuICAgICAgICAgICAgbGVmdCAtPSB3aWR0aDtcbiAgICAgICAgICB9IGVsc2UgaWYgKGhlaWdodCA8IDApIHtcbiAgICAgICAgICAgIGFjdGlvbiA9IEFDVElPTl9OT1JUSF9XRVNUO1xuICAgICAgICAgICAgaGVpZ2h0ID0gLWhlaWdodDtcbiAgICAgICAgICAgIHRvcCAtPSBoZWlnaHQ7XG4gICAgICAgICAgfVxuICAgICAgICAgIGJyZWFrO1xuICAgICAgICBjYXNlIEFDVElPTl9TT1VUSF9FQVNUOlxuICAgICAgICAgIGlmIChhc3BlY3RSYXRpbykge1xuICAgICAgICAgICAgaWYgKHJhbmdlLnggPj0gMCAmJiAocmlnaHQgPj0gbWF4V2lkdGggfHwgYm90dG9tID49IG1heEhlaWdodCkpIHtcbiAgICAgICAgICAgICAgcmVuZGVyYWJsZSA9IGZhbHNlO1xuICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGNoZWNrKEFDVElPTl9FQVNUKTtcbiAgICAgICAgICAgIHdpZHRoICs9IHJhbmdlLng7XG4gICAgICAgICAgICBoZWlnaHQgPSB3aWR0aCAvIGFzcGVjdFJhdGlvO1xuICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICBjaGVjayhBQ1RJT05fU09VVEgpO1xuICAgICAgICAgICAgY2hlY2soQUNUSU9OX0VBU1QpO1xuICAgICAgICAgICAgaWYgKHJhbmdlLnggPj0gMCkge1xuICAgICAgICAgICAgICBpZiAocmlnaHQgPCBtYXhXaWR0aCkge1xuICAgICAgICAgICAgICAgIHdpZHRoICs9IHJhbmdlLng7XG4gICAgICAgICAgICAgIH0gZWxzZSBpZiAocmFuZ2UueSA+PSAwICYmIGJvdHRvbSA+PSBtYXhIZWlnaHQpIHtcbiAgICAgICAgICAgICAgICByZW5kZXJhYmxlID0gZmFsc2U7XG4gICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgIHdpZHRoICs9IHJhbmdlLng7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBpZiAocmFuZ2UueSA+PSAwKSB7XG4gICAgICAgICAgICAgIGlmIChib3R0b20gPCBtYXhIZWlnaHQpIHtcbiAgICAgICAgICAgICAgICBoZWlnaHQgKz0gcmFuZ2UueTtcbiAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgaGVpZ2h0ICs9IHJhbmdlLnk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgfVxuICAgICAgICAgIGlmICh3aWR0aCA8IDAgJiYgaGVpZ2h0IDwgMCkge1xuICAgICAgICAgICAgYWN0aW9uID0gQUNUSU9OX05PUlRIX1dFU1Q7XG4gICAgICAgICAgICBoZWlnaHQgPSAtaGVpZ2h0O1xuICAgICAgICAgICAgd2lkdGggPSAtd2lkdGg7XG4gICAgICAgICAgICB0b3AgLT0gaGVpZ2h0O1xuICAgICAgICAgICAgbGVmdCAtPSB3aWR0aDtcbiAgICAgICAgICB9IGVsc2UgaWYgKHdpZHRoIDwgMCkge1xuICAgICAgICAgICAgYWN0aW9uID0gQUNUSU9OX1NPVVRIX1dFU1Q7XG4gICAgICAgICAgICB3aWR0aCA9IC13aWR0aDtcbiAgICAgICAgICAgIGxlZnQgLT0gd2lkdGg7XG4gICAgICAgICAgfSBlbHNlIGlmIChoZWlnaHQgPCAwKSB7XG4gICAgICAgICAgICBhY3Rpb24gPSBBQ1RJT05fTk9SVEhfRUFTVDtcbiAgICAgICAgICAgIGhlaWdodCA9IC1oZWlnaHQ7XG4gICAgICAgICAgICB0b3AgLT0gaGVpZ2h0O1xuICAgICAgICAgIH1cbiAgICAgICAgICBicmVhaztcblxuICAgICAgICAvLyBNb3ZlIGNhbnZhc1xuICAgICAgICBjYXNlIEFDVElPTl9NT1ZFOlxuICAgICAgICAgIHRoaXMubW92ZShyYW5nZS54LCByYW5nZS55KTtcbiAgICAgICAgICByZW5kZXJhYmxlID0gZmFsc2U7XG4gICAgICAgICAgYnJlYWs7XG5cbiAgICAgICAgLy8gWm9vbSBjYW52YXNcbiAgICAgICAgY2FzZSBBQ1RJT05fWk9PTTpcbiAgICAgICAgICB0aGlzLnpvb20oZ2V0TWF4Wm9vbVJhdGlvKHBvaW50ZXJzKSwgZXZlbnQpO1xuICAgICAgICAgIHJlbmRlcmFibGUgPSBmYWxzZTtcbiAgICAgICAgICBicmVhaztcblxuICAgICAgICAvLyBDcmVhdGUgY3JvcCBib3hcbiAgICAgICAgY2FzZSBBQ1RJT05fQ1JPUDpcbiAgICAgICAgICBpZiAoIXJhbmdlLnggfHwgIXJhbmdlLnkpIHtcbiAgICAgICAgICAgIHJlbmRlcmFibGUgPSBmYWxzZTtcbiAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgIH1cbiAgICAgICAgICBvZmZzZXQgPSBnZXRPZmZzZXQodGhpcy5jcm9wcGVyKTtcbiAgICAgICAgICBsZWZ0ID0gcG9pbnRlci5zdGFydFggLSBvZmZzZXQubGVmdDtcbiAgICAgICAgICB0b3AgPSBwb2ludGVyLnN0YXJ0WSAtIG9mZnNldC50b3A7XG4gICAgICAgICAgd2lkdGggPSBjcm9wQm94RGF0YS5taW5XaWR0aDtcbiAgICAgICAgICBoZWlnaHQgPSBjcm9wQm94RGF0YS5taW5IZWlnaHQ7XG4gICAgICAgICAgaWYgKHJhbmdlLnggPiAwKSB7XG4gICAgICAgICAgICBhY3Rpb24gPSByYW5nZS55ID4gMCA/IEFDVElPTl9TT1VUSF9FQVNUIDogQUNUSU9OX05PUlRIX0VBU1Q7XG4gICAgICAgICAgfSBlbHNlIGlmIChyYW5nZS54IDwgMCkge1xuICAgICAgICAgICAgbGVmdCAtPSB3aWR0aDtcbiAgICAgICAgICAgIGFjdGlvbiA9IHJhbmdlLnkgPiAwID8gQUNUSU9OX1NPVVRIX1dFU1QgOiBBQ1RJT05fTk9SVEhfV0VTVDtcbiAgICAgICAgICB9XG4gICAgICAgICAgaWYgKHJhbmdlLnkgPCAwKSB7XG4gICAgICAgICAgICB0b3AgLT0gaGVpZ2h0O1xuICAgICAgICAgIH1cblxuICAgICAgICAgIC8vIFNob3cgdGhlIGNyb3AgYm94IGlmIGlzIGhpZGRlblxuICAgICAgICAgIGlmICghdGhpcy5jcm9wcGVkKSB7XG4gICAgICAgICAgICByZW1vdmVDbGFzcyh0aGlzLmNyb3BCb3gsIENMQVNTX0hJRERFTik7XG4gICAgICAgICAgICB0aGlzLmNyb3BwZWQgPSB0cnVlO1xuICAgICAgICAgICAgaWYgKHRoaXMubGltaXRlZCkge1xuICAgICAgICAgICAgICB0aGlzLmxpbWl0Q3JvcEJveCh0cnVlLCB0cnVlKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICB9XG4gICAgICAgICAgYnJlYWs7XG4gICAgICB9XG4gICAgICBpZiAocmVuZGVyYWJsZSkge1xuICAgICAgICBjcm9wQm94RGF0YS53aWR0aCA9IHdpZHRoO1xuICAgICAgICBjcm9wQm94RGF0YS5oZWlnaHQgPSBoZWlnaHQ7XG4gICAgICAgIGNyb3BCb3hEYXRhLmxlZnQgPSBsZWZ0O1xuICAgICAgICBjcm9wQm94RGF0YS50b3AgPSB0b3A7XG4gICAgICAgIHRoaXMuYWN0aW9uID0gYWN0aW9uO1xuICAgICAgICB0aGlzLnJlbmRlckNyb3BCb3goKTtcbiAgICAgIH1cblxuICAgICAgLy8gT3ZlcnJpZGVcbiAgICAgIGZvckVhY2gocG9pbnRlcnMsIGZ1bmN0aW9uIChwKSB7XG4gICAgICAgIHAuc3RhcnRYID0gcC5lbmRYO1xuICAgICAgICBwLnN0YXJ0WSA9IHAuZW5kWTtcbiAgICAgIH0pO1xuICAgIH1cbiAgfTtcblxuICB2YXIgbWV0aG9kcyA9IHtcbiAgICAvLyBTaG93IHRoZSBjcm9wIGJveCBtYW51YWxseVxuICAgIGNyb3A6IGZ1bmN0aW9uIGNyb3AoKSB7XG4gICAgICBpZiAodGhpcy5yZWFkeSAmJiAhdGhpcy5jcm9wcGVkICYmICF0aGlzLmRpc2FibGVkKSB7XG4gICAgICAgIHRoaXMuY3JvcHBlZCA9IHRydWU7XG4gICAgICAgIHRoaXMubGltaXRDcm9wQm94KHRydWUsIHRydWUpO1xuICAgICAgICBpZiAodGhpcy5vcHRpb25zLm1vZGFsKSB7XG4gICAgICAgICAgYWRkQ2xhc3ModGhpcy5kcmFnQm94LCBDTEFTU19NT0RBTCk7XG4gICAgICAgIH1cbiAgICAgICAgcmVtb3ZlQ2xhc3ModGhpcy5jcm9wQm94LCBDTEFTU19ISURERU4pO1xuICAgICAgICB0aGlzLnNldENyb3BCb3hEYXRhKHRoaXMuaW5pdGlhbENyb3BCb3hEYXRhKTtcbiAgICAgIH1cbiAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG4gICAgLy8gUmVzZXQgdGhlIGltYWdlIGFuZCBjcm9wIGJveCB0byB0aGVpciBpbml0aWFsIHN0YXRlc1xuICAgIHJlc2V0OiBmdW5jdGlvbiByZXNldCgpIHtcbiAgICAgIGlmICh0aGlzLnJlYWR5ICYmICF0aGlzLmRpc2FibGVkKSB7XG4gICAgICAgIHRoaXMuaW1hZ2VEYXRhID0gYXNzaWduKHt9LCB0aGlzLmluaXRpYWxJbWFnZURhdGEpO1xuICAgICAgICB0aGlzLmNhbnZhc0RhdGEgPSBhc3NpZ24oe30sIHRoaXMuaW5pdGlhbENhbnZhc0RhdGEpO1xuICAgICAgICB0aGlzLmNyb3BCb3hEYXRhID0gYXNzaWduKHt9LCB0aGlzLmluaXRpYWxDcm9wQm94RGF0YSk7XG4gICAgICAgIHRoaXMucmVuZGVyQ2FudmFzKCk7XG4gICAgICAgIGlmICh0aGlzLmNyb3BwZWQpIHtcbiAgICAgICAgICB0aGlzLnJlbmRlckNyb3BCb3goKTtcbiAgICAgICAgfVxuICAgICAgfVxuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcbiAgICAvLyBDbGVhciB0aGUgY3JvcCBib3hcbiAgICBjbGVhcjogZnVuY3Rpb24gY2xlYXIoKSB7XG4gICAgICBpZiAodGhpcy5jcm9wcGVkICYmICF0aGlzLmRpc2FibGVkKSB7XG4gICAgICAgIGFzc2lnbih0aGlzLmNyb3BCb3hEYXRhLCB7XG4gICAgICAgICAgbGVmdDogMCxcbiAgICAgICAgICB0b3A6IDAsXG4gICAgICAgICAgd2lkdGg6IDAsXG4gICAgICAgICAgaGVpZ2h0OiAwXG4gICAgICAgIH0pO1xuICAgICAgICB0aGlzLmNyb3BwZWQgPSBmYWxzZTtcbiAgICAgICAgdGhpcy5yZW5kZXJDcm9wQm94KCk7XG4gICAgICAgIHRoaXMubGltaXRDYW52YXModHJ1ZSwgdHJ1ZSk7XG5cbiAgICAgICAgLy8gUmVuZGVyIGNhbnZhcyBhZnRlciBjcm9wIGJveCByZW5kZXJlZFxuICAgICAgICB0aGlzLnJlbmRlckNhbnZhcygpO1xuICAgICAgICByZW1vdmVDbGFzcyh0aGlzLmRyYWdCb3gsIENMQVNTX01PREFMKTtcbiAgICAgICAgYWRkQ2xhc3ModGhpcy5jcm9wQm94LCBDTEFTU19ISURERU4pO1xuICAgICAgfVxuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcbiAgICAvKipcbiAgICAgKiBSZXBsYWNlIHRoZSBpbWFnZSdzIHNyYyBhbmQgcmVidWlsZCB0aGUgY3JvcHBlclxuICAgICAqIEBwYXJhbSB7c3RyaW5nfSB1cmwgLSBUaGUgbmV3IFVSTC5cbiAgICAgKiBAcGFyYW0ge2Jvb2xlYW59IFtoYXNTYW1lU2l6ZV0gLSBJbmRpY2F0ZSBpZiB0aGUgbmV3IGltYWdlIGhhcyB0aGUgc2FtZSBzaXplIGFzIHRoZSBvbGQgb25lLlxuICAgICAqIEByZXR1cm5zIHtDcm9wcGVyfSB0aGlzXG4gICAgICovXG4gICAgcmVwbGFjZTogZnVuY3Rpb24gcmVwbGFjZSh1cmwpIHtcbiAgICAgIHZhciBoYXNTYW1lU2l6ZSA9IGFyZ3VtZW50cy5sZW5ndGggPiAxICYmIGFyZ3VtZW50c1sxXSAhPT0gdW5kZWZpbmVkID8gYXJndW1lbnRzWzFdIDogZmFsc2U7XG4gICAgICBpZiAoIXRoaXMuZGlzYWJsZWQgJiYgdXJsKSB7XG4gICAgICAgIGlmICh0aGlzLmlzSW1nKSB7XG4gICAgICAgICAgdGhpcy5lbGVtZW50LnNyYyA9IHVybDtcbiAgICAgICAgfVxuICAgICAgICBpZiAoaGFzU2FtZVNpemUpIHtcbiAgICAgICAgICB0aGlzLnVybCA9IHVybDtcbiAgICAgICAgICB0aGlzLmltYWdlLnNyYyA9IHVybDtcbiAgICAgICAgICBpZiAodGhpcy5yZWFkeSkge1xuICAgICAgICAgICAgdGhpcy52aWV3Qm94SW1hZ2Uuc3JjID0gdXJsO1xuICAgICAgICAgICAgZm9yRWFjaCh0aGlzLnByZXZpZXdzLCBmdW5jdGlvbiAoZWxlbWVudCkge1xuICAgICAgICAgICAgICBlbGVtZW50LmdldEVsZW1lbnRzQnlUYWdOYW1lKCdpbWcnKVswXS5zcmMgPSB1cmw7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICB9XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgaWYgKHRoaXMuaXNJbWcpIHtcbiAgICAgICAgICAgIHRoaXMucmVwbGFjZWQgPSB0cnVlO1xuICAgICAgICAgIH1cbiAgICAgICAgICB0aGlzLm9wdGlvbnMuZGF0YSA9IG51bGw7XG4gICAgICAgICAgdGhpcy51bmNyZWF0ZSgpO1xuICAgICAgICAgIHRoaXMubG9hZCh1cmwpO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxuICAgIC8vIEVuYWJsZSAodW5mcmVlemUpIHRoZSBjcm9wcGVyXG4gICAgZW5hYmxlOiBmdW5jdGlvbiBlbmFibGUoKSB7XG4gICAgICBpZiAodGhpcy5yZWFkeSAmJiB0aGlzLmRpc2FibGVkKSB7XG4gICAgICAgIHRoaXMuZGlzYWJsZWQgPSBmYWxzZTtcbiAgICAgICAgcmVtb3ZlQ2xhc3ModGhpcy5jcm9wcGVyLCBDTEFTU19ESVNBQkxFRCk7XG4gICAgICB9XG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxuICAgIC8vIERpc2FibGUgKGZyZWV6ZSkgdGhlIGNyb3BwZXJcbiAgICBkaXNhYmxlOiBmdW5jdGlvbiBkaXNhYmxlKCkge1xuICAgICAgaWYgKHRoaXMucmVhZHkgJiYgIXRoaXMuZGlzYWJsZWQpIHtcbiAgICAgICAgdGhpcy5kaXNhYmxlZCA9IHRydWU7XG4gICAgICAgIGFkZENsYXNzKHRoaXMuY3JvcHBlciwgQ0xBU1NfRElTQUJMRUQpO1xuICAgICAgfVxuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcbiAgICAvKipcbiAgICAgKiBEZXN0cm95IHRoZSBjcm9wcGVyIGFuZCByZW1vdmUgdGhlIGluc3RhbmNlIGZyb20gdGhlIGltYWdlXG4gICAgICogQHJldHVybnMge0Nyb3BwZXJ9IHRoaXNcbiAgICAgKi9cbiAgICBkZXN0cm95OiBmdW5jdGlvbiBkZXN0cm95KCkge1xuICAgICAgdmFyIGVsZW1lbnQgPSB0aGlzLmVsZW1lbnQ7XG4gICAgICBpZiAoIWVsZW1lbnRbTkFNRVNQQUNFXSkge1xuICAgICAgICByZXR1cm4gdGhpcztcbiAgICAgIH1cbiAgICAgIGVsZW1lbnRbTkFNRVNQQUNFXSA9IHVuZGVmaW5lZDtcbiAgICAgIGlmICh0aGlzLmlzSW1nICYmIHRoaXMucmVwbGFjZWQpIHtcbiAgICAgICAgZWxlbWVudC5zcmMgPSB0aGlzLm9yaWdpbmFsVXJsO1xuICAgICAgfVxuICAgICAgdGhpcy51bmNyZWF0ZSgpO1xuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcbiAgICAvKipcbiAgICAgKiBNb3ZlIHRoZSBjYW52YXMgd2l0aCByZWxhdGl2ZSBvZmZzZXRzXG4gICAgICogQHBhcmFtIHtudW1iZXJ9IG9mZnNldFggLSBUaGUgcmVsYXRpdmUgb2Zmc2V0IGRpc3RhbmNlIG9uIHRoZSB4LWF4aXMuXG4gICAgICogQHBhcmFtIHtudW1iZXJ9IFtvZmZzZXRZPW9mZnNldFhdIC0gVGhlIHJlbGF0aXZlIG9mZnNldCBkaXN0YW5jZSBvbiB0aGUgeS1heGlzLlxuICAgICAqIEByZXR1cm5zIHtDcm9wcGVyfSB0aGlzXG4gICAgICovXG4gICAgbW92ZTogZnVuY3Rpb24gbW92ZShvZmZzZXRYKSB7XG4gICAgICB2YXIgb2Zmc2V0WSA9IGFyZ3VtZW50cy5sZW5ndGggPiAxICYmIGFyZ3VtZW50c1sxXSAhPT0gdW5kZWZpbmVkID8gYXJndW1lbnRzWzFdIDogb2Zmc2V0WDtcbiAgICAgIHZhciBfdGhpcyRjYW52YXNEYXRhID0gdGhpcy5jYW52YXNEYXRhLFxuICAgICAgICBsZWZ0ID0gX3RoaXMkY2FudmFzRGF0YS5sZWZ0LFxuICAgICAgICB0b3AgPSBfdGhpcyRjYW52YXNEYXRhLnRvcDtcbiAgICAgIHJldHVybiB0aGlzLm1vdmVUbyhpc1VuZGVmaW5lZChvZmZzZXRYKSA/IG9mZnNldFggOiBsZWZ0ICsgTnVtYmVyKG9mZnNldFgpLCBpc1VuZGVmaW5lZChvZmZzZXRZKSA/IG9mZnNldFkgOiB0b3AgKyBOdW1iZXIob2Zmc2V0WSkpO1xuICAgIH0sXG4gICAgLyoqXG4gICAgICogTW92ZSB0aGUgY2FudmFzIHRvIGFuIGFic29sdXRlIHBvaW50XG4gICAgICogQHBhcmFtIHtudW1iZXJ9IHggLSBUaGUgeC1heGlzIGNvb3JkaW5hdGUuXG4gICAgICogQHBhcmFtIHtudW1iZXJ9IFt5PXhdIC0gVGhlIHktYXhpcyBjb29yZGluYXRlLlxuICAgICAqIEByZXR1cm5zIHtDcm9wcGVyfSB0aGlzXG4gICAgICovXG4gICAgbW92ZVRvOiBmdW5jdGlvbiBtb3ZlVG8oeCkge1xuICAgICAgdmFyIHkgPSBhcmd1bWVudHMubGVuZ3RoID4gMSAmJiBhcmd1bWVudHNbMV0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1sxXSA6IHg7XG4gICAgICB2YXIgY2FudmFzRGF0YSA9IHRoaXMuY2FudmFzRGF0YTtcbiAgICAgIHZhciBjaGFuZ2VkID0gZmFsc2U7XG4gICAgICB4ID0gTnVtYmVyKHgpO1xuICAgICAgeSA9IE51bWJlcih5KTtcbiAgICAgIGlmICh0aGlzLnJlYWR5ICYmICF0aGlzLmRpc2FibGVkICYmIHRoaXMub3B0aW9ucy5tb3ZhYmxlKSB7XG4gICAgICAgIGlmIChpc051bWJlcih4KSkge1xuICAgICAgICAgIGNhbnZhc0RhdGEubGVmdCA9IHg7XG4gICAgICAgICAgY2hhbmdlZCA9IHRydWU7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKGlzTnVtYmVyKHkpKSB7XG4gICAgICAgICAgY2FudmFzRGF0YS50b3AgPSB5O1xuICAgICAgICAgIGNoYW5nZWQgPSB0cnVlO1xuICAgICAgICB9XG4gICAgICAgIGlmIChjaGFuZ2VkKSB7XG4gICAgICAgICAgdGhpcy5yZW5kZXJDYW52YXModHJ1ZSk7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG4gICAgLyoqXG4gICAgICogWm9vbSB0aGUgY2FudmFzIHdpdGggYSByZWxhdGl2ZSByYXRpb1xuICAgICAqIEBwYXJhbSB7bnVtYmVyfSByYXRpbyAtIFRoZSB0YXJnZXQgcmF0aW8uXG4gICAgICogQHBhcmFtIHtFdmVudH0gX29yaWdpbmFsRXZlbnQgLSBUaGUgb3JpZ2luYWwgZXZlbnQgaWYgYW55LlxuICAgICAqIEByZXR1cm5zIHtDcm9wcGVyfSB0aGlzXG4gICAgICovXG4gICAgem9vbTogZnVuY3Rpb24gem9vbShyYXRpbywgX29yaWdpbmFsRXZlbnQpIHtcbiAgICAgIHZhciBjYW52YXNEYXRhID0gdGhpcy5jYW52YXNEYXRhO1xuICAgICAgcmF0aW8gPSBOdW1iZXIocmF0aW8pO1xuICAgICAgaWYgKHJhdGlvIDwgMCkge1xuICAgICAgICByYXRpbyA9IDEgLyAoMSAtIHJhdGlvKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIHJhdGlvID0gMSArIHJhdGlvO1xuICAgICAgfVxuICAgICAgcmV0dXJuIHRoaXMuem9vbVRvKGNhbnZhc0RhdGEud2lkdGggKiByYXRpbyAvIGNhbnZhc0RhdGEubmF0dXJhbFdpZHRoLCBudWxsLCBfb3JpZ2luYWxFdmVudCk7XG4gICAgfSxcbiAgICAvKipcbiAgICAgKiBab29tIHRoZSBjYW52YXMgdG8gYW4gYWJzb2x1dGUgcmF0aW9cbiAgICAgKiBAcGFyYW0ge251bWJlcn0gcmF0aW8gLSBUaGUgdGFyZ2V0IHJhdGlvLlxuICAgICAqIEBwYXJhbSB7T2JqZWN0fSBwaXZvdCAtIFRoZSB6b29tIHBpdm90IHBvaW50IGNvb3JkaW5hdGUuXG4gICAgICogQHBhcmFtIHtFdmVudH0gX29yaWdpbmFsRXZlbnQgLSBUaGUgb3JpZ2luYWwgZXZlbnQgaWYgYW55LlxuICAgICAqIEByZXR1cm5zIHtDcm9wcGVyfSB0aGlzXG4gICAgICovXG4gICAgem9vbVRvOiBmdW5jdGlvbiB6b29tVG8ocmF0aW8sIHBpdm90LCBfb3JpZ2luYWxFdmVudCkge1xuICAgICAgdmFyIG9wdGlvbnMgPSB0aGlzLm9wdGlvbnMsXG4gICAgICAgIGNhbnZhc0RhdGEgPSB0aGlzLmNhbnZhc0RhdGE7XG4gICAgICB2YXIgd2lkdGggPSBjYW52YXNEYXRhLndpZHRoLFxuICAgICAgICBoZWlnaHQgPSBjYW52YXNEYXRhLmhlaWdodCxcbiAgICAgICAgbmF0dXJhbFdpZHRoID0gY2FudmFzRGF0YS5uYXR1cmFsV2lkdGgsXG4gICAgICAgIG5hdHVyYWxIZWlnaHQgPSBjYW52YXNEYXRhLm5hdHVyYWxIZWlnaHQ7XG4gICAgICByYXRpbyA9IE51bWJlcihyYXRpbyk7XG4gICAgICBpZiAocmF0aW8gPj0gMCAmJiB0aGlzLnJlYWR5ICYmICF0aGlzLmRpc2FibGVkICYmIG9wdGlvbnMuem9vbWFibGUpIHtcbiAgICAgICAgdmFyIG5ld1dpZHRoID0gbmF0dXJhbFdpZHRoICogcmF0aW87XG4gICAgICAgIHZhciBuZXdIZWlnaHQgPSBuYXR1cmFsSGVpZ2h0ICogcmF0aW87XG4gICAgICAgIGlmIChkaXNwYXRjaEV2ZW50KHRoaXMuZWxlbWVudCwgRVZFTlRfWk9PTSwge1xuICAgICAgICAgIHJhdGlvOiByYXRpbyxcbiAgICAgICAgICBvbGRSYXRpbzogd2lkdGggLyBuYXR1cmFsV2lkdGgsXG4gICAgICAgICAgb3JpZ2luYWxFdmVudDogX29yaWdpbmFsRXZlbnRcbiAgICAgICAgfSkgPT09IGZhbHNlKSB7XG4gICAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKF9vcmlnaW5hbEV2ZW50KSB7XG4gICAgICAgICAgdmFyIHBvaW50ZXJzID0gdGhpcy5wb2ludGVycztcbiAgICAgICAgICB2YXIgb2Zmc2V0ID0gZ2V0T2Zmc2V0KHRoaXMuY3JvcHBlcik7XG4gICAgICAgICAgdmFyIGNlbnRlciA9IHBvaW50ZXJzICYmIE9iamVjdC5rZXlzKHBvaW50ZXJzKS5sZW5ndGggPyBnZXRQb2ludGVyc0NlbnRlcihwb2ludGVycykgOiB7XG4gICAgICAgICAgICBwYWdlWDogX29yaWdpbmFsRXZlbnQucGFnZVgsXG4gICAgICAgICAgICBwYWdlWTogX29yaWdpbmFsRXZlbnQucGFnZVlcbiAgICAgICAgICB9O1xuXG4gICAgICAgICAgLy8gWm9vbSBmcm9tIHRoZSB0cmlnZ2VyaW5nIHBvaW50IG9mIHRoZSBldmVudFxuICAgICAgICAgIGNhbnZhc0RhdGEubGVmdCAtPSAobmV3V2lkdGggLSB3aWR0aCkgKiAoKGNlbnRlci5wYWdlWCAtIG9mZnNldC5sZWZ0IC0gY2FudmFzRGF0YS5sZWZ0KSAvIHdpZHRoKTtcbiAgICAgICAgICBjYW52YXNEYXRhLnRvcCAtPSAobmV3SGVpZ2h0IC0gaGVpZ2h0KSAqICgoY2VudGVyLnBhZ2VZIC0gb2Zmc2V0LnRvcCAtIGNhbnZhc0RhdGEudG9wKSAvIGhlaWdodCk7XG4gICAgICAgIH0gZWxzZSBpZiAoaXNQbGFpbk9iamVjdChwaXZvdCkgJiYgaXNOdW1iZXIocGl2b3QueCkgJiYgaXNOdW1iZXIocGl2b3QueSkpIHtcbiAgICAgICAgICBjYW52YXNEYXRhLmxlZnQgLT0gKG5ld1dpZHRoIC0gd2lkdGgpICogKChwaXZvdC54IC0gY2FudmFzRGF0YS5sZWZ0KSAvIHdpZHRoKTtcbiAgICAgICAgICBjYW52YXNEYXRhLnRvcCAtPSAobmV3SGVpZ2h0IC0gaGVpZ2h0KSAqICgocGl2b3QueSAtIGNhbnZhc0RhdGEudG9wKSAvIGhlaWdodCk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgLy8gWm9vbSBmcm9tIHRoZSBjZW50ZXIgb2YgdGhlIGNhbnZhc1xuICAgICAgICAgIGNhbnZhc0RhdGEubGVmdCAtPSAobmV3V2lkdGggLSB3aWR0aCkgLyAyO1xuICAgICAgICAgIGNhbnZhc0RhdGEudG9wIC09IChuZXdIZWlnaHQgLSBoZWlnaHQpIC8gMjtcbiAgICAgICAgfVxuICAgICAgICBjYW52YXNEYXRhLndpZHRoID0gbmV3V2lkdGg7XG4gICAgICAgIGNhbnZhc0RhdGEuaGVpZ2h0ID0gbmV3SGVpZ2h0O1xuICAgICAgICB0aGlzLnJlbmRlckNhbnZhcyh0cnVlKTtcbiAgICAgIH1cbiAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG4gICAgLyoqXG4gICAgICogUm90YXRlIHRoZSBjYW52YXMgd2l0aCBhIHJlbGF0aXZlIGRlZ3JlZVxuICAgICAqIEBwYXJhbSB7bnVtYmVyfSBkZWdyZWUgLSBUaGUgcm90YXRlIGRlZ3JlZS5cbiAgICAgKiBAcmV0dXJucyB7Q3JvcHBlcn0gdGhpc1xuICAgICAqL1xuICAgIHJvdGF0ZTogZnVuY3Rpb24gcm90YXRlKGRlZ3JlZSkge1xuICAgICAgcmV0dXJuIHRoaXMucm90YXRlVG8oKHRoaXMuaW1hZ2VEYXRhLnJvdGF0ZSB8fCAwKSArIE51bWJlcihkZWdyZWUpKTtcbiAgICB9LFxuICAgIC8qKlxuICAgICAqIFJvdGF0ZSB0aGUgY2FudmFzIHRvIGFuIGFic29sdXRlIGRlZ3JlZVxuICAgICAqIEBwYXJhbSB7bnVtYmVyfSBkZWdyZWUgLSBUaGUgcm90YXRlIGRlZ3JlZS5cbiAgICAgKiBAcmV0dXJucyB7Q3JvcHBlcn0gdGhpc1xuICAgICAqL1xuICAgIHJvdGF0ZVRvOiBmdW5jdGlvbiByb3RhdGVUbyhkZWdyZWUpIHtcbiAgICAgIGRlZ3JlZSA9IE51bWJlcihkZWdyZWUpO1xuICAgICAgaWYgKGlzTnVtYmVyKGRlZ3JlZSkgJiYgdGhpcy5yZWFkeSAmJiAhdGhpcy5kaXNhYmxlZCAmJiB0aGlzLm9wdGlvbnMucm90YXRhYmxlKSB7XG4gICAgICAgIHRoaXMuaW1hZ2VEYXRhLnJvdGF0ZSA9IGRlZ3JlZSAlIDM2MDtcbiAgICAgICAgdGhpcy5yZW5kZXJDYW52YXModHJ1ZSwgdHJ1ZSk7XG4gICAgICB9XG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxuICAgIC8qKlxuICAgICAqIFNjYWxlIHRoZSBpbWFnZSBvbiB0aGUgeC1heGlzLlxuICAgICAqIEBwYXJhbSB7bnVtYmVyfSBzY2FsZVggLSBUaGUgc2NhbGUgcmF0aW8gb24gdGhlIHgtYXhpcy5cbiAgICAgKiBAcmV0dXJucyB7Q3JvcHBlcn0gdGhpc1xuICAgICAqL1xuICAgIHNjYWxlWDogZnVuY3Rpb24gc2NhbGVYKF9zY2FsZVgpIHtcbiAgICAgIHZhciBzY2FsZVkgPSB0aGlzLmltYWdlRGF0YS5zY2FsZVk7XG4gICAgICByZXR1cm4gdGhpcy5zY2FsZShfc2NhbGVYLCBpc051bWJlcihzY2FsZVkpID8gc2NhbGVZIDogMSk7XG4gICAgfSxcbiAgICAvKipcbiAgICAgKiBTY2FsZSB0aGUgaW1hZ2Ugb24gdGhlIHktYXhpcy5cbiAgICAgKiBAcGFyYW0ge251bWJlcn0gc2NhbGVZIC0gVGhlIHNjYWxlIHJhdGlvIG9uIHRoZSB5LWF4aXMuXG4gICAgICogQHJldHVybnMge0Nyb3BwZXJ9IHRoaXNcbiAgICAgKi9cbiAgICBzY2FsZVk6IGZ1bmN0aW9uIHNjYWxlWShfc2NhbGVZKSB7XG4gICAgICB2YXIgc2NhbGVYID0gdGhpcy5pbWFnZURhdGEuc2NhbGVYO1xuICAgICAgcmV0dXJuIHRoaXMuc2NhbGUoaXNOdW1iZXIoc2NhbGVYKSA/IHNjYWxlWCA6IDEsIF9zY2FsZVkpO1xuICAgIH0sXG4gICAgLyoqXG4gICAgICogU2NhbGUgdGhlIGltYWdlXG4gICAgICogQHBhcmFtIHtudW1iZXJ9IHNjYWxlWCAtIFRoZSBzY2FsZSByYXRpbyBvbiB0aGUgeC1heGlzLlxuICAgICAqIEBwYXJhbSB7bnVtYmVyfSBbc2NhbGVZPXNjYWxlWF0gLSBUaGUgc2NhbGUgcmF0aW8gb24gdGhlIHktYXhpcy5cbiAgICAgKiBAcmV0dXJucyB7Q3JvcHBlcn0gdGhpc1xuICAgICAqL1xuICAgIHNjYWxlOiBmdW5jdGlvbiBzY2FsZShzY2FsZVgpIHtcbiAgICAgIHZhciBzY2FsZVkgPSBhcmd1bWVudHMubGVuZ3RoID4gMSAmJiBhcmd1bWVudHNbMV0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1sxXSA6IHNjYWxlWDtcbiAgICAgIHZhciBpbWFnZURhdGEgPSB0aGlzLmltYWdlRGF0YTtcbiAgICAgIHZhciB0cmFuc2Zvcm1lZCA9IGZhbHNlO1xuICAgICAgc2NhbGVYID0gTnVtYmVyKHNjYWxlWCk7XG4gICAgICBzY2FsZVkgPSBOdW1iZXIoc2NhbGVZKTtcbiAgICAgIGlmICh0aGlzLnJlYWR5ICYmICF0aGlzLmRpc2FibGVkICYmIHRoaXMub3B0aW9ucy5zY2FsYWJsZSkge1xuICAgICAgICBpZiAoaXNOdW1iZXIoc2NhbGVYKSkge1xuICAgICAgICAgIGltYWdlRGF0YS5zY2FsZVggPSBzY2FsZVg7XG4gICAgICAgICAgdHJhbnNmb3JtZWQgPSB0cnVlO1xuICAgICAgICB9XG4gICAgICAgIGlmIChpc051bWJlcihzY2FsZVkpKSB7XG4gICAgICAgICAgaW1hZ2VEYXRhLnNjYWxlWSA9IHNjYWxlWTtcbiAgICAgICAgICB0cmFuc2Zvcm1lZCA9IHRydWU7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKHRyYW5zZm9ybWVkKSB7XG4gICAgICAgICAgdGhpcy5yZW5kZXJDYW52YXModHJ1ZSwgdHJ1ZSk7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG4gICAgLyoqXG4gICAgICogR2V0IHRoZSBjcm9wcGVkIGFyZWEgcG9zaXRpb24gYW5kIHNpemUgZGF0YSAoYmFzZSBvbiB0aGUgb3JpZ2luYWwgaW1hZ2UpXG4gICAgICogQHBhcmFtIHtib29sZWFufSBbcm91bmRlZD1mYWxzZV0gLSBJbmRpY2F0ZSBpZiByb3VuZCB0aGUgZGF0YSB2YWx1ZXMgb3Igbm90LlxuICAgICAqIEByZXR1cm5zIHtPYmplY3R9IFRoZSByZXN1bHQgY3JvcHBlZCBkYXRhLlxuICAgICAqL1xuICAgIGdldERhdGE6IGZ1bmN0aW9uIGdldERhdGEoKSB7XG4gICAgICB2YXIgcm91bmRlZCA9IGFyZ3VtZW50cy5sZW5ndGggPiAwICYmIGFyZ3VtZW50c1swXSAhPT0gdW5kZWZpbmVkID8gYXJndW1lbnRzWzBdIDogZmFsc2U7XG4gICAgICB2YXIgb3B0aW9ucyA9IHRoaXMub3B0aW9ucyxcbiAgICAgICAgaW1hZ2VEYXRhID0gdGhpcy5pbWFnZURhdGEsXG4gICAgICAgIGNhbnZhc0RhdGEgPSB0aGlzLmNhbnZhc0RhdGEsXG4gICAgICAgIGNyb3BCb3hEYXRhID0gdGhpcy5jcm9wQm94RGF0YTtcbiAgICAgIHZhciBkYXRhO1xuICAgICAgaWYgKHRoaXMucmVhZHkgJiYgdGhpcy5jcm9wcGVkKSB7XG4gICAgICAgIGRhdGEgPSB7XG4gICAgICAgICAgeDogY3JvcEJveERhdGEubGVmdCAtIGNhbnZhc0RhdGEubGVmdCxcbiAgICAgICAgICB5OiBjcm9wQm94RGF0YS50b3AgLSBjYW52YXNEYXRhLnRvcCxcbiAgICAgICAgICB3aWR0aDogY3JvcEJveERhdGEud2lkdGgsXG4gICAgICAgICAgaGVpZ2h0OiBjcm9wQm94RGF0YS5oZWlnaHRcbiAgICAgICAgfTtcbiAgICAgICAgdmFyIHJhdGlvID0gaW1hZ2VEYXRhLndpZHRoIC8gaW1hZ2VEYXRhLm5hdHVyYWxXaWR0aDtcbiAgICAgICAgZm9yRWFjaChkYXRhLCBmdW5jdGlvbiAobiwgaSkge1xuICAgICAgICAgIGRhdGFbaV0gPSBuIC8gcmF0aW87XG4gICAgICAgIH0pO1xuICAgICAgICBpZiAocm91bmRlZCkge1xuICAgICAgICAgIC8vIEluIGNhc2Ugcm91bmRpbmcgb2ZmIGxlYWRzIHRvIGV4dHJhIDFweCBpbiByaWdodCBvciBib3R0b20gYm9yZGVyXG4gICAgICAgICAgLy8gd2Ugc2hvdWxkIHJvdW5kIHRoZSB0b3AtbGVmdCBjb3JuZXIgYW5kIHRoZSBkaW1lbnNpb24gKCMzNDMpLlxuICAgICAgICAgIHZhciBib3R0b20gPSBNYXRoLnJvdW5kKGRhdGEueSArIGRhdGEuaGVpZ2h0KTtcbiAgICAgICAgICB2YXIgcmlnaHQgPSBNYXRoLnJvdW5kKGRhdGEueCArIGRhdGEud2lkdGgpO1xuICAgICAgICAgIGRhdGEueCA9IE1hdGgucm91bmQoZGF0YS54KTtcbiAgICAgICAgICBkYXRhLnkgPSBNYXRoLnJvdW5kKGRhdGEueSk7XG4gICAgICAgICAgZGF0YS53aWR0aCA9IHJpZ2h0IC0gZGF0YS54O1xuICAgICAgICAgIGRhdGEuaGVpZ2h0ID0gYm90dG9tIC0gZGF0YS55O1xuICAgICAgICB9XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBkYXRhID0ge1xuICAgICAgICAgIHg6IDAsXG4gICAgICAgICAgeTogMCxcbiAgICAgICAgICB3aWR0aDogMCxcbiAgICAgICAgICBoZWlnaHQ6IDBcbiAgICAgICAgfTtcbiAgICAgIH1cbiAgICAgIGlmIChvcHRpb25zLnJvdGF0YWJsZSkge1xuICAgICAgICBkYXRhLnJvdGF0ZSA9IGltYWdlRGF0YS5yb3RhdGUgfHwgMDtcbiAgICAgIH1cbiAgICAgIGlmIChvcHRpb25zLnNjYWxhYmxlKSB7XG4gICAgICAgIGRhdGEuc2NhbGVYID0gaW1hZ2VEYXRhLnNjYWxlWCB8fCAxO1xuICAgICAgICBkYXRhLnNjYWxlWSA9IGltYWdlRGF0YS5zY2FsZVkgfHwgMTtcbiAgICAgIH1cbiAgICAgIHJldHVybiBkYXRhO1xuICAgIH0sXG4gICAgLyoqXG4gICAgICogU2V0IHRoZSBjcm9wcGVkIGFyZWEgcG9zaXRpb24gYW5kIHNpemUgd2l0aCBuZXcgZGF0YVxuICAgICAqIEBwYXJhbSB7T2JqZWN0fSBkYXRhIC0gVGhlIG5ldyBkYXRhLlxuICAgICAqIEByZXR1cm5zIHtDcm9wcGVyfSB0aGlzXG4gICAgICovXG4gICAgc2V0RGF0YTogZnVuY3Rpb24gc2V0RGF0YShkYXRhKSB7XG4gICAgICB2YXIgb3B0aW9ucyA9IHRoaXMub3B0aW9ucyxcbiAgICAgICAgaW1hZ2VEYXRhID0gdGhpcy5pbWFnZURhdGEsXG4gICAgICAgIGNhbnZhc0RhdGEgPSB0aGlzLmNhbnZhc0RhdGE7XG4gICAgICB2YXIgY3JvcEJveERhdGEgPSB7fTtcbiAgICAgIGlmICh0aGlzLnJlYWR5ICYmICF0aGlzLmRpc2FibGVkICYmIGlzUGxhaW5PYmplY3QoZGF0YSkpIHtcbiAgICAgICAgdmFyIHRyYW5zZm9ybWVkID0gZmFsc2U7XG4gICAgICAgIGlmIChvcHRpb25zLnJvdGF0YWJsZSkge1xuICAgICAgICAgIGlmIChpc051bWJlcihkYXRhLnJvdGF0ZSkgJiYgZGF0YS5yb3RhdGUgIT09IGltYWdlRGF0YS5yb3RhdGUpIHtcbiAgICAgICAgICAgIGltYWdlRGF0YS5yb3RhdGUgPSBkYXRhLnJvdGF0ZTtcbiAgICAgICAgICAgIHRyYW5zZm9ybWVkID0gdHJ1ZTtcbiAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgICAgaWYgKG9wdGlvbnMuc2NhbGFibGUpIHtcbiAgICAgICAgICBpZiAoaXNOdW1iZXIoZGF0YS5zY2FsZVgpICYmIGRhdGEuc2NhbGVYICE9PSBpbWFnZURhdGEuc2NhbGVYKSB7XG4gICAgICAgICAgICBpbWFnZURhdGEuc2NhbGVYID0gZGF0YS5zY2FsZVg7XG4gICAgICAgICAgICB0cmFuc2Zvcm1lZCA9IHRydWU7XG4gICAgICAgICAgfVxuICAgICAgICAgIGlmIChpc051bWJlcihkYXRhLnNjYWxlWSkgJiYgZGF0YS5zY2FsZVkgIT09IGltYWdlRGF0YS5zY2FsZVkpIHtcbiAgICAgICAgICAgIGltYWdlRGF0YS5zY2FsZVkgPSBkYXRhLnNjYWxlWTtcbiAgICAgICAgICAgIHRyYW5zZm9ybWVkID0gdHJ1ZTtcbiAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgICAgaWYgKHRyYW5zZm9ybWVkKSB7XG4gICAgICAgICAgdGhpcy5yZW5kZXJDYW52YXModHJ1ZSwgdHJ1ZSk7XG4gICAgICAgIH1cbiAgICAgICAgdmFyIHJhdGlvID0gaW1hZ2VEYXRhLndpZHRoIC8gaW1hZ2VEYXRhLm5hdHVyYWxXaWR0aDtcbiAgICAgICAgaWYgKGlzTnVtYmVyKGRhdGEueCkpIHtcbiAgICAgICAgICBjcm9wQm94RGF0YS5sZWZ0ID0gZGF0YS54ICogcmF0aW8gKyBjYW52YXNEYXRhLmxlZnQ7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKGlzTnVtYmVyKGRhdGEueSkpIHtcbiAgICAgICAgICBjcm9wQm94RGF0YS50b3AgPSBkYXRhLnkgKiByYXRpbyArIGNhbnZhc0RhdGEudG9wO1xuICAgICAgICB9XG4gICAgICAgIGlmIChpc051bWJlcihkYXRhLndpZHRoKSkge1xuICAgICAgICAgIGNyb3BCb3hEYXRhLndpZHRoID0gZGF0YS53aWR0aCAqIHJhdGlvO1xuICAgICAgICB9XG4gICAgICAgIGlmIChpc051bWJlcihkYXRhLmhlaWdodCkpIHtcbiAgICAgICAgICBjcm9wQm94RGF0YS5oZWlnaHQgPSBkYXRhLmhlaWdodCAqIHJhdGlvO1xuICAgICAgICB9XG4gICAgICAgIHRoaXMuc2V0Q3JvcEJveERhdGEoY3JvcEJveERhdGEpO1xuICAgICAgfVxuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcbiAgICAvKipcbiAgICAgKiBHZXQgdGhlIGNvbnRhaW5lciBzaXplIGRhdGEuXG4gICAgICogQHJldHVybnMge09iamVjdH0gVGhlIHJlc3VsdCBjb250YWluZXIgZGF0YS5cbiAgICAgKi9cbiAgICBnZXRDb250YWluZXJEYXRhOiBmdW5jdGlvbiBnZXRDb250YWluZXJEYXRhKCkge1xuICAgICAgcmV0dXJuIHRoaXMucmVhZHkgPyBhc3NpZ24oe30sIHRoaXMuY29udGFpbmVyRGF0YSkgOiB7fTtcbiAgICB9LFxuICAgIC8qKlxuICAgICAqIEdldCB0aGUgaW1hZ2UgcG9zaXRpb24gYW5kIHNpemUgZGF0YS5cbiAgICAgKiBAcmV0dXJucyB7T2JqZWN0fSBUaGUgcmVzdWx0IGltYWdlIGRhdGEuXG4gICAgICovXG4gICAgZ2V0SW1hZ2VEYXRhOiBmdW5jdGlvbiBnZXRJbWFnZURhdGEoKSB7XG4gICAgICByZXR1cm4gdGhpcy5zaXplZCA/IGFzc2lnbih7fSwgdGhpcy5pbWFnZURhdGEpIDoge307XG4gICAgfSxcbiAgICAvKipcbiAgICAgKiBHZXQgdGhlIGNhbnZhcyBwb3NpdGlvbiBhbmQgc2l6ZSBkYXRhLlxuICAgICAqIEByZXR1cm5zIHtPYmplY3R9IFRoZSByZXN1bHQgY2FudmFzIGRhdGEuXG4gICAgICovXG4gICAgZ2V0Q2FudmFzRGF0YTogZnVuY3Rpb24gZ2V0Q2FudmFzRGF0YSgpIHtcbiAgICAgIHZhciBjYW52YXNEYXRhID0gdGhpcy5jYW52YXNEYXRhO1xuICAgICAgdmFyIGRhdGEgPSB7fTtcbiAgICAgIGlmICh0aGlzLnJlYWR5KSB7XG4gICAgICAgIGZvckVhY2goWydsZWZ0JywgJ3RvcCcsICd3aWR0aCcsICdoZWlnaHQnLCAnbmF0dXJhbFdpZHRoJywgJ25hdHVyYWxIZWlnaHQnXSwgZnVuY3Rpb24gKG4pIHtcbiAgICAgICAgICBkYXRhW25dID0gY2FudmFzRGF0YVtuXTtcbiAgICAgICAgfSk7XG4gICAgICB9XG4gICAgICByZXR1cm4gZGF0YTtcbiAgICB9LFxuICAgIC8qKlxuICAgICAqIFNldCB0aGUgY2FudmFzIHBvc2l0aW9uIGFuZCBzaXplIHdpdGggbmV3IGRhdGEuXG4gICAgICogQHBhcmFtIHtPYmplY3R9IGRhdGEgLSBUaGUgbmV3IGNhbnZhcyBkYXRhLlxuICAgICAqIEByZXR1cm5zIHtDcm9wcGVyfSB0aGlzXG4gICAgICovXG4gICAgc2V0Q2FudmFzRGF0YTogZnVuY3Rpb24gc2V0Q2FudmFzRGF0YShkYXRhKSB7XG4gICAgICB2YXIgY2FudmFzRGF0YSA9IHRoaXMuY2FudmFzRGF0YTtcbiAgICAgIHZhciBhc3BlY3RSYXRpbyA9IGNhbnZhc0RhdGEuYXNwZWN0UmF0aW87XG4gICAgICBpZiAodGhpcy5yZWFkeSAmJiAhdGhpcy5kaXNhYmxlZCAmJiBpc1BsYWluT2JqZWN0KGRhdGEpKSB7XG4gICAgICAgIGlmIChpc051bWJlcihkYXRhLmxlZnQpKSB7XG4gICAgICAgICAgY2FudmFzRGF0YS5sZWZ0ID0gZGF0YS5sZWZ0O1xuICAgICAgICB9XG4gICAgICAgIGlmIChpc051bWJlcihkYXRhLnRvcCkpIHtcbiAgICAgICAgICBjYW52YXNEYXRhLnRvcCA9IGRhdGEudG9wO1xuICAgICAgICB9XG4gICAgICAgIGlmIChpc051bWJlcihkYXRhLndpZHRoKSkge1xuICAgICAgICAgIGNhbnZhc0RhdGEud2lkdGggPSBkYXRhLndpZHRoO1xuICAgICAgICAgIGNhbnZhc0RhdGEuaGVpZ2h0ID0gZGF0YS53aWR0aCAvIGFzcGVjdFJhdGlvO1xuICAgICAgICB9IGVsc2UgaWYgKGlzTnVtYmVyKGRhdGEuaGVpZ2h0KSkge1xuICAgICAgICAgIGNhbnZhc0RhdGEuaGVpZ2h0ID0gZGF0YS5oZWlnaHQ7XG4gICAgICAgICAgY2FudmFzRGF0YS53aWR0aCA9IGRhdGEuaGVpZ2h0ICogYXNwZWN0UmF0aW87XG4gICAgICAgIH1cbiAgICAgICAgdGhpcy5yZW5kZXJDYW52YXModHJ1ZSk7XG4gICAgICB9XG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxuICAgIC8qKlxuICAgICAqIEdldCB0aGUgY3JvcCBib3ggcG9zaXRpb24gYW5kIHNpemUgZGF0YS5cbiAgICAgKiBAcmV0dXJucyB7T2JqZWN0fSBUaGUgcmVzdWx0IGNyb3AgYm94IGRhdGEuXG4gICAgICovXG4gICAgZ2V0Q3JvcEJveERhdGE6IGZ1bmN0aW9uIGdldENyb3BCb3hEYXRhKCkge1xuICAgICAgdmFyIGNyb3BCb3hEYXRhID0gdGhpcy5jcm9wQm94RGF0YTtcbiAgICAgIHZhciBkYXRhO1xuICAgICAgaWYgKHRoaXMucmVhZHkgJiYgdGhpcy5jcm9wcGVkKSB7XG4gICAgICAgIGRhdGEgPSB7XG4gICAgICAgICAgbGVmdDogY3JvcEJveERhdGEubGVmdCxcbiAgICAgICAgICB0b3A6IGNyb3BCb3hEYXRhLnRvcCxcbiAgICAgICAgICB3aWR0aDogY3JvcEJveERhdGEud2lkdGgsXG4gICAgICAgICAgaGVpZ2h0OiBjcm9wQm94RGF0YS5oZWlnaHRcbiAgICAgICAgfTtcbiAgICAgIH1cbiAgICAgIHJldHVybiBkYXRhIHx8IHt9O1xuICAgIH0sXG4gICAgLyoqXG4gICAgICogU2V0IHRoZSBjcm9wIGJveCBwb3NpdGlvbiBhbmQgc2l6ZSB3aXRoIG5ldyBkYXRhLlxuICAgICAqIEBwYXJhbSB7T2JqZWN0fSBkYXRhIC0gVGhlIG5ldyBjcm9wIGJveCBkYXRhLlxuICAgICAqIEByZXR1cm5zIHtDcm9wcGVyfSB0aGlzXG4gICAgICovXG4gICAgc2V0Q3JvcEJveERhdGE6IGZ1bmN0aW9uIHNldENyb3BCb3hEYXRhKGRhdGEpIHtcbiAgICAgIHZhciBjcm9wQm94RGF0YSA9IHRoaXMuY3JvcEJveERhdGE7XG4gICAgICB2YXIgYXNwZWN0UmF0aW8gPSB0aGlzLm9wdGlvbnMuYXNwZWN0UmF0aW87XG4gICAgICB2YXIgd2lkdGhDaGFuZ2VkO1xuICAgICAgdmFyIGhlaWdodENoYW5nZWQ7XG4gICAgICBpZiAodGhpcy5yZWFkeSAmJiB0aGlzLmNyb3BwZWQgJiYgIXRoaXMuZGlzYWJsZWQgJiYgaXNQbGFpbk9iamVjdChkYXRhKSkge1xuICAgICAgICBpZiAoaXNOdW1iZXIoZGF0YS5sZWZ0KSkge1xuICAgICAgICAgIGNyb3BCb3hEYXRhLmxlZnQgPSBkYXRhLmxlZnQ7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKGlzTnVtYmVyKGRhdGEudG9wKSkge1xuICAgICAgICAgIGNyb3BCb3hEYXRhLnRvcCA9IGRhdGEudG9wO1xuICAgICAgICB9XG4gICAgICAgIGlmIChpc051bWJlcihkYXRhLndpZHRoKSAmJiBkYXRhLndpZHRoICE9PSBjcm9wQm94RGF0YS53aWR0aCkge1xuICAgICAgICAgIHdpZHRoQ2hhbmdlZCA9IHRydWU7XG4gICAgICAgICAgY3JvcEJveERhdGEud2lkdGggPSBkYXRhLndpZHRoO1xuICAgICAgICB9XG4gICAgICAgIGlmIChpc051bWJlcihkYXRhLmhlaWdodCkgJiYgZGF0YS5oZWlnaHQgIT09IGNyb3BCb3hEYXRhLmhlaWdodCkge1xuICAgICAgICAgIGhlaWdodENoYW5nZWQgPSB0cnVlO1xuICAgICAgICAgIGNyb3BCb3hEYXRhLmhlaWdodCA9IGRhdGEuaGVpZ2h0O1xuICAgICAgICB9XG4gICAgICAgIGlmIChhc3BlY3RSYXRpbykge1xuICAgICAgICAgIGlmICh3aWR0aENoYW5nZWQpIHtcbiAgICAgICAgICAgIGNyb3BCb3hEYXRhLmhlaWdodCA9IGNyb3BCb3hEYXRhLndpZHRoIC8gYXNwZWN0UmF0aW87XG4gICAgICAgICAgfSBlbHNlIGlmIChoZWlnaHRDaGFuZ2VkKSB7XG4gICAgICAgICAgICBjcm9wQm94RGF0YS53aWR0aCA9IGNyb3BCb3hEYXRhLmhlaWdodCAqIGFzcGVjdFJhdGlvO1xuICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgICAgICB0aGlzLnJlbmRlckNyb3BCb3goKTtcbiAgICAgIH1cbiAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG4gICAgLyoqXG4gICAgICogR2V0IGEgY2FudmFzIGRyYXduIHRoZSBjcm9wcGVkIGltYWdlLlxuICAgICAqIEBwYXJhbSB7T2JqZWN0fSBbb3B0aW9ucz17fV0gLSBUaGUgY29uZmlnIG9wdGlvbnMuXG4gICAgICogQHJldHVybnMge0hUTUxDYW52YXNFbGVtZW50fSAtIFRoZSByZXN1bHQgY2FudmFzLlxuICAgICAqL1xuICAgIGdldENyb3BwZWRDYW52YXM6IGZ1bmN0aW9uIGdldENyb3BwZWRDYW52YXMoKSB7XG4gICAgICB2YXIgb3B0aW9ucyA9IGFyZ3VtZW50cy5sZW5ndGggPiAwICYmIGFyZ3VtZW50c1swXSAhPT0gdW5kZWZpbmVkID8gYXJndW1lbnRzWzBdIDoge307XG4gICAgICBpZiAoIXRoaXMucmVhZHkgfHwgIXdpbmRvdy5IVE1MQ2FudmFzRWxlbWVudCkge1xuICAgICAgICByZXR1cm4gbnVsbDtcbiAgICAgIH1cbiAgICAgIHZhciBjYW52YXNEYXRhID0gdGhpcy5jYW52YXNEYXRhO1xuICAgICAgdmFyIHNvdXJjZSA9IGdldFNvdXJjZUNhbnZhcyh0aGlzLmltYWdlLCB0aGlzLmltYWdlRGF0YSwgY2FudmFzRGF0YSwgb3B0aW9ucyk7XG5cbiAgICAgIC8vIFJldHVybnMgdGhlIHNvdXJjZSBjYW52YXMgaWYgaXQgaXMgbm90IGNyb3BwZWQuXG4gICAgICBpZiAoIXRoaXMuY3JvcHBlZCkge1xuICAgICAgICByZXR1cm4gc291cmNlO1xuICAgICAgfVxuICAgICAgdmFyIF90aGlzJGdldERhdGEgPSB0aGlzLmdldERhdGEoKSxcbiAgICAgICAgaW5pdGlhbFggPSBfdGhpcyRnZXREYXRhLngsXG4gICAgICAgIGluaXRpYWxZID0gX3RoaXMkZ2V0RGF0YS55LFxuICAgICAgICBpbml0aWFsV2lkdGggPSBfdGhpcyRnZXREYXRhLndpZHRoLFxuICAgICAgICBpbml0aWFsSGVpZ2h0ID0gX3RoaXMkZ2V0RGF0YS5oZWlnaHQ7XG4gICAgICB2YXIgcmF0aW8gPSBzb3VyY2Uud2lkdGggLyBNYXRoLmZsb29yKGNhbnZhc0RhdGEubmF0dXJhbFdpZHRoKTtcbiAgICAgIGlmIChyYXRpbyAhPT0gMSkge1xuICAgICAgICBpbml0aWFsWCAqPSByYXRpbztcbiAgICAgICAgaW5pdGlhbFkgKj0gcmF0aW87XG4gICAgICAgIGluaXRpYWxXaWR0aCAqPSByYXRpbztcbiAgICAgICAgaW5pdGlhbEhlaWdodCAqPSByYXRpbztcbiAgICAgIH1cbiAgICAgIHZhciBhc3BlY3RSYXRpbyA9IGluaXRpYWxXaWR0aCAvIGluaXRpYWxIZWlnaHQ7XG4gICAgICB2YXIgbWF4U2l6ZXMgPSBnZXRBZGp1c3RlZFNpemVzKHtcbiAgICAgICAgYXNwZWN0UmF0aW86IGFzcGVjdFJhdGlvLFxuICAgICAgICB3aWR0aDogb3B0aW9ucy5tYXhXaWR0aCB8fCBJbmZpbml0eSxcbiAgICAgICAgaGVpZ2h0OiBvcHRpb25zLm1heEhlaWdodCB8fCBJbmZpbml0eVxuICAgICAgfSk7XG4gICAgICB2YXIgbWluU2l6ZXMgPSBnZXRBZGp1c3RlZFNpemVzKHtcbiAgICAgICAgYXNwZWN0UmF0aW86IGFzcGVjdFJhdGlvLFxuICAgICAgICB3aWR0aDogb3B0aW9ucy5taW5XaWR0aCB8fCAwLFxuICAgICAgICBoZWlnaHQ6IG9wdGlvbnMubWluSGVpZ2h0IHx8IDBcbiAgICAgIH0sICdjb3ZlcicpO1xuICAgICAgdmFyIF9nZXRBZGp1c3RlZFNpemVzID0gZ2V0QWRqdXN0ZWRTaXplcyh7XG4gICAgICAgICAgYXNwZWN0UmF0aW86IGFzcGVjdFJhdGlvLFxuICAgICAgICAgIHdpZHRoOiBvcHRpb25zLndpZHRoIHx8IChyYXRpbyAhPT0gMSA/IHNvdXJjZS53aWR0aCA6IGluaXRpYWxXaWR0aCksXG4gICAgICAgICAgaGVpZ2h0OiBvcHRpb25zLmhlaWdodCB8fCAocmF0aW8gIT09IDEgPyBzb3VyY2UuaGVpZ2h0IDogaW5pdGlhbEhlaWdodClcbiAgICAgICAgfSksXG4gICAgICAgIHdpZHRoID0gX2dldEFkanVzdGVkU2l6ZXMud2lkdGgsXG4gICAgICAgIGhlaWdodCA9IF9nZXRBZGp1c3RlZFNpemVzLmhlaWdodDtcbiAgICAgIHdpZHRoID0gTWF0aC5taW4obWF4U2l6ZXMud2lkdGgsIE1hdGgubWF4KG1pblNpemVzLndpZHRoLCB3aWR0aCkpO1xuICAgICAgaGVpZ2h0ID0gTWF0aC5taW4obWF4U2l6ZXMuaGVpZ2h0LCBNYXRoLm1heChtaW5TaXplcy5oZWlnaHQsIGhlaWdodCkpO1xuICAgICAgdmFyIGNhbnZhcyA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2NhbnZhcycpO1xuICAgICAgdmFyIGNvbnRleHQgPSBjYW52YXMuZ2V0Q29udGV4dCgnMmQnKTtcbiAgICAgIGNhbnZhcy53aWR0aCA9IG5vcm1hbGl6ZURlY2ltYWxOdW1iZXIod2lkdGgpO1xuICAgICAgY2FudmFzLmhlaWdodCA9IG5vcm1hbGl6ZURlY2ltYWxOdW1iZXIoaGVpZ2h0KTtcbiAgICAgIGNvbnRleHQuZmlsbFN0eWxlID0gb3B0aW9ucy5maWxsQ29sb3IgfHwgJ3RyYW5zcGFyZW50JztcbiAgICAgIGNvbnRleHQuZmlsbFJlY3QoMCwgMCwgd2lkdGgsIGhlaWdodCk7XG4gICAgICB2YXIgX29wdGlvbnMkaW1hZ2VTbW9vdGhpID0gb3B0aW9ucy5pbWFnZVNtb290aGluZ0VuYWJsZWQsXG4gICAgICAgIGltYWdlU21vb3RoaW5nRW5hYmxlZCA9IF9vcHRpb25zJGltYWdlU21vb3RoaSA9PT0gdm9pZCAwID8gdHJ1ZSA6IF9vcHRpb25zJGltYWdlU21vb3RoaSxcbiAgICAgICAgaW1hZ2VTbW9vdGhpbmdRdWFsaXR5ID0gb3B0aW9ucy5pbWFnZVNtb290aGluZ1F1YWxpdHk7XG4gICAgICBjb250ZXh0LmltYWdlU21vb3RoaW5nRW5hYmxlZCA9IGltYWdlU21vb3RoaW5nRW5hYmxlZDtcbiAgICAgIGlmIChpbWFnZVNtb290aGluZ1F1YWxpdHkpIHtcbiAgICAgICAgY29udGV4dC5pbWFnZVNtb290aGluZ1F1YWxpdHkgPSBpbWFnZVNtb290aGluZ1F1YWxpdHk7XG4gICAgICB9XG5cbiAgICAgIC8vIGh0dHBzOi8vZGV2ZWxvcGVyLm1vemlsbGEub3JnL2VuLVVTL2RvY3MvV2ViL0FQSS9DYW52YXNSZW5kZXJpbmdDb250ZXh0MkQuZHJhd0ltYWdlXG4gICAgICB2YXIgc291cmNlV2lkdGggPSBzb3VyY2Uud2lkdGg7XG4gICAgICB2YXIgc291cmNlSGVpZ2h0ID0gc291cmNlLmhlaWdodDtcblxuICAgICAgLy8gU291cmNlIGNhbnZhcyBwYXJhbWV0ZXJzXG4gICAgICB2YXIgc3JjWCA9IGluaXRpYWxYO1xuICAgICAgdmFyIHNyY1kgPSBpbml0aWFsWTtcbiAgICAgIHZhciBzcmNXaWR0aDtcbiAgICAgIHZhciBzcmNIZWlnaHQ7XG5cbiAgICAgIC8vIERlc3RpbmF0aW9uIGNhbnZhcyBwYXJhbWV0ZXJzXG4gICAgICB2YXIgZHN0WDtcbiAgICAgIHZhciBkc3RZO1xuICAgICAgdmFyIGRzdFdpZHRoO1xuICAgICAgdmFyIGRzdEhlaWdodDtcbiAgICAgIGlmIChzcmNYIDw9IC1pbml0aWFsV2lkdGggfHwgc3JjWCA+IHNvdXJjZVdpZHRoKSB7XG4gICAgICAgIHNyY1ggPSAwO1xuICAgICAgICBzcmNXaWR0aCA9IDA7XG4gICAgICAgIGRzdFggPSAwO1xuICAgICAgICBkc3RXaWR0aCA9IDA7XG4gICAgICB9IGVsc2UgaWYgKHNyY1ggPD0gMCkge1xuICAgICAgICBkc3RYID0gLXNyY1g7XG4gICAgICAgIHNyY1ggPSAwO1xuICAgICAgICBzcmNXaWR0aCA9IE1hdGgubWluKHNvdXJjZVdpZHRoLCBpbml0aWFsV2lkdGggKyBzcmNYKTtcbiAgICAgICAgZHN0V2lkdGggPSBzcmNXaWR0aDtcbiAgICAgIH0gZWxzZSBpZiAoc3JjWCA8PSBzb3VyY2VXaWR0aCkge1xuICAgICAgICBkc3RYID0gMDtcbiAgICAgICAgc3JjV2lkdGggPSBNYXRoLm1pbihpbml0aWFsV2lkdGgsIHNvdXJjZVdpZHRoIC0gc3JjWCk7XG4gICAgICAgIGRzdFdpZHRoID0gc3JjV2lkdGg7XG4gICAgICB9XG4gICAgICBpZiAoc3JjV2lkdGggPD0gMCB8fCBzcmNZIDw9IC1pbml0aWFsSGVpZ2h0IHx8IHNyY1kgPiBzb3VyY2VIZWlnaHQpIHtcbiAgICAgICAgc3JjWSA9IDA7XG4gICAgICAgIHNyY0hlaWdodCA9IDA7XG4gICAgICAgIGRzdFkgPSAwO1xuICAgICAgICBkc3RIZWlnaHQgPSAwO1xuICAgICAgfSBlbHNlIGlmIChzcmNZIDw9IDApIHtcbiAgICAgICAgZHN0WSA9IC1zcmNZO1xuICAgICAgICBzcmNZID0gMDtcbiAgICAgICAgc3JjSGVpZ2h0ID0gTWF0aC5taW4oc291cmNlSGVpZ2h0LCBpbml0aWFsSGVpZ2h0ICsgc3JjWSk7XG4gICAgICAgIGRzdEhlaWdodCA9IHNyY0hlaWdodDtcbiAgICAgIH0gZWxzZSBpZiAoc3JjWSA8PSBzb3VyY2VIZWlnaHQpIHtcbiAgICAgICAgZHN0WSA9IDA7XG4gICAgICAgIHNyY0hlaWdodCA9IE1hdGgubWluKGluaXRpYWxIZWlnaHQsIHNvdXJjZUhlaWdodCAtIHNyY1kpO1xuICAgICAgICBkc3RIZWlnaHQgPSBzcmNIZWlnaHQ7XG4gICAgICB9XG4gICAgICB2YXIgcGFyYW1zID0gW3NyY1gsIHNyY1ksIHNyY1dpZHRoLCBzcmNIZWlnaHRdO1xuXG4gICAgICAvLyBBdm9pZCBcIkluZGV4U2l6ZUVycm9yXCJcbiAgICAgIGlmIChkc3RXaWR0aCA+IDAgJiYgZHN0SGVpZ2h0ID4gMCkge1xuICAgICAgICB2YXIgc2NhbGUgPSB3aWR0aCAvIGluaXRpYWxXaWR0aDtcbiAgICAgICAgcGFyYW1zLnB1c2goZHN0WCAqIHNjYWxlLCBkc3RZICogc2NhbGUsIGRzdFdpZHRoICogc2NhbGUsIGRzdEhlaWdodCAqIHNjYWxlKTtcbiAgICAgIH1cblxuICAgICAgLy8gQWxsIHRoZSBudW1lcmljYWwgcGFyYW1ldGVycyBzaG91bGQgYmUgaW50ZWdlciBmb3IgYGRyYXdJbWFnZWBcbiAgICAgIC8vIGh0dHBzOi8vZ2l0aHViLmNvbS9mZW5neXVhbmNoZW4vY3JvcHBlci9pc3N1ZXMvNDc2XG4gICAgICBjb250ZXh0LmRyYXdJbWFnZS5hcHBseShjb250ZXh0LCBbc291cmNlXS5jb25jYXQoX3RvQ29uc3VtYWJsZUFycmF5KHBhcmFtcy5tYXAoZnVuY3Rpb24gKHBhcmFtKSB7XG4gICAgICAgIHJldHVybiBNYXRoLmZsb29yKG5vcm1hbGl6ZURlY2ltYWxOdW1iZXIocGFyYW0pKTtcbiAgICAgIH0pKSkpO1xuICAgICAgcmV0dXJuIGNhbnZhcztcbiAgICB9LFxuICAgIC8qKlxuICAgICAqIENoYW5nZSB0aGUgYXNwZWN0IHJhdGlvIG9mIHRoZSBjcm9wIGJveC5cbiAgICAgKiBAcGFyYW0ge251bWJlcn0gYXNwZWN0UmF0aW8gLSBUaGUgbmV3IGFzcGVjdCByYXRpby5cbiAgICAgKiBAcmV0dXJucyB7Q3JvcHBlcn0gdGhpc1xuICAgICAqL1xuICAgIHNldEFzcGVjdFJhdGlvOiBmdW5jdGlvbiBzZXRBc3BlY3RSYXRpbyhhc3BlY3RSYXRpbykge1xuICAgICAgdmFyIG9wdGlvbnMgPSB0aGlzLm9wdGlvbnM7XG4gICAgICBpZiAoIXRoaXMuZGlzYWJsZWQgJiYgIWlzVW5kZWZpbmVkKGFzcGVjdFJhdGlvKSkge1xuICAgICAgICAvLyAwIC0+IE5hTlxuICAgICAgICBvcHRpb25zLmFzcGVjdFJhdGlvID0gTWF0aC5tYXgoMCwgYXNwZWN0UmF0aW8pIHx8IE5hTjtcbiAgICAgICAgaWYgKHRoaXMucmVhZHkpIHtcbiAgICAgICAgICB0aGlzLmluaXRDcm9wQm94KCk7XG4gICAgICAgICAgaWYgKHRoaXMuY3JvcHBlZCkge1xuICAgICAgICAgICAgdGhpcy5yZW5kZXJDcm9wQm94KCk7XG4gICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICB9XG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxuICAgIC8qKlxuICAgICAqIENoYW5nZSB0aGUgZHJhZyBtb2RlLlxuICAgICAqIEBwYXJhbSB7c3RyaW5nfSBtb2RlIC0gVGhlIG5ldyBkcmFnIG1vZGUuXG4gICAgICogQHJldHVybnMge0Nyb3BwZXJ9IHRoaXNcbiAgICAgKi9cbiAgICBzZXREcmFnTW9kZTogZnVuY3Rpb24gc2V0RHJhZ01vZGUobW9kZSkge1xuICAgICAgdmFyIG9wdGlvbnMgPSB0aGlzLm9wdGlvbnMsXG4gICAgICAgIGRyYWdCb3ggPSB0aGlzLmRyYWdCb3gsXG4gICAgICAgIGZhY2UgPSB0aGlzLmZhY2U7XG4gICAgICBpZiAodGhpcy5yZWFkeSAmJiAhdGhpcy5kaXNhYmxlZCkge1xuICAgICAgICB2YXIgY3JvcHBhYmxlID0gbW9kZSA9PT0gRFJBR19NT0RFX0NST1A7XG4gICAgICAgIHZhciBtb3ZhYmxlID0gb3B0aW9ucy5tb3ZhYmxlICYmIG1vZGUgPT09IERSQUdfTU9ERV9NT1ZFO1xuICAgICAgICBtb2RlID0gY3JvcHBhYmxlIHx8IG1vdmFibGUgPyBtb2RlIDogRFJBR19NT0RFX05PTkU7XG4gICAgICAgIG9wdGlvbnMuZHJhZ01vZGUgPSBtb2RlO1xuICAgICAgICBzZXREYXRhKGRyYWdCb3gsIERBVEFfQUNUSU9OLCBtb2RlKTtcbiAgICAgICAgdG9nZ2xlQ2xhc3MoZHJhZ0JveCwgQ0xBU1NfQ1JPUCwgY3JvcHBhYmxlKTtcbiAgICAgICAgdG9nZ2xlQ2xhc3MoZHJhZ0JveCwgQ0xBU1NfTU9WRSwgbW92YWJsZSk7XG4gICAgICAgIGlmICghb3B0aW9ucy5jcm9wQm94TW92YWJsZSkge1xuICAgICAgICAgIC8vIFN5bmMgZHJhZyBtb2RlIHRvIGNyb3AgYm94IHdoZW4gaXQgaXMgbm90IG1vdmFibGVcbiAgICAgICAgICBzZXREYXRhKGZhY2UsIERBVEFfQUNUSU9OLCBtb2RlKTtcbiAgICAgICAgICB0b2dnbGVDbGFzcyhmYWNlLCBDTEFTU19DUk9QLCBjcm9wcGFibGUpO1xuICAgICAgICAgIHRvZ2dsZUNsYXNzKGZhY2UsIENMQVNTX01PVkUsIG1vdmFibGUpO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9XG4gIH07XG5cbiAgdmFyIEFub3RoZXJDcm9wcGVyID0gV0lORE9XLkNyb3BwZXI7XG4gIHZhciBDcm9wcGVyID0gLyojX19QVVJFX18qL2Z1bmN0aW9uICgpIHtcbiAgICAvKipcbiAgICAgKiBDcmVhdGUgYSBuZXcgQ3JvcHBlci5cbiAgICAgKiBAcGFyYW0ge0VsZW1lbnR9IGVsZW1lbnQgLSBUaGUgdGFyZ2V0IGVsZW1lbnQgZm9yIGNyb3BwaW5nLlxuICAgICAqIEBwYXJhbSB7T2JqZWN0fSBbb3B0aW9ucz17fV0gLSBUaGUgY29uZmlndXJhdGlvbiBvcHRpb25zLlxuICAgICAqL1xuICAgIGZ1bmN0aW9uIENyb3BwZXIoZWxlbWVudCkge1xuICAgICAgdmFyIG9wdGlvbnMgPSBhcmd1bWVudHMubGVuZ3RoID4gMSAmJiBhcmd1bWVudHNbMV0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1sxXSA6IHt9O1xuICAgICAgX2NsYXNzQ2FsbENoZWNrKHRoaXMsIENyb3BwZXIpO1xuICAgICAgaWYgKCFlbGVtZW50IHx8ICFSRUdFWFBfVEFHX05BTUUudGVzdChlbGVtZW50LnRhZ05hbWUpKSB7XG4gICAgICAgIHRocm93IG5ldyBFcnJvcignVGhlIGZpcnN0IGFyZ3VtZW50IGlzIHJlcXVpcmVkIGFuZCBtdXN0IGJlIGFuIDxpbWc+IG9yIDxjYW52YXM+IGVsZW1lbnQuJyk7XG4gICAgICB9XG4gICAgICB0aGlzLmVsZW1lbnQgPSBlbGVtZW50O1xuICAgICAgdGhpcy5vcHRpb25zID0gYXNzaWduKHt9LCBERUZBVUxUUywgaXNQbGFpbk9iamVjdChvcHRpb25zKSAmJiBvcHRpb25zKTtcbiAgICAgIHRoaXMuY3JvcHBlZCA9IGZhbHNlO1xuICAgICAgdGhpcy5kaXNhYmxlZCA9IGZhbHNlO1xuICAgICAgdGhpcy5wb2ludGVycyA9IHt9O1xuICAgICAgdGhpcy5yZWFkeSA9IGZhbHNlO1xuICAgICAgdGhpcy5yZWxvYWRpbmcgPSBmYWxzZTtcbiAgICAgIHRoaXMucmVwbGFjZWQgPSBmYWxzZTtcbiAgICAgIHRoaXMuc2l6ZWQgPSBmYWxzZTtcbiAgICAgIHRoaXMuc2l6aW5nID0gZmFsc2U7XG4gICAgICB0aGlzLmluaXQoKTtcbiAgICB9XG4gICAgX2NyZWF0ZUNsYXNzKENyb3BwZXIsIFt7XG4gICAgICBrZXk6IFwiaW5pdFwiLFxuICAgICAgdmFsdWU6IGZ1bmN0aW9uIGluaXQoKSB7XG4gICAgICAgIHZhciBlbGVtZW50ID0gdGhpcy5lbGVtZW50O1xuICAgICAgICB2YXIgdGFnTmFtZSA9IGVsZW1lbnQudGFnTmFtZS50b0xvd2VyQ2FzZSgpO1xuICAgICAgICB2YXIgdXJsO1xuICAgICAgICBpZiAoZWxlbWVudFtOQU1FU1BBQ0VdKSB7XG4gICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG4gICAgICAgIGVsZW1lbnRbTkFNRVNQQUNFXSA9IHRoaXM7XG4gICAgICAgIGlmICh0YWdOYW1lID09PSAnaW1nJykge1xuICAgICAgICAgIHRoaXMuaXNJbWcgPSB0cnVlO1xuXG4gICAgICAgICAgLy8gZS5nLjogXCJpbWcvcGljdHVyZS5qcGdcIlxuICAgICAgICAgIHVybCA9IGVsZW1lbnQuZ2V0QXR0cmlidXRlKCdzcmMnKSB8fCAnJztcbiAgICAgICAgICB0aGlzLm9yaWdpbmFsVXJsID0gdXJsO1xuXG4gICAgICAgICAgLy8gU3RvcCB3aGVuIGl0J3MgYSBibGFuayBpbWFnZVxuICAgICAgICAgIGlmICghdXJsKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgfVxuXG4gICAgICAgICAgLy8gZS5nLjogXCJodHRwczovL2V4YW1wbGUuY29tL2ltZy9waWN0dXJlLmpwZ1wiXG4gICAgICAgICAgdXJsID0gZWxlbWVudC5zcmM7XG4gICAgICAgIH0gZWxzZSBpZiAodGFnTmFtZSA9PT0gJ2NhbnZhcycgJiYgd2luZG93LkhUTUxDYW52YXNFbGVtZW50KSB7XG4gICAgICAgICAgdXJsID0gZWxlbWVudC50b0RhdGFVUkwoKTtcbiAgICAgICAgfVxuICAgICAgICB0aGlzLmxvYWQodXJsKTtcbiAgICAgIH1cbiAgICB9LCB7XG4gICAgICBrZXk6IFwibG9hZFwiLFxuICAgICAgdmFsdWU6IGZ1bmN0aW9uIGxvYWQodXJsKSB7XG4gICAgICAgIHZhciBfdGhpcyA9IHRoaXM7XG4gICAgICAgIGlmICghdXJsKSB7XG4gICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG4gICAgICAgIHRoaXMudXJsID0gdXJsO1xuICAgICAgICB0aGlzLmltYWdlRGF0YSA9IHt9O1xuICAgICAgICB2YXIgZWxlbWVudCA9IHRoaXMuZWxlbWVudCxcbiAgICAgICAgICBvcHRpb25zID0gdGhpcy5vcHRpb25zO1xuICAgICAgICBpZiAoIW9wdGlvbnMucm90YXRhYmxlICYmICFvcHRpb25zLnNjYWxhYmxlKSB7XG4gICAgICAgICAgb3B0aW9ucy5jaGVja09yaWVudGF0aW9uID0gZmFsc2U7XG4gICAgICAgIH1cblxuICAgICAgICAvLyBPbmx5IElFMTArIHN1cHBvcnRzIFR5cGVkIEFycmF5c1xuICAgICAgICBpZiAoIW9wdGlvbnMuY2hlY2tPcmllbnRhdGlvbiB8fCAhd2luZG93LkFycmF5QnVmZmVyKSB7XG4gICAgICAgICAgdGhpcy5jbG9uZSgpO1xuICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIERldGVjdCB0aGUgbWltZSB0eXBlIG9mIHRoZSBpbWFnZSBkaXJlY3RseSBpZiBpdCBpcyBhIERhdGEgVVJMXG4gICAgICAgIGlmIChSRUdFWFBfREFUQV9VUkwudGVzdCh1cmwpKSB7XG4gICAgICAgICAgLy8gUmVhZCBBcnJheUJ1ZmZlciBmcm9tIERhdGEgVVJMIG9mIEpQRUcgaW1hZ2VzIGRpcmVjdGx5IGZvciBiZXR0ZXIgcGVyZm9ybWFuY2VcbiAgICAgICAgICBpZiAoUkVHRVhQX0RBVEFfVVJMX0pQRUcudGVzdCh1cmwpKSB7XG4gICAgICAgICAgICB0aGlzLnJlYWQoZGF0YVVSTFRvQXJyYXlCdWZmZXIodXJsKSk7XG4gICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIC8vIE9ubHkgYSBKUEVHIGltYWdlIG1heSBjb250YWlucyBFeGlmIE9yaWVudGF0aW9uIGluZm9ybWF0aW9uLFxuICAgICAgICAgICAgLy8gdGhlIHJlc3QgdHlwZXMgb2YgRGF0YSBVUkxzIGFyZSBub3QgbmVjZXNzYXJ5IHRvIGNoZWNrIG9yaWVudGF0aW9uIGF0IGFsbC5cbiAgICAgICAgICAgIHRoaXMuY2xvbmUoKTtcbiAgICAgICAgICB9XG4gICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gMS4gRGV0ZWN0IHRoZSBtaW1lIHR5cGUgb2YgdGhlIGltYWdlIGJ5IGEgWE1MSHR0cFJlcXVlc3QuXG4gICAgICAgIC8vIDIuIExvYWQgdGhlIGltYWdlIGFzIEFycmF5QnVmZmVyIGZvciByZWFkaW5nIG9yaWVudGF0aW9uIGlmIGl0cyBhIEpQRUcgaW1hZ2UuXG4gICAgICAgIHZhciB4aHIgPSBuZXcgWE1MSHR0cFJlcXVlc3QoKTtcbiAgICAgICAgdmFyIGNsb25lID0gdGhpcy5jbG9uZS5iaW5kKHRoaXMpO1xuICAgICAgICB0aGlzLnJlbG9hZGluZyA9IHRydWU7XG4gICAgICAgIHRoaXMueGhyID0geGhyO1xuXG4gICAgICAgIC8vIDEuIENyb3NzIG9yaWdpbiByZXF1ZXN0cyBhcmUgb25seSBzdXBwb3J0ZWQgZm9yIHByb3RvY29sIHNjaGVtZXM6XG4gICAgICAgIC8vIGh0dHAsIGh0dHBzLCBkYXRhLCBjaHJvbWUsIGNocm9tZS1leHRlbnNpb24uXG4gICAgICAgIC8vIDIuIEFjY2VzcyB0byBYTUxIdHRwUmVxdWVzdCBmcm9tIGEgRGF0YSBVUkwgd2lsbCBiZSBibG9ja2VkIGJ5IENPUlMgcG9saWN5XG4gICAgICAgIC8vIGluIHNvbWUgYnJvd3NlcnMgYXMgSUUxMSBhbmQgU2FmYXJpLlxuICAgICAgICB4aHIub25hYm9ydCA9IGNsb25lO1xuICAgICAgICB4aHIub25lcnJvciA9IGNsb25lO1xuICAgICAgICB4aHIub250aW1lb3V0ID0gY2xvbmU7XG4gICAgICAgIHhoci5vbnByb2dyZXNzID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgIC8vIEFib3J0IHRoZSByZXF1ZXN0IGRpcmVjdGx5IGlmIGl0IG5vdCBhIEpQRUcgaW1hZ2UgZm9yIGJldHRlciBwZXJmb3JtYW5jZVxuICAgICAgICAgIGlmICh4aHIuZ2V0UmVzcG9uc2VIZWFkZXIoJ2NvbnRlbnQtdHlwZScpICE9PSBNSU1FX1RZUEVfSlBFRykge1xuICAgICAgICAgICAgeGhyLmFib3J0KCk7XG4gICAgICAgICAgfVxuICAgICAgICB9O1xuICAgICAgICB4aHIub25sb2FkID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgIF90aGlzLnJlYWQoeGhyLnJlc3BvbnNlKTtcbiAgICAgICAgfTtcbiAgICAgICAgeGhyLm9ubG9hZGVuZCA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICBfdGhpcy5yZWxvYWRpbmcgPSBmYWxzZTtcbiAgICAgICAgICBfdGhpcy54aHIgPSBudWxsO1xuICAgICAgICB9O1xuXG4gICAgICAgIC8vIEJ1c3QgY2FjaGUgd2hlbiB0aGVyZSBpcyBhIFwiY3Jvc3NPcmlnaW5cIiBwcm9wZXJ0eSB0byBhdm9pZCBicm93c2VyIGNhY2hlIGVycm9yXG4gICAgICAgIGlmIChvcHRpb25zLmNoZWNrQ3Jvc3NPcmlnaW4gJiYgaXNDcm9zc09yaWdpblVSTCh1cmwpICYmIGVsZW1lbnQuY3Jvc3NPcmlnaW4pIHtcbiAgICAgICAgICB1cmwgPSBhZGRUaW1lc3RhbXAodXJsKTtcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIFRoZSB0aGlyZCBwYXJhbWV0ZXIgaXMgcmVxdWlyZWQgZm9yIGF2b2lkaW5nIHNpZGUtZWZmZWN0ICgjNjgyKVxuICAgICAgICB4aHIub3BlbignR0VUJywgdXJsLCB0cnVlKTtcbiAgICAgICAgeGhyLnJlc3BvbnNlVHlwZSA9ICdhcnJheWJ1ZmZlcic7XG4gICAgICAgIHhoci53aXRoQ3JlZGVudGlhbHMgPSBlbGVtZW50LmNyb3NzT3JpZ2luID09PSAndXNlLWNyZWRlbnRpYWxzJztcbiAgICAgICAgeGhyLnNlbmQoKTtcbiAgICAgIH1cbiAgICB9LCB7XG4gICAgICBrZXk6IFwicmVhZFwiLFxuICAgICAgdmFsdWU6IGZ1bmN0aW9uIHJlYWQoYXJyYXlCdWZmZXIpIHtcbiAgICAgICAgdmFyIG9wdGlvbnMgPSB0aGlzLm9wdGlvbnMsXG4gICAgICAgICAgaW1hZ2VEYXRhID0gdGhpcy5pbWFnZURhdGE7XG5cbiAgICAgICAgLy8gUmVzZXQgdGhlIG9yaWVudGF0aW9uIHZhbHVlIHRvIGl0cyBkZWZhdWx0IHZhbHVlIDFcbiAgICAgICAgLy8gYXMgc29tZSBpT1MgYnJvd3NlcnMgd2lsbCByZW5kZXIgaW1hZ2Ugd2l0aCBpdHMgb3JpZW50YXRpb25cbiAgICAgICAgdmFyIG9yaWVudGF0aW9uID0gcmVzZXRBbmRHZXRPcmllbnRhdGlvbihhcnJheUJ1ZmZlcik7XG4gICAgICAgIHZhciByb3RhdGUgPSAwO1xuICAgICAgICB2YXIgc2NhbGVYID0gMTtcbiAgICAgICAgdmFyIHNjYWxlWSA9IDE7XG4gICAgICAgIGlmIChvcmllbnRhdGlvbiA+IDEpIHtcbiAgICAgICAgICAvLyBHZW5lcmF0ZSBhIG5ldyBVUkwgd2hpY2ggaGFzIHRoZSBkZWZhdWx0IG9yaWVudGF0aW9uIHZhbHVlXG4gICAgICAgICAgdGhpcy51cmwgPSBhcnJheUJ1ZmZlclRvRGF0YVVSTChhcnJheUJ1ZmZlciwgTUlNRV9UWVBFX0pQRUcpO1xuICAgICAgICAgIHZhciBfcGFyc2VPcmllbnRhdGlvbiA9IHBhcnNlT3JpZW50YXRpb24ob3JpZW50YXRpb24pO1xuICAgICAgICAgIHJvdGF0ZSA9IF9wYXJzZU9yaWVudGF0aW9uLnJvdGF0ZTtcbiAgICAgICAgICBzY2FsZVggPSBfcGFyc2VPcmllbnRhdGlvbi5zY2FsZVg7XG4gICAgICAgICAgc2NhbGVZID0gX3BhcnNlT3JpZW50YXRpb24uc2NhbGVZO1xuICAgICAgICB9XG4gICAgICAgIGlmIChvcHRpb25zLnJvdGF0YWJsZSkge1xuICAgICAgICAgIGltYWdlRGF0YS5yb3RhdGUgPSByb3RhdGU7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKG9wdGlvbnMuc2NhbGFibGUpIHtcbiAgICAgICAgICBpbWFnZURhdGEuc2NhbGVYID0gc2NhbGVYO1xuICAgICAgICAgIGltYWdlRGF0YS5zY2FsZVkgPSBzY2FsZVk7XG4gICAgICAgIH1cbiAgICAgICAgdGhpcy5jbG9uZSgpO1xuICAgICAgfVxuICAgIH0sIHtcbiAgICAgIGtleTogXCJjbG9uZVwiLFxuICAgICAgdmFsdWU6IGZ1bmN0aW9uIGNsb25lKCkge1xuICAgICAgICB2YXIgZWxlbWVudCA9IHRoaXMuZWxlbWVudCxcbiAgICAgICAgICB1cmwgPSB0aGlzLnVybDtcbiAgICAgICAgdmFyIGNyb3NzT3JpZ2luID0gZWxlbWVudC5jcm9zc09yaWdpbjtcbiAgICAgICAgdmFyIGNyb3NzT3JpZ2luVXJsID0gdXJsO1xuICAgICAgICBpZiAodGhpcy5vcHRpb25zLmNoZWNrQ3Jvc3NPcmlnaW4gJiYgaXNDcm9zc09yaWdpblVSTCh1cmwpKSB7XG4gICAgICAgICAgaWYgKCFjcm9zc09yaWdpbikge1xuICAgICAgICAgICAgY3Jvc3NPcmlnaW4gPSAnYW5vbnltb3VzJztcbiAgICAgICAgICB9XG5cbiAgICAgICAgICAvLyBCdXN0IGNhY2hlIHdoZW4gdGhlcmUgaXMgbm90IGEgXCJjcm9zc09yaWdpblwiIHByb3BlcnR5ICgjNTE5KVxuICAgICAgICAgIGNyb3NzT3JpZ2luVXJsID0gYWRkVGltZXN0YW1wKHVybCk7XG4gICAgICAgIH1cbiAgICAgICAgdGhpcy5jcm9zc09yaWdpbiA9IGNyb3NzT3JpZ2luO1xuICAgICAgICB0aGlzLmNyb3NzT3JpZ2luVXJsID0gY3Jvc3NPcmlnaW5Vcmw7XG4gICAgICAgIHZhciBpbWFnZSA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2ltZycpO1xuICAgICAgICBpZiAoY3Jvc3NPcmlnaW4pIHtcbiAgICAgICAgICBpbWFnZS5jcm9zc09yaWdpbiA9IGNyb3NzT3JpZ2luO1xuICAgICAgICB9XG4gICAgICAgIGltYWdlLnNyYyA9IGNyb3NzT3JpZ2luVXJsIHx8IHVybDtcbiAgICAgICAgaW1hZ2UuYWx0ID0gZWxlbWVudC5hbHQgfHwgJ1RoZSBpbWFnZSB0byBjcm9wJztcbiAgICAgICAgdGhpcy5pbWFnZSA9IGltYWdlO1xuICAgICAgICBpbWFnZS5vbmxvYWQgPSB0aGlzLnN0YXJ0LmJpbmQodGhpcyk7XG4gICAgICAgIGltYWdlLm9uZXJyb3IgPSB0aGlzLnN0b3AuYmluZCh0aGlzKTtcbiAgICAgICAgYWRkQ2xhc3MoaW1hZ2UsIENMQVNTX0hJREUpO1xuICAgICAgICBlbGVtZW50LnBhcmVudE5vZGUuaW5zZXJ0QmVmb3JlKGltYWdlLCBlbGVtZW50Lm5leHRTaWJsaW5nKTtcbiAgICAgIH1cbiAgICB9LCB7XG4gICAgICBrZXk6IFwic3RhcnRcIixcbiAgICAgIHZhbHVlOiBmdW5jdGlvbiBzdGFydCgpIHtcbiAgICAgICAgdmFyIF90aGlzMiA9IHRoaXM7XG4gICAgICAgIHZhciBpbWFnZSA9IHRoaXMuaW1hZ2U7XG4gICAgICAgIGltYWdlLm9ubG9hZCA9IG51bGw7XG4gICAgICAgIGltYWdlLm9uZXJyb3IgPSBudWxsO1xuICAgICAgICB0aGlzLnNpemluZyA9IHRydWU7XG5cbiAgICAgICAgLy8gTWF0Y2ggYWxsIGJyb3dzZXJzIHRoYXQgdXNlIFdlYktpdCBhcyB0aGUgbGF5b3V0IGVuZ2luZSBpbiBpT1MgZGV2aWNlcyxcbiAgICAgICAgLy8gc3VjaCBhcyBTYWZhcmkgZm9yIGlPUywgQ2hyb21lIGZvciBpT1MsIGFuZCBpbi1hcHAgYnJvd3NlcnMuXG4gICAgICAgIHZhciBpc0lPU1dlYktpdCA9IFdJTkRPVy5uYXZpZ2F0b3IgJiYgLyg/OmlQYWR8aVBob25lfGlQb2QpLio/QXBwbGVXZWJLaXQvaS50ZXN0KFdJTkRPVy5uYXZpZ2F0b3IudXNlckFnZW50KTtcbiAgICAgICAgdmFyIGRvbmUgPSBmdW5jdGlvbiBkb25lKG5hdHVyYWxXaWR0aCwgbmF0dXJhbEhlaWdodCkge1xuICAgICAgICAgIGFzc2lnbihfdGhpczIuaW1hZ2VEYXRhLCB7XG4gICAgICAgICAgICBuYXR1cmFsV2lkdGg6IG5hdHVyYWxXaWR0aCxcbiAgICAgICAgICAgIG5hdHVyYWxIZWlnaHQ6IG5hdHVyYWxIZWlnaHQsXG4gICAgICAgICAgICBhc3BlY3RSYXRpbzogbmF0dXJhbFdpZHRoIC8gbmF0dXJhbEhlaWdodFxuICAgICAgICAgIH0pO1xuICAgICAgICAgIF90aGlzMi5pbml0aWFsSW1hZ2VEYXRhID0gYXNzaWduKHt9LCBfdGhpczIuaW1hZ2VEYXRhKTtcbiAgICAgICAgICBfdGhpczIuc2l6aW5nID0gZmFsc2U7XG4gICAgICAgICAgX3RoaXMyLnNpemVkID0gdHJ1ZTtcbiAgICAgICAgICBfdGhpczIuYnVpbGQoKTtcbiAgICAgICAgfTtcblxuICAgICAgICAvLyBNb3N0IG1vZGVybiBicm93c2VycyAoZXhjZXB0cyBpT1MgV2ViS2l0KVxuICAgICAgICBpZiAoaW1hZ2UubmF0dXJhbFdpZHRoICYmICFpc0lPU1dlYktpdCkge1xuICAgICAgICAgIGRvbmUoaW1hZ2UubmF0dXJhbFdpZHRoLCBpbWFnZS5uYXR1cmFsSGVpZ2h0KTtcbiAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cbiAgICAgICAgdmFyIHNpemluZ0ltYWdlID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnaW1nJyk7XG4gICAgICAgIHZhciBib2R5ID0gZG9jdW1lbnQuYm9keSB8fCBkb2N1bWVudC5kb2N1bWVudEVsZW1lbnQ7XG4gICAgICAgIHRoaXMuc2l6aW5nSW1hZ2UgPSBzaXppbmdJbWFnZTtcbiAgICAgICAgc2l6aW5nSW1hZ2Uub25sb2FkID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgIGRvbmUoc2l6aW5nSW1hZ2Uud2lkdGgsIHNpemluZ0ltYWdlLmhlaWdodCk7XG4gICAgICAgICAgaWYgKCFpc0lPU1dlYktpdCkge1xuICAgICAgICAgICAgYm9keS5yZW1vdmVDaGlsZChzaXppbmdJbWFnZSk7XG4gICAgICAgICAgfVxuICAgICAgICB9O1xuICAgICAgICBzaXppbmdJbWFnZS5zcmMgPSBpbWFnZS5zcmM7XG5cbiAgICAgICAgLy8gaU9TIFdlYktpdCB3aWxsIGNvbnZlcnQgdGhlIGltYWdlIGF1dG9tYXRpY2FsbHlcbiAgICAgICAgLy8gd2l0aCBpdHMgb3JpZW50YXRpb24gb25jZSBhcHBlbmQgaXQgaW50byBET00gKCMyNzkpXG4gICAgICAgIGlmICghaXNJT1NXZWJLaXQpIHtcbiAgICAgICAgICBzaXppbmdJbWFnZS5zdHlsZS5jc3NUZXh0ID0gJ2xlZnQ6MDsnICsgJ21heC1oZWlnaHQ6bm9uZSFpbXBvcnRhbnQ7JyArICdtYXgtd2lkdGg6bm9uZSFpbXBvcnRhbnQ7JyArICdtaW4taGVpZ2h0OjAhaW1wb3J0YW50OycgKyAnbWluLXdpZHRoOjAhaW1wb3J0YW50OycgKyAnb3BhY2l0eTowOycgKyAncG9zaXRpb246YWJzb2x1dGU7JyArICd0b3A6MDsnICsgJ3otaW5kZXg6LTE7JztcbiAgICAgICAgICBib2R5LmFwcGVuZENoaWxkKHNpemluZ0ltYWdlKTtcbiAgICAgICAgfVxuICAgICAgfVxuICAgIH0sIHtcbiAgICAgIGtleTogXCJzdG9wXCIsXG4gICAgICB2YWx1ZTogZnVuY3Rpb24gc3RvcCgpIHtcbiAgICAgICAgdmFyIGltYWdlID0gdGhpcy5pbWFnZTtcbiAgICAgICAgaW1hZ2Uub25sb2FkID0gbnVsbDtcbiAgICAgICAgaW1hZ2Uub25lcnJvciA9IG51bGw7XG4gICAgICAgIGltYWdlLnBhcmVudE5vZGUucmVtb3ZlQ2hpbGQoaW1hZ2UpO1xuICAgICAgICB0aGlzLmltYWdlID0gbnVsbDtcbiAgICAgIH1cbiAgICB9LCB7XG4gICAgICBrZXk6IFwiYnVpbGRcIixcbiAgICAgIHZhbHVlOiBmdW5jdGlvbiBidWlsZCgpIHtcbiAgICAgICAgaWYgKCF0aGlzLnNpemVkIHx8IHRoaXMucmVhZHkpIHtcbiAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cbiAgICAgICAgdmFyIGVsZW1lbnQgPSB0aGlzLmVsZW1lbnQsXG4gICAgICAgICAgb3B0aW9ucyA9IHRoaXMub3B0aW9ucyxcbiAgICAgICAgICBpbWFnZSA9IHRoaXMuaW1hZ2U7XG5cbiAgICAgICAgLy8gQ3JlYXRlIGNyb3BwZXIgZWxlbWVudHNcbiAgICAgICAgdmFyIGNvbnRhaW5lciA9IGVsZW1lbnQucGFyZW50Tm9kZTtcbiAgICAgICAgdmFyIHRlbXBsYXRlID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2Jyk7XG4gICAgICAgIHRlbXBsYXRlLmlubmVySFRNTCA9IFRFTVBMQVRFO1xuICAgICAgICB2YXIgY3JvcHBlciA9IHRlbXBsYXRlLnF1ZXJ5U2VsZWN0b3IoXCIuXCIuY29uY2F0KE5BTUVTUEFDRSwgXCItY29udGFpbmVyXCIpKTtcbiAgICAgICAgdmFyIGNhbnZhcyA9IGNyb3BwZXIucXVlcnlTZWxlY3RvcihcIi5cIi5jb25jYXQoTkFNRVNQQUNFLCBcIi1jYW52YXNcIikpO1xuICAgICAgICB2YXIgZHJhZ0JveCA9IGNyb3BwZXIucXVlcnlTZWxlY3RvcihcIi5cIi5jb25jYXQoTkFNRVNQQUNFLCBcIi1kcmFnLWJveFwiKSk7XG4gICAgICAgIHZhciBjcm9wQm94ID0gY3JvcHBlci5xdWVyeVNlbGVjdG9yKFwiLlwiLmNvbmNhdChOQU1FU1BBQ0UsIFwiLWNyb3AtYm94XCIpKTtcbiAgICAgICAgdmFyIGZhY2UgPSBjcm9wQm94LnF1ZXJ5U2VsZWN0b3IoXCIuXCIuY29uY2F0KE5BTUVTUEFDRSwgXCItZmFjZVwiKSk7XG4gICAgICAgIHRoaXMuY29udGFpbmVyID0gY29udGFpbmVyO1xuICAgICAgICB0aGlzLmNyb3BwZXIgPSBjcm9wcGVyO1xuICAgICAgICB0aGlzLmNhbnZhcyA9IGNhbnZhcztcbiAgICAgICAgdGhpcy5kcmFnQm94ID0gZHJhZ0JveDtcbiAgICAgICAgdGhpcy5jcm9wQm94ID0gY3JvcEJveDtcbiAgICAgICAgdGhpcy52aWV3Qm94ID0gY3JvcHBlci5xdWVyeVNlbGVjdG9yKFwiLlwiLmNvbmNhdChOQU1FU1BBQ0UsIFwiLXZpZXctYm94XCIpKTtcbiAgICAgICAgdGhpcy5mYWNlID0gZmFjZTtcbiAgICAgICAgY2FudmFzLmFwcGVuZENoaWxkKGltYWdlKTtcblxuICAgICAgICAvLyBIaWRlIHRoZSBvcmlnaW5hbCBpbWFnZVxuICAgICAgICBhZGRDbGFzcyhlbGVtZW50LCBDTEFTU19ISURERU4pO1xuXG4gICAgICAgIC8vIEluc2VydHMgdGhlIGNyb3BwZXIgYWZ0ZXIgdG8gdGhlIGN1cnJlbnQgaW1hZ2VcbiAgICAgICAgY29udGFpbmVyLmluc2VydEJlZm9yZShjcm9wcGVyLCBlbGVtZW50Lm5leHRTaWJsaW5nKTtcblxuICAgICAgICAvLyBTaG93IHRoZSBoaWRkZW4gaW1hZ2VcbiAgICAgICAgcmVtb3ZlQ2xhc3MoaW1hZ2UsIENMQVNTX0hJREUpO1xuICAgICAgICB0aGlzLmluaXRQcmV2aWV3KCk7XG4gICAgICAgIHRoaXMuYmluZCgpO1xuICAgICAgICBvcHRpb25zLmluaXRpYWxBc3BlY3RSYXRpbyA9IE1hdGgubWF4KDAsIG9wdGlvbnMuaW5pdGlhbEFzcGVjdFJhdGlvKSB8fCBOYU47XG4gICAgICAgIG9wdGlvbnMuYXNwZWN0UmF0aW8gPSBNYXRoLm1heCgwLCBvcHRpb25zLmFzcGVjdFJhdGlvKSB8fCBOYU47XG4gICAgICAgIG9wdGlvbnMudmlld01vZGUgPSBNYXRoLm1heCgwLCBNYXRoLm1pbigzLCBNYXRoLnJvdW5kKG9wdGlvbnMudmlld01vZGUpKSkgfHwgMDtcbiAgICAgICAgYWRkQ2xhc3MoY3JvcEJveCwgQ0xBU1NfSElEREVOKTtcbiAgICAgICAgaWYgKCFvcHRpb25zLmd1aWRlcykge1xuICAgICAgICAgIGFkZENsYXNzKGNyb3BCb3guZ2V0RWxlbWVudHNCeUNsYXNzTmFtZShcIlwiLmNvbmNhdChOQU1FU1BBQ0UsIFwiLWRhc2hlZFwiKSksIENMQVNTX0hJRERFTik7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKCFvcHRpb25zLmNlbnRlcikge1xuICAgICAgICAgIGFkZENsYXNzKGNyb3BCb3guZ2V0RWxlbWVudHNCeUNsYXNzTmFtZShcIlwiLmNvbmNhdChOQU1FU1BBQ0UsIFwiLWNlbnRlclwiKSksIENMQVNTX0hJRERFTik7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKG9wdGlvbnMuYmFja2dyb3VuZCkge1xuICAgICAgICAgIGFkZENsYXNzKGNyb3BwZXIsIFwiXCIuY29uY2F0KE5BTUVTUEFDRSwgXCItYmdcIikpO1xuICAgICAgICB9XG4gICAgICAgIGlmICghb3B0aW9ucy5oaWdobGlnaHQpIHtcbiAgICAgICAgICBhZGRDbGFzcyhmYWNlLCBDTEFTU19JTlZJU0lCTEUpO1xuICAgICAgICB9XG4gICAgICAgIGlmIChvcHRpb25zLmNyb3BCb3hNb3ZhYmxlKSB7XG4gICAgICAgICAgYWRkQ2xhc3MoZmFjZSwgQ0xBU1NfTU9WRSk7XG4gICAgICAgICAgc2V0RGF0YShmYWNlLCBEQVRBX0FDVElPTiwgQUNUSU9OX0FMTCk7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKCFvcHRpb25zLmNyb3BCb3hSZXNpemFibGUpIHtcbiAgICAgICAgICBhZGRDbGFzcyhjcm9wQm94LmdldEVsZW1lbnRzQnlDbGFzc05hbWUoXCJcIi5jb25jYXQoTkFNRVNQQUNFLCBcIi1saW5lXCIpKSwgQ0xBU1NfSElEREVOKTtcbiAgICAgICAgICBhZGRDbGFzcyhjcm9wQm94LmdldEVsZW1lbnRzQnlDbGFzc05hbWUoXCJcIi5jb25jYXQoTkFNRVNQQUNFLCBcIi1wb2ludFwiKSksIENMQVNTX0hJRERFTik7XG4gICAgICAgIH1cbiAgICAgICAgdGhpcy5yZW5kZXIoKTtcbiAgICAgICAgdGhpcy5yZWFkeSA9IHRydWU7XG4gICAgICAgIHRoaXMuc2V0RHJhZ01vZGUob3B0aW9ucy5kcmFnTW9kZSk7XG4gICAgICAgIGlmIChvcHRpb25zLmF1dG9Dcm9wKSB7XG4gICAgICAgICAgdGhpcy5jcm9wKCk7XG4gICAgICAgIH1cbiAgICAgICAgdGhpcy5zZXREYXRhKG9wdGlvbnMuZGF0YSk7XG4gICAgICAgIGlmIChpc0Z1bmN0aW9uKG9wdGlvbnMucmVhZHkpKSB7XG4gICAgICAgICAgYWRkTGlzdGVuZXIoZWxlbWVudCwgRVZFTlRfUkVBRFksIG9wdGlvbnMucmVhZHksIHtcbiAgICAgICAgICAgIG9uY2U6IHRydWVcbiAgICAgICAgICB9KTtcbiAgICAgICAgfVxuICAgICAgICBkaXNwYXRjaEV2ZW50KGVsZW1lbnQsIEVWRU5UX1JFQURZKTtcbiAgICAgIH1cbiAgICB9LCB7XG4gICAgICBrZXk6IFwidW5idWlsZFwiLFxuICAgICAgdmFsdWU6IGZ1bmN0aW9uIHVuYnVpbGQoKSB7XG4gICAgICAgIGlmICghdGhpcy5yZWFkeSkge1xuICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuICAgICAgICB0aGlzLnJlYWR5ID0gZmFsc2U7XG4gICAgICAgIHRoaXMudW5iaW5kKCk7XG4gICAgICAgIHRoaXMucmVzZXRQcmV2aWV3KCk7XG4gICAgICAgIHZhciBwYXJlbnROb2RlID0gdGhpcy5jcm9wcGVyLnBhcmVudE5vZGU7XG4gICAgICAgIGlmIChwYXJlbnROb2RlKSB7XG4gICAgICAgICAgcGFyZW50Tm9kZS5yZW1vdmVDaGlsZCh0aGlzLmNyb3BwZXIpO1xuICAgICAgICB9XG4gICAgICAgIHJlbW92ZUNsYXNzKHRoaXMuZWxlbWVudCwgQ0xBU1NfSElEREVOKTtcbiAgICAgIH1cbiAgICB9LCB7XG4gICAgICBrZXk6IFwidW5jcmVhdGVcIixcbiAgICAgIHZhbHVlOiBmdW5jdGlvbiB1bmNyZWF0ZSgpIHtcbiAgICAgICAgaWYgKHRoaXMucmVhZHkpIHtcbiAgICAgICAgICB0aGlzLnVuYnVpbGQoKTtcbiAgICAgICAgICB0aGlzLnJlYWR5ID0gZmFsc2U7XG4gICAgICAgICAgdGhpcy5jcm9wcGVkID0gZmFsc2U7XG4gICAgICAgIH0gZWxzZSBpZiAodGhpcy5zaXppbmcpIHtcbiAgICAgICAgICB0aGlzLnNpemluZ0ltYWdlLm9ubG9hZCA9IG51bGw7XG4gICAgICAgICAgdGhpcy5zaXppbmcgPSBmYWxzZTtcbiAgICAgICAgICB0aGlzLnNpemVkID0gZmFsc2U7XG4gICAgICAgIH0gZWxzZSBpZiAodGhpcy5yZWxvYWRpbmcpIHtcbiAgICAgICAgICB0aGlzLnhoci5vbmFib3J0ID0gbnVsbDtcbiAgICAgICAgICB0aGlzLnhoci5hYm9ydCgpO1xuICAgICAgICB9IGVsc2UgaWYgKHRoaXMuaW1hZ2UpIHtcbiAgICAgICAgICB0aGlzLnN0b3AoKTtcbiAgICAgICAgfVxuICAgICAgfVxuXG4gICAgICAvKipcbiAgICAgICAqIEdldCB0aGUgbm8gY29uZmxpY3QgY3JvcHBlciBjbGFzcy5cbiAgICAgICAqIEByZXR1cm5zIHtDcm9wcGVyfSBUaGUgY3JvcHBlciBjbGFzcy5cbiAgICAgICAqL1xuICAgIH1dLCBbe1xuICAgICAga2V5OiBcIm5vQ29uZmxpY3RcIixcbiAgICAgIHZhbHVlOiBmdW5jdGlvbiBub0NvbmZsaWN0KCkge1xuICAgICAgICB3aW5kb3cuQ3JvcHBlciA9IEFub3RoZXJDcm9wcGVyO1xuICAgICAgICByZXR1cm4gQ3JvcHBlcjtcbiAgICAgIH1cblxuICAgICAgLyoqXG4gICAgICAgKiBDaGFuZ2UgdGhlIGRlZmF1bHQgb3B0aW9ucy5cbiAgICAgICAqIEBwYXJhbSB7T2JqZWN0fSBvcHRpb25zIC0gVGhlIG5ldyBkZWZhdWx0IG9wdGlvbnMuXG4gICAgICAgKi9cbiAgICB9LCB7XG4gICAgICBrZXk6IFwic2V0RGVmYXVsdHNcIixcbiAgICAgIHZhbHVlOiBmdW5jdGlvbiBzZXREZWZhdWx0cyhvcHRpb25zKSB7XG4gICAgICAgIGFzc2lnbihERUZBVUxUUywgaXNQbGFpbk9iamVjdChvcHRpb25zKSAmJiBvcHRpb25zKTtcbiAgICAgIH1cbiAgICB9XSk7XG4gICAgcmV0dXJuIENyb3BwZXI7XG4gIH0oKTtcbiAgYXNzaWduKENyb3BwZXIucHJvdG90eXBlLCByZW5kZXIsIHByZXZpZXcsIGV2ZW50cywgaGFuZGxlcnMsIGNoYW5nZSwgbWV0aG9kcyk7XG5cbiAgcmV0dXJuIENyb3BwZXI7XG5cbn0pKTtcbiIsICJpbXBvcnQgQ3JvcHBlciBmcm9tICdjcm9wcGVyanMnXG5cbmRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoXCJhbHBpbmU6aW5pdFwiLCAoKSA9PiB7XG4gICAgQWxwaW5lLmRhdGEoJ2N1cmF0b3InLCAoe1xuICAgICAgICBzdGF0ZVBhdGgsXG4gICAgICAgIHR5cGVzLFxuICAgICAgICBpbml0aWFsU2VsZWN0aW9uID0gbnVsbFxuICAgIH0pID0+ICh7XG4gICAgICAgIHN0YXRlUGF0aCxcbiAgICAgICAgdHlwZXMsXG4gICAgICAgIHNlbGVjdGVkOiBbXSxcbiAgICAgICAgZmlsZXM6IFtdLFxuICAgICAgICBuZXh0UGFnZVVybDogbnVsbCxcbiAgICAgICAgaXNGZXRjaGluZzogZmFsc2UsXG4gICAgICAgIGFzeW5jIGluaXQoKSB7XG4gICAgICAgICAgICBhd2FpdCB0aGlzLmdldEZpbGVzKCcvY3VyYXRvci9tZWRpYScsIGluaXRpYWxTZWxlY3Rpb24/LmlkKTtcbiAgICAgICAgICAgIGNvbnN0IG9ic2VydmVyID0gbmV3IEludGVyc2VjdGlvbk9ic2VydmVyKFxuICAgICAgICAgICAgICAgIChbZV0pID0+IHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKGUuaXNJbnRlcnNlY3RpbmcpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMubG9hZE1vcmVGaWxlcygpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIHJvb3RNYXJnaW46ICcwcHgnLFxuICAgICAgICAgICAgICAgICAgICB0aHJlc2hvbGQ6IFswXSxcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICApO1xuICAgICAgICAgICAgb2JzZXJ2ZXIub2JzZXJ2ZSh0aGlzLiRyZWZzLmxvYWRNb3JlKTtcbiAgICAgICAgICAgIGlmIChpbml0aWFsU2VsZWN0aW9uKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5zZXRTZWxlY3RlZChpbml0aWFsU2VsZWN0aW9uLmlkKVxuICAgICAgICAgICAgfVxuICAgICAgICB9LFxuICAgICAgICBnZXRGaWxlczogYXN5bmMgZnVuY3Rpb24odXJsID0gJy9jdXJhdG9yL21lZGlhJywgc2VsZWN0ZWQgPSBudWxsKSB7XG4gICAgICAgICAgICBpZiAoc2VsZWN0ZWQpIHtcbiAgICAgICAgICAgICAgICBsZXQgaW5kaWNhdG9yID0gdXJsLmluY2x1ZGVzKCc/JykgPyAnJicgOiAnPyc7XG4gICAgICAgICAgICAgICAgdXJsID0gdXJsICsgaW5kaWNhdG9yICsgJ21lZGlhX2lkPScgKyBzZWxlY3RlZDtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHRoaXMuaXNGZXRjaGluZyA9IHRydWU7XG4gICAgICAgICAgICBjb25zdCByZXNwb25zZSA9IGF3YWl0IGZldGNoKHVybCk7XG4gICAgICAgICAgICBjb25zdCByZXN1bHQgPSBhd2FpdCByZXNwb25zZS5qc29uKCk7XG4gICAgICAgICAgICB0aGlzLmZpbGVzID0gdGhpcy5maWxlcyA/IHRoaXMuZmlsZXMuY29uY2F0KHJlc3VsdC5kYXRhKSA6IHJlc3VsdC5kYXRhO1xuICAgICAgICAgICAgdGhpcy5uZXh0UGFnZVVybCA9IHJlc3VsdC5uZXh0X3BhZ2VfdXJsO1xuICAgICAgICAgICAgdGhpcy5pc0ZldGNoaW5nID0gZmFsc2U7XG4gICAgICAgIH0sXG4gICAgICAgIGxvYWRNb3JlRmlsZXM6IGFzeW5jIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgaWYgKHRoaXMubmV4dFBhZ2VVcmwpIHtcbiAgICAgICAgICAgICAgICB0aGlzLmlzRmV0Y2hpbmcgPSB0cnVlO1xuICAgICAgICAgICAgICAgIGF3YWl0IHRoaXMuZ2V0RmlsZXModGhpcy5uZXh0UGFnZVVybCwgdGhpcy5zZWxlY3RlZD8uaWQpO1xuICAgICAgICAgICAgICAgIHRoaXMuaXNGZXRjaGluZyA9IGZhbHNlO1xuICAgICAgICAgICAgfVxuICAgICAgICB9LFxuICAgICAgICBzZWFyY2hGaWxlczogYXN5bmMgZnVuY3Rpb24oZXZlbnQpIHtcbiAgICAgICAgICAgIHRoaXMuaXNGZXRjaGluZyA9IHRydWU7XG4gICAgICAgICAgICBjb25zdCByZXNwb25zZSA9IGF3YWl0IGZldGNoKCcvY3VyYXRvci9tZWRpYS9zZWFyY2g/cT0nICsgZXZlbnQudGFyZ2V0LnZhbHVlKTtcbiAgICAgICAgICAgIGNvbnN0IHJlc3VsdCA9IGF3YWl0IHJlc3BvbnNlLmpzb24oKTtcbiAgICAgICAgICAgIHRoaXMuZmlsZXMgPSByZXN1bHQuZGF0YTtcbiAgICAgICAgICAgIHRoaXMuaXNGZXRjaGluZyA9IGZhbHNlO1xuICAgICAgICB9LFxuICAgICAgICBhZGROZXdGaWxlOiBmdW5jdGlvbihtZWRpYSA9IG51bGwpIHtcbiAgICAgICAgICAgIGlmIChtZWRpYSkge1xuICAgICAgICAgICAgICAgIHRoaXMuZmlsZXMgPSBbLi4ubWVkaWEsIC4uLnRoaXMuZmlsZXNdO1xuICAgICAgICAgICAgICAgIHRoaXMuJG5leHRUaWNrKCgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5zZXRTZWxlY3RlZChtZWRpYVswXS5pZCk7XG4gICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgcmVtb3ZlRmlsZTogZnVuY3Rpb24obWVkaWEgPSBudWxsKSB7XG4gICAgICAgICAgICBpZiAobWVkaWEpIHtcbiAgICAgICAgICAgICAgICB0aGlzLmZpbGVzID0gdGhpcy5maWxlcy5maWx0ZXIoKG9iaikgPT4gb2JqLmlkICE9PSBtZWRpYS5pZCk7XG4gICAgICAgICAgICAgICAgdGhpcy5zZWxlY3RlZCA9IG51bGw7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG4gICAgICAgIHNldFNlbGVjdGVkOiBmdW5jdGlvbihtZWRpYUlkID0gbnVsbCkge1xuICAgICAgICAgICAgaWYgKCFtZWRpYUlkIHx8ICh0aGlzLnNlbGVjdGVkICYmIHRoaXMuc2VsZWN0ZWQuaWQgPT09IG1lZGlhSWQpKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5zZWxlY3RlZCA9IG51bGw7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIHRoaXMuc2VsZWN0ZWQgPSB0aGlzLmZpbGVzLmZpbmQob2JqID0+IG9iai5pZCA9PT0gbWVkaWFJZCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHRoaXMuJHdpcmUuc2V0Q3VycmVudEZpbGUodGhpcy5zZWxlY3RlZCk7XG4gICAgICAgIH0sXG4gICAgfSkpO1xuXG4gICAgQWxwaW5lLmRhdGEoJ2N1cmF0aW9uJywgKHsgc3RhdGVQYXRoLCBmaWxlTmFtZSwgZmlsZVR5cGUsIHByZXNldHMgPSB7fX0pID0+ICh7XG4gICAgICAgIHN0YXRlUGF0aDogc3RhdGVQYXRoLFxuICAgICAgICBmaWxlbmFtZTogZmlsZU5hbWUsXG4gICAgICAgIGZpbGV0eXBlOiBmaWxlVHlwZSxcbiAgICAgICAgY3JvcHBlcjogbnVsbCxcbiAgICAgICAgcHJlc2V0czogcHJlc2V0cyxcbiAgICAgICAgcHJlc2V0OiAnY3VzdG9tJyxcbiAgICAgICAgZmxpcHBlZEhvcml6b250YWxseTogZmFsc2UsXG4gICAgICAgIGZsaXBwZWRWZXJ0aWNhbGx5OiBmYWxzZSxcbiAgICAgICAgZm9ybWF0OiAnanBnJyxcbiAgICAgICAgcXVhbGl0eTogIDYwLFxuICAgICAgICBrZXk6IG51bGwsXG4gICAgICAgIGZpbmFsV2lkdGg6IDAsXG4gICAgICAgIGZpbmFsSGVpZ2h0OiAwLFxuICAgICAgICBjcm9wQm94RGF0YToge1xuICAgICAgICAgICAgbGVmdDogMCxcbiAgICAgICAgICAgIHRvcDogMCxcbiAgICAgICAgICAgIHdpZHRoOiAwLFxuICAgICAgICAgICAgaGVpZ2h0OiAwLFxuICAgICAgICB9LFxuICAgICAgICBkYXRhOiB7XG4gICAgICAgICAgICBsZWZ0OiAwLFxuICAgICAgICAgICAgdG9wOiAwLFxuICAgICAgICAgICAgd2lkdGg6IDAsXG4gICAgICAgICAgICBoZWlnaHQ6IDAsXG4gICAgICAgICAgICByb3RhdGU6IDAsXG4gICAgICAgICAgICBzY2FsZVg6IDEsXG4gICAgICAgICAgICBzY2FsZVk6IDEsXG4gICAgICAgIH0sXG4gICAgICAgIGluaXQoKSB7XG4gICAgICAgICAgICB0aGlzLmRlc3Ryb3koKTtcblxuICAgICAgICAgICAgc2V0VGltZW91dCgoKSA9PiB7XG4gICAgICAgICAgICAgICAgdGhpcy5jcm9wcGVyID0gbmV3IENyb3BwZXIodGhpcy4kcmVmcy5pbWFnZSwge1xuICAgICAgICAgICAgICAgICAgICBiYWNrZ3JvdW5kOiBmYWxzZSxcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH0sIDEwMCk7XG4gICAgICAgICAgICBcbiAgICAgICAgICAgIHRoaXMuJHdhdGNoKCdwcmVzZXQnLCAoJHZhbHVlKSA9PiB7XG4gICAgICAgICAgICAgICAgaWYgKCR2YWx1ZSA9PT0gJ2N1c3RvbScpIHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5jcm9wcGVyLnJlc2V0KClcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5rZXkgPSBudWxsO1xuICAgICAgICAgICAgICAgICAgICB0aGlzLmZvcm1hdCA9ICdqcGcnO1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnF1YWxpdHkgPSA2MDtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICBsZXQgY29udGFpbmVyRGF0YSA9IHRoaXMuY3JvcHBlci5nZXRDb250YWluZXJEYXRhKCk7XG4gICAgICAgICAgICAgICAgICAgIGxldCBjcm9wQm94RGF0YSA9IHRoaXMuY3JvcHBlci5nZXRDcm9wQm94RGF0YSgpO1xuICAgICAgICAgICAgICAgICAgICBsZXQgcHJlc2V0ID0gdGhpcy5wcmVzZXRzLmZpbmQoKHApID0+IHAua2V5ID09PSAkdmFsdWUpXG4gICAgICAgICAgICAgICAgICAgIGxldCB3aWR0aCA9IHByZXNldC53aWR0aDtcbiAgICAgICAgICAgICAgICAgICAgbGV0IGhlaWdodCA9IHByZXNldC5oZWlnaHQ7XG4gICAgICAgICAgICAgICAgICAgIGxldCBsZWZ0ID0gTWF0aC5yb3VuZCgoY29udGFpbmVyRGF0YS53aWR0aCAtIHdpZHRoKSAvIDIpO1xuICAgICAgICAgICAgICAgICAgICBsZXQgdG9wID0gTWF0aC5yb3VuZCgoY29udGFpbmVyRGF0YS5oZWlnaHQgLSBoZWlnaHQpIC8gMik7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuY3JvcHBlci5zZXRDcm9wQm94RGF0YSh7Li4uY3JvcEJveERhdGEsIGxlZnQsIHRvcCwgd2lkdGgsIGhlaWdodH0pO1xuICAgICAgICAgICAgICAgICAgICB0aGlzLmtleSA9IHByZXNldC5rZXk7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuZm9ybWF0ID0gcHJlc2V0LmZvcm1hdDtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5xdWFsaXR5ID0gcHJlc2V0LnF1YWxpdHk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSlcbiAgICAgICAgfSxcbiAgICAgICAgZGVzdHJveSgpIHtcbiAgICAgICAgICAgIGlmICh0aGlzLmNyb3BwZXIgPT0gbnVsbCkgcmV0dXJuO1xuICAgICAgICAgICAgdGhpcy5jcm9wcGVyLmRlc3Ryb3koKTtcbiAgICAgICAgICAgIHRoaXMuY3JvcHBlciA9IG51bGw7XG4gICAgICAgIH0sXG4gICAgICAgIHNldERhdGEoKSB7XG4gICAgICAgICAgICB0aGlzLmZpbmFsV2lkdGggPSB0aGlzLmRhdGEud2lkdGg7XG4gICAgICAgICAgICB0aGlzLmZpbmFsSGVpZ2h0ID0gdGhpcy5kYXRhLmhlaWdodDtcbiAgICAgICAgICAgIHRoaXMuZGF0YSA9IHRoaXMuY3JvcHBlci5nZXREYXRhKHRydWUpO1xuICAgICAgICAgICAgdGhpcy5jcm9wQm94RGF0YSA9IHRoaXMuY3JvcHBlci5nZXRDcm9wQm94RGF0YSgpO1xuICAgICAgICB9LFxuICAgICAgICB1cGRhdGVEYXRhKCkge1xuICAgICAgICAgICAgdGhpcy5maW5hbFdpZHRoID0gdGhpcy5kYXRhLndpZHRoO1xuICAgICAgICAgICAgdGhpcy5maW5hbEhlaWdodCA9IHRoaXMuZGF0YS5oZWlnaHQ7XG4gICAgICAgICAgICB0aGlzLmRhdGEgPSB0aGlzLmNyb3BwZXIuZ2V0RGF0YSh0cnVlKTtcbiAgICAgICAgICAgIHRoaXMuY3JvcEJveERhdGEgPSB0aGlzLmNyb3BwZXIuZ2V0Q3JvcEJveERhdGEoKTtcbiAgICAgICAgfSxcbiAgICAgICAgc2V0Q3JvcEJveFgoJGV2ZW50KSB7XG4gICAgICAgICAgICBsZXQgY3VycmVudENyb3BCb3ggPSB0aGlzLmNyb3BwZXIuZ2V0Q3JvcEJveERhdGEoKTtcbiAgICAgICAgICAgIHRoaXMuY3JvcHBlci5zZXRDcm9wQm94RGF0YSh7Li4uY3VycmVudENyb3BCb3gsIGxlZnQ6IHBhcnNlSW50KCRldmVudC50YXJnZXQudmFsdWUpfSlcbiAgICAgICAgfSxcbiAgICAgICAgc2V0Q3JvcEJveFkoJGV2ZW50KSB7XG4gICAgICAgICAgICBsZXQgY3VycmVudENyb3BCb3ggPSB0aGlzLmNyb3BwZXIuZ2V0Q3JvcEJveERhdGEoKTtcbiAgICAgICAgICAgIHRoaXMuY3JvcHBlci5zZXRDcm9wQm94RGF0YSh7Li4uY3VycmVudENyb3BCb3gsIHRvcDogcGFyc2VJbnQoJGV2ZW50LnRhcmdldC52YWx1ZSl9KVxuICAgICAgICB9LFxuICAgICAgICBzZXRDcm9wQm94V2lkdGgoJGV2ZW50KSB7XG4gICAgICAgICAgICBsZXQgY3VycmVudENyb3BCb3ggPSB0aGlzLmNyb3BwZXIuZ2V0Q3JvcEJveERhdGEoKTtcbiAgICAgICAgICAgIHRoaXMuY3JvcHBlci5zZXRDcm9wQm94RGF0YSh7Li4uY3VycmVudENyb3BCb3gsIHdpZHRoOiBwYXJzZUludCgkZXZlbnQudGFyZ2V0LnZhbHVlKX0pXG4gICAgICAgIH0sXG4gICAgICAgIHNldENyb3BCb3hIZWlnaHQoJGV2ZW50KSB7XG4gICAgICAgICAgICBsZXQgY3VycmVudENyb3BCb3ggPSB0aGlzLmNyb3BwZXIuZ2V0Q3JvcEJveERhdGEoKTtcbiAgICAgICAgICAgIHRoaXMuY3JvcHBlci5zZXRDcm9wQm94RGF0YSh7Li4uY3VycmVudENyb3BCb3gsIGhlaWdodDogcGFyc2VJbnQoJGV2ZW50LnRhcmdldC52YWx1ZSl9KVxuICAgICAgICB9LFxuICAgICAgICBmbGlwSG9yaXpvbnRhbGx5KClcbiAgICAgICAge1xuICAgICAgICAgICAgdGhpcy5jcm9wcGVyLnNjYWxlWCh0aGlzLmZsaXBwZWRIb3Jpem9udGFsbHkgPyAxIDogLTEpO1xuICAgICAgICAgICAgdGhpcy5mbGlwcGVkSG9yaXpvbnRhbGx5ID0gISB0aGlzLmZsaXBwZWRIb3Jpem9udGFsbHlcbiAgICAgICAgfSxcbiAgICAgICAgZmxpcFZlcnRpY2FsbHkoKVxuICAgICAgICB7XG4gICAgICAgICAgICB0aGlzLmNyb3BwZXIuc2NhbGVZKHRoaXMuZmxpcHBlZFZlcnRpY2FsbHkgPyAxIDogLTEpO1xuICAgICAgICAgICAgdGhpcy5mbGlwcGVkVmVydGljYWxseSA9ICEgdGhpcy5mbGlwcGVkVmVydGljYWxseVxuICAgICAgICB9LFxuICAgICAgICBzYXZlQ3VyYXRpb24oKSB7XG4gICAgICAgICAgICBsZXQgZGF0YSA9IHRoaXMuY3JvcHBlci5nZXREYXRhKHRydWUpO1xuICAgICAgICAgICAgZGF0YSA9IHtcbiAgICAgICAgICAgICAgICAuLi5kYXRhLFxuICAgICAgICAgICAgICAgIGNvbnRhaW5lckRhdGE6IHRoaXMuY3JvcHBlci5nZXRDb250YWluZXJEYXRhKCksXG4gICAgICAgICAgICAgICAgaW1hZ2VEYXRhOiB0aGlzLmNyb3BwZXIuZ2V0SW1hZ2VEYXRhKCksXG4gICAgICAgICAgICAgICAgY2FudmFzRGF0YTogdGhpcy5jcm9wcGVyLmdldENhbnZhc0RhdGEoKSxcbiAgICAgICAgICAgICAgICBjcm9wcGVkQ2FudmFzRGF0YTogdGhpcy5jcm9wcGVyLmdldENyb3BwZWRDYW52YXMoKSxcbiAgICAgICAgICAgICAgICBmb3JtYXQ6IHRoaXMuZm9ybWF0LFxuICAgICAgICAgICAgICAgIHF1YWxpdHk6IHRoaXMucXVhbGl0eSxcbiAgICAgICAgICAgICAgICBwcmVzZXQ6IHRoaXMucHJlc2V0LFxuICAgICAgICAgICAgICAgIGtleTogdGhpcy5rZXkgPz8gdGhpcy5wcmVzZXQsXG4gICAgICAgICAgICB9XG4gICAgICAgICAgICB0aGlzLiR3aXJlLnNhdmVDdXJhdGlvbihkYXRhKTtcbiAgICAgICAgfSxcbiAgICB9KSk7XG59KVxuIl0sCiAgIm1hcHBpbmdzIjogIjs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBVUEsSUFBQyxVQUFVLFFBQVEsU0FBUztBQUMxQixhQUFPLFlBQVksWUFBWSxPQUFPLFdBQVcsY0FBYyxPQUFPLFVBQVUsWUFDaEYsT0FBTyxXQUFXLGNBQWMsT0FBTyxNQUFNLE9BQU8sV0FDbkQsVUFBUyxPQUFPLGVBQWUsY0FBYyxhQUFhLFVBQVUsTUFBTSxPQUFPLFVBQVU7QUFBQSxPQUMzRixTQUFPLFdBQVk7QUFBRTtBQUV0Qix1QkFBaUIsUUFBUSxnQkFBZ0I7QUFDdkMsWUFBSSxPQUFPLE9BQU8sS0FBSztBQUN2QixZQUFJLE9BQU8sdUJBQXVCO0FBQ2hDLGNBQUksVUFBVSxPQUFPLHNCQUFzQjtBQUMzQyw0QkFBbUIs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
