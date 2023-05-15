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
      showEditForm: false,
      showUploadForm: true,
      async init() {
        await this.getFiles("/curator/media", initialSelection);
        const observer = new IntersectionObserver(([e]) => {
          if (e.isIntersecting) {
            this.loadMoreFiles();
          }
        }, {
          rootMargin: "0px",
          threshold: [0]
        });
        observer.observe(this.$refs.loadMore);
        this.$watch("selected", (value) => {
          if (value.length === 1) {
            this.$wire.setSelection(value);
            this.showEditForm = true;
            this.showUploadForm = false;
          } else if (value.length > 1) {
            this.showEditForm = false;
            this.showUploadForm = false;
          } else {
            this.showEditForm = false;
            this.showUploadForm = true;
          }
        });
        if (initialSelection?.length > 0) {
          this.selected = initialSelection;
        }
      },
      getFiles: async function(url = "/curator/media", selected = []) {
        if (selected.length > 0) {
          let indicator = url.includes("?") ? "&" : "?";
          url = url + indicator + "media=" + selected.map((obj) => obj.id).join(",");
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
            this.addToSelection(media[0].id);
          });
        }
      },
      removeFile: function(media = null) {
        if (media) {
          this.files = this.files.filter((obj) => obj.id !== media.id);
          this.removeFromSelection(media.id);
        }
      },
      addToSelection: function(mediaId = null) {
        this.selected.push(this.files.find((obj) => obj.id === mediaId));
      },
      removeFromSelection: function(mediaId = null) {
        this.selected = this.selected.filter((obj) => obj.id !== mediaId);
      },
      isSelected: function(mediaId = null) {
        if (this.selected.length === 0)
          return false;
        return this.selected.find((obj) => obj.id === mediaId) !== void 0;
      },
      insertMedia: function() {
        this.$dispatch("insert-media", {
          statePath: this.statePath,
          media: this.selected
        });
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
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsiLi4vLi4vbm9kZV9tb2R1bGVzL2Nyb3BwZXJqcy9kaXN0L2Nyb3BwZXIuanMiLCAiLi4vanMvcGx1Z2luLmpzIl0sCiAgInNvdXJjZXNDb250ZW50IjogWyIvKiFcbiAqIENyb3BwZXIuanMgdjEuNS4xM1xuICogaHR0cHM6Ly9mZW5neXVhbmNoZW4uZ2l0aHViLmlvL2Nyb3BwZXJqc1xuICpcbiAqIENvcHlyaWdodCAyMDE1LXByZXNlbnQgQ2hlbiBGZW5neXVhblxuICogUmVsZWFzZWQgdW5kZXIgdGhlIE1JVCBsaWNlbnNlXG4gKlxuICogRGF0ZTogMjAyMi0xMS0yMFQwNTozMDo0Ni4xMTRaXG4gKi9cblxuKGZ1bmN0aW9uIChnbG9iYWwsIGZhY3RvcnkpIHtcbiAgdHlwZW9mIGV4cG9ydHMgPT09ICdvYmplY3QnICYmIHR5cGVvZiBtb2R1bGUgIT09ICd1bmRlZmluZWQnID8gbW9kdWxlLmV4cG9ydHMgPSBmYWN0b3J5KCkgOlxuICB0eXBlb2YgZGVmaW5lID09PSAnZnVuY3Rpb24nICYmIGRlZmluZS5hbWQgPyBkZWZpbmUoZmFjdG9yeSkgOlxuICAoZ2xvYmFsID0gdHlwZW9mIGdsb2JhbFRoaXMgIT09ICd1bmRlZmluZWQnID8gZ2xvYmFsVGhpcyA6IGdsb2JhbCB8fCBzZWxmLCBnbG9iYWwuQ3JvcHBlciA9IGZhY3RvcnkoKSk7XG59KSh0aGlzLCAoZnVuY3Rpb24gKCkgeyAndXNlIHN0cmljdCc7XG5cbiAgZnVuY3Rpb24gb3duS2V5cyhvYmplY3QsIGVudW1lcmFibGVPbmx5KSB7XG4gICAgdmFyIGtleXMgPSBPYmplY3Qua2V5cyhvYmplY3QpO1xuICAgIGlmIChPYmplY3QuZ2V0T3duUHJvcGVydHlTeW1ib2xzKSB7XG4gICAgICB2YXIgc3ltYm9scyA9IE9iamVjdC5nZXRPd25Qcm9wZXJ0eVN5bWJvbHMob2JqZWN0KTtcbiAgICAgIGVudW1lcmFibGVPbmx5ICYmIChzeW1ib2xzID0gc3ltYm9scy5maWx0ZXIoZnVuY3Rpb24gKHN5bSkge1xuICAgICAgICByZXR1cm4gT2JqZWN0LmdldE93blByb3BlcnR5RGVzY3JpcHRvcihvYmplY3QsIHN5bSkuZW51bWVyYWJsZTtcbiAgICAgIH0pKSwga2V5cy5wdXNoLmFwcGx5KGtleXMsIHN5bWJvbHMpO1xuICAgIH1cbiAgICByZXR1cm4ga2V5cztcbiAgfVxuICBmdW5jdGlvbiBfb2JqZWN0U3ByZWFkMih0YXJnZXQpIHtcbiAgICBmb3IgKHZhciBpID0gMTsgaSA8IGFyZ3VtZW50cy5sZW5ndGg7IGkrKykge1xuICAgICAgdmFyIHNvdXJjZSA9IG51bGwgIT0gYXJndW1lbnRzW2ldID8gYXJndW1lbnRzW2ldIDoge307XG4gICAgICBpICUgMiA/IG93bktleXMoT2JqZWN0KHNvdXJjZSksICEwKS5mb3JFYWNoKGZ1bmN0aW9uIChrZXkpIHtcbiAgICAgICAgX2RlZmluZVByb3BlcnR5KHRhcmdldCwga2V5LCBzb3VyY2Vba2V5XSk7XG4gICAgICB9KSA6IE9iamVjdC5nZXRPd25Qcm9wZXJ0eURlc2NyaXB0b3JzID8gT2JqZWN0LmRlZmluZVByb3BlcnRpZXModGFyZ2V0LCBPYmplY3QuZ2V0T3duUHJvcGVydHlEZXNjcmlwdG9ycyhzb3VyY2UpKSA6IG93bktleXMoT2JqZWN0KHNvdXJjZSkpLmZvckVhY2goZnVuY3Rpb24gKGtleSkge1xuICAgICAgICBPYmplY3QuZGVmaW5lUHJvcGVydHkodGFyZ2V0LCBrZXksIE9iamVjdC5nZXRPd25Qcm9wZXJ0eURlc2NyaXB0b3Ioc291cmNlLCBrZXkpKTtcbiAgICAgIH0pO1xuICAgIH1cbiAgICByZXR1cm4gdGFyZ2V0O1xuICB9XG4gIGZ1bmN0aW9uIF90eXBlb2Yob2JqKSB7XG4gICAgXCJAYmFiZWwvaGVscGVycyAtIHR5cGVvZlwiO1xuXG4gICAgcmV0dXJuIF90eXBlb2YgPSBcImZ1bmN0aW9uXCIgPT0gdHlwZW9mIFN5bWJvbCAmJiBcInN5bWJvbFwiID09IHR5cGVvZiBTeW1ib2wuaXRlcmF0b3IgPyBmdW5jdGlvbiAob2JqKSB7XG4gICAgICByZXR1cm4gdHlwZW9mIG9iajtcbiAgICB9IDogZnVuY3Rpb24gKG9iaikge1xuICAgICAgcmV0dXJuIG9iaiAmJiBcImZ1bmN0aW9uXCIgPT0gdHlwZW9mIFN5bWJvbCAmJiBvYmouY29uc3RydWN0b3IgPT09IFN5bWJvbCAmJiBvYmogIT09IFN5bWJvbC5wcm90b3R5cGUgPyBcInN5bWJvbFwiIDogdHlwZW9mIG9iajtcbiAgICB9LCBfdHlwZW9mKG9iaik7XG4gIH1cbiAgZnVuY3Rpb24gX2NsYXNzQ2FsbENoZWNrKGluc3RhbmNlLCBDb25zdHJ1Y3Rvcikge1xuICAgIGlmICghKGluc3RhbmNlIGluc3RhbmNlb2YgQ29uc3RydWN0b3IpKSB7XG4gICAgICB0aHJvdyBuZXcgVHlwZUVycm9yKFwiQ2Fubm90IGNhbGwgYSBjbGFzcyBhcyBhIGZ1bmN0aW9uXCIpO1xuICAgIH1cbiAgfVxuICBmdW5jdGlvbiBfZGVmaW5lUHJvcGVydGllcyh0YXJnZXQsIHByb3BzKSB7XG4gICAgZm9yICh2YXIgaSA9IDA7IGkgPCBwcm9wcy5sZW5ndGg7IGkrKykge1xuICAgICAgdmFyIGRlc2NyaXB0b3IgPSBwcm9wc1tpXTtcbiAgICAgIGRlc2NyaXB0b3IuZW51bWVyYWJsZSA9IGRlc2NyaXB0b3IuZW51bWVyYWJsZSB8fCBmYWxzZTtcbiAgICAgIGRlc2NyaXB0b3IuY29uZmlndXJhYmxlID0gdHJ1ZTtcbiAgICAgIGlmIChcInZhbHVlXCIgaW4gZGVzY3JpcHRvcikgZGVzY3JpcHRvci53cml0YWJsZSA9IHRydWU7XG4gICAgICBPYmplY3QuZGVmaW5lUHJvcGVydHkodGFyZ2V0LCBkZXNjcmlwdG9yLmtleSwgZGVzY3JpcHRvcik7XG4gICAgfVxuICB9XG4gIGZ1bmN0aW9uIF9jcmVhdGVDbGFzcyhDb25zdHJ1Y3RvciwgcHJvdG9Qcm9wcywgc3RhdGljUHJvcHMpIHtcbiAgICBpZiAocHJvdG9Qcm9wcykgX2RlZmluZVByb3BlcnRpZXMoQ29uc3RydWN0b3IucHJvdG90eXBlLCBwcm90b1Byb3BzKTtcbiAgICBpZiAoc3RhdGljUHJvcHMpIF9kZWZpbmVQcm9wZXJ0aWVzKENvbnN0cnVjdG9yLCBzdGF0aWNQcm9wcyk7XG4gICAgT2JqZWN0LmRlZmluZVByb3BlcnR5KENvbnN0cnVjdG9yLCBcInByb3RvdHlwZVwiLCB7XG4gICAgICB3cml0YWJsZTogZmFsc2VcbiAgICB9KTtcbiAgICByZXR1cm4gQ29uc3RydWN0b3I7XG4gIH1cbiAgZnVuY3Rpb24gX2RlZmluZVByb3BlcnR5KG9iaiwga2V5LCB2YWx1ZSkge1xuICAgIGlmIChrZXkgaW4gb2JqKSB7XG4gICAgICBPYmplY3QuZGVmaW5lUHJvcGVydHkob2JqLCBrZXksIHtcbiAgICAgICAgdmFsdWU6IHZhbHVlLFxuICAgICAgICBlbnVtZXJhYmxlOiB0cnVlLFxuICAgICAgICBjb25maWd1cmFibGU6IHRydWUsXG4gICAgICAgIHdyaXRhYmxlOiB0cnVlXG4gICAgICB9KTtcbiAgICB9IGVsc2Uge1xuICAgICAgb2JqW2tleV0gPSB2YWx1ZTtcbiAgICB9XG4gICAgcmV0dXJuIG9iajtcbiAgfVxuICBmdW5jdGlvbiBfdG9Db25zdW1hYmxlQXJyYXkoYXJyKSB7XG4gICAgcmV0dXJuIF9hcnJheVdpdGhvdXRIb2xlcyhhcnIpIHx8IF9pdGVyYWJsZVRvQXJyYXkoYXJyKSB8fCBfdW5zdXBwb3J0ZWRJdGVyYWJsZVRvQXJyYXkoYXJyKSB8fCBfbm9uSXRlcmFibGVTcHJlYWQoKTtcbiAgfVxuICBmdW5jdGlvbiBfYXJyYXlXaXRob3V0SG9sZXMoYXJyKSB7XG4gICAgaWYgKEFycmF5LmlzQXJyYXkoYXJyKSkgcmV0dXJuIF9hcnJheUxpa2VUb0FycmF5KGFycik7XG4gIH1cbiAgZnVuY3Rpb24gX2l0ZXJhYmxlVG9BcnJheShpdGVyKSB7XG4gICAgaWYgKHR5cGVvZiBTeW1ib2wgIT09IFwidW5kZWZpbmVkXCIgJiYgaXRlcltTeW1ib2wuaXRlcmF0b3JdICE9IG51bGwgfHwgaXRlcltcIkBAaXRlcmF0b3JcIl0gIT0gbnVsbCkgcmV0dXJuIEFycmF5LmZyb20oaXRlcik7XG4gIH1cbiAgZnVuY3Rpb24gX3Vuc3VwcG9ydGVkSXRlcmFibGVUb0FycmF5KG8sIG1pbkxlbikge1xuICAgIGlmICghbykgcmV0dXJuO1xuICAgIGlmICh0eXBlb2YgbyA9PT0gXCJzdHJpbmdcIikgcmV0dXJuIF9hcnJheUxpa2VUb0FycmF5KG8sIG1pbkxlbik7XG4gICAgdmFyIG4gPSBPYmplY3QucHJvdG90eXBlLnRvU3RyaW5nLmNhbGwobykuc2xpY2UoOCwgLTEpO1xuICAgIGlmIChuID09PSBcIk9iamVjdFwiICYmIG8uY29uc3RydWN0b3IpIG4gPSBvLmNvbnN0cnVjdG9yLm5hbWU7XG4gICAgaWYgKG4gPT09IFwiTWFwXCIgfHwgbiA9PT0gXCJTZXRcIikgcmV0dXJuIEFycmF5LmZyb20obyk7XG4gICAgaWYgKG4gPT09IFwiQXJndW1lbnRzXCIgfHwgL14oPzpVaXxJKW50KD86OHwxNnwzMikoPzpDbGFtcGVkKT9BcnJheSQvLnRlc3QobikpIHJldHVybiBfYXJyYXlMaWtlVG9BcnJheShvLCBtaW5MZW4pO1xuICB9XG4gIGZ1bmN0aW9uIF9hcnJheUxpa2VUb0FycmF5KGFyciwgbGVuKSB7XG4gICAgaWYgKGxlbiA9PSBudWxsIHx8IGxlbiA+IGFyci5sZW5ndGgpIGxlbiA9IGFyci5sZW5ndGg7XG4gICAgZm9yICh2YXIgaSA9IDAsIGFycjIgPSBuZXcgQXJyYXkobGVuKTsgaSA8IGxlbjsgaSsrKSBhcnIyW2ldID0gYXJyW2ldO1xuICAgIHJldHVybiBhcnIyO1xuICB9XG4gIGZ1bmN0aW9uIF9ub25JdGVyYWJsZVNwcmVhZCgpIHtcbiAgICB0aHJvdyBuZXcgVHlwZUVycm9yKFwiSW52YWxpZCBhdHRlbXB0IHRvIHNwcmVhZCBub24taXRlcmFibGUgaW5zdGFuY2UuXFxuSW4gb3JkZXIgdG8gYmUgaXRlcmFibGUsIG5vbi1hcnJheSBvYmplY3RzIG11c3QgaGF2ZSBhIFtTeW1ib2wuaXRlcmF0b3JdKCkgbWV0aG9kLlwiKTtcbiAgfVxuXG4gIHZhciBJU19CUk9XU0VSID0gdHlwZW9mIHdpbmRvdyAhPT0gJ3VuZGVmaW5lZCcgJiYgdHlwZW9mIHdpbmRvdy5kb2N1bWVudCAhPT0gJ3VuZGVmaW5lZCc7XG4gIHZhciBXSU5ET1cgPSBJU19CUk9XU0VSID8gd2luZG93IDoge307XG4gIHZhciBJU19UT1VDSF9ERVZJQ0UgPSBJU19CUk9XU0VSICYmIFdJTkRPVy5kb2N1bWVudC5kb2N1bWVudEVsZW1lbnQgPyAnb250b3VjaHN0YXJ0JyBpbiBXSU5ET1cuZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50IDogZmFsc2U7XG4gIHZhciBIQVNfUE9JTlRFUl9FVkVOVCA9IElTX0JST1dTRVIgPyAnUG9pbnRlckV2ZW50JyBpbiBXSU5ET1cgOiBmYWxzZTtcbiAgdmFyIE5BTUVTUEFDRSA9ICdjcm9wcGVyJztcblxuICAvLyBBY3Rpb25zXG4gIHZhciBBQ1RJT05fQUxMID0gJ2FsbCc7XG4gIHZhciBBQ1RJT05fQ1JPUCA9ICdjcm9wJztcbiAgdmFyIEFDVElPTl9NT1ZFID0gJ21vdmUnO1xuICB2YXIgQUNUSU9OX1pPT00gPSAnem9vbSc7XG4gIHZhciBBQ1RJT05fRUFTVCA9ICdlJztcbiAgdmFyIEFDVElPTl9XRVNUID0gJ3cnO1xuICB2YXIgQUNUSU9OX1NPVVRIID0gJ3MnO1xuICB2YXIgQUNUSU9OX05PUlRIID0gJ24nO1xuICB2YXIgQUNUSU9OX05PUlRIX0VBU1QgPSAnbmUnO1xuICB2YXIgQUNUSU9OX05PUlRIX1dFU1QgPSAnbncnO1xuICB2YXIgQUNUSU9OX1NPVVRIX0VBU1QgPSAnc2UnO1xuICB2YXIgQUNUSU9OX1NPVVRIX1dFU1QgPSAnc3cnO1xuXG4gIC8vIENsYXNzZXNcbiAgdmFyIENMQVNTX0NST1AgPSBcIlwiLmNvbmNhdChOQU1FU1BBQ0UsIFwiLWNyb3BcIik7XG4gIHZhciBDTEFTU19ESVNBQkxFRCA9IFwiXCIuY29uY2F0KE5BTUVTUEFDRSwgXCItZGlzYWJsZWRcIik7XG4gIHZhciBDTEFTU19ISURERU4gPSBcIlwiLmNvbmNhdChOQU1FU1BBQ0UsIFwiLWhpZGRlblwiKTtcbiAgdmFyIENMQVNTX0hJREUgPSBcIlwiLmNvbmNhdChOQU1FU1BBQ0UsIFwiLWhpZGVcIik7XG4gIHZhciBDTEFTU19JTlZJU0lCTEUgPSBcIlwiLmNvbmNhdChOQU1FU1BBQ0UsIFwiLWludmlzaWJsZVwiKTtcbiAgdmFyIENMQVNTX01PREFMID0gXCJcIi5jb25jYXQoTkFNRVNQQUNFLCBcIi1tb2RhbFwiKTtcbiAgdmFyIENMQVNTX01PVkUgPSBcIlwiLmNvbmNhdChOQU1FU1BBQ0UsIFwiLW1vdmVcIik7XG5cbiAgLy8gRGF0YSBrZXlzXG4gIHZhciBEQVRBX0FDVElPTiA9IFwiXCIuY29uY2F0KE5BTUVTUEFDRSwgXCJBY3Rpb25cIik7XG4gIHZhciBEQVRBX1BSRVZJRVcgPSBcIlwiLmNvbmNhdChOQU1FU1BBQ0UsIFwiUHJldmlld1wiKTtcblxuICAvLyBEcmFnIG1vZGVzXG4gIHZhciBEUkFHX01PREVfQ1JPUCA9ICdjcm9wJztcbiAgdmFyIERSQUdfTU9ERV9NT1ZFID0gJ21vdmUnO1xuICB2YXIgRFJBR19NT0RFX05PTkUgPSAnbm9uZSc7XG5cbiAgLy8gRXZlbnRzXG4gIHZhciBFVkVOVF9DUk9QID0gJ2Nyb3AnO1xuICB2YXIgRVZFTlRfQ1JPUF9FTkQgPSAnY3JvcGVuZCc7XG4gIHZhciBFVkVOVF9DUk9QX01PVkUgPSAnY3JvcG1vdmUnO1xuICB2YXIgRVZFTlRfQ1JPUF9TVEFSVCA9ICdjcm9wc3RhcnQnO1xuICB2YXIgRVZFTlRfREJMQ0xJQ0sgPSAnZGJsY2xpY2snO1xuICB2YXIgRVZFTlRfVE9VQ0hfU1RBUlQgPSBJU19UT1VDSF9ERVZJQ0UgPyAndG91Y2hzdGFydCcgOiAnbW91c2Vkb3duJztcbiAgdmFyIEVWRU5UX1RPVUNIX01PVkUgPSBJU19UT1VDSF9ERVZJQ0UgPyAndG91Y2htb3ZlJyA6ICdtb3VzZW1vdmUnO1xuICB2YXIgRVZFTlRfVE9VQ0hfRU5EID0gSVNfVE9VQ0hfREVWSUNFID8gJ3RvdWNoZW5kIHRvdWNoY2FuY2VsJyA6ICdtb3VzZXVwJztcbiAgdmFyIEVWRU5UX1BPSU5URVJfRE9XTiA9IEhBU19QT0lOVEVSX0VWRU5UID8gJ3BvaW50ZXJkb3duJyA6IEVWRU5UX1RPVUNIX1NUQVJUO1xuICB2YXIgRVZFTlRfUE9JTlRFUl9NT1ZFID0gSEFTX1BPSU5URVJfRVZFTlQgPyAncG9pbnRlcm1vdmUnIDogRVZFTlRfVE9VQ0hfTU9WRTtcbiAgdmFyIEVWRU5UX1BPSU5URVJfVVAgPSBIQVNfUE9JTlRFUl9FVkVOVCA/ICdwb2ludGVydXAgcG9pbnRlcmNhbmNlbCcgOiBFVkVOVF9UT1VDSF9FTkQ7XG4gIHZhciBFVkVOVF9SRUFEWSA9ICdyZWFkeSc7XG4gIHZhciBFVkVOVF9SRVNJWkUgPSAncmVzaXplJztcbiAgdmFyIEVWRU5UX1dIRUVMID0gJ3doZWVsJztcbiAgdmFyIEVWRU5UX1pPT00gPSAnem9vbSc7XG5cbiAgLy8gTWltZSB0eXBlc1xuICB2YXIgTUlNRV9UWVBFX0pQRUcgPSAnaW1hZ2UvanBlZyc7XG5cbiAgLy8gUmVnRXhwc1xuICB2YXIgUkVHRVhQX0FDVElPTlMgPSAvXmV8d3xzfG58c2V8c3d8bmV8bnd8YWxsfGNyb3B8bW92ZXx6b29tJC87XG4gIHZhciBSRUdFWFBfREFUQV9VUkwgPSAvXmRhdGE6LztcbiAgdmFyIFJFR0VYUF9EQVRBX1VSTF9KUEVHID0gL15kYXRhOmltYWdlXFwvanBlZztiYXNlNjQsLztcbiAgdmFyIFJFR0VYUF9UQUdfTkFNRSA9IC9eaW1nfGNhbnZhcyQvaTtcblxuICAvLyBNaXNjXG4gIC8vIEluc3BpcmVkIGJ5IHRoZSBkZWZhdWx0IHdpZHRoIGFuZCBoZWlnaHQgb2YgYSBjYW52YXMgZWxlbWVudC5cbiAgdmFyIE1JTl9DT05UQUlORVJfV0lEVEggPSAyMDA7XG4gIHZhciBNSU5fQ09OVEFJTkVSX0hFSUdIVCA9IDEwMDtcblxuICB2YXIgREVGQVVMVFMgPSB7XG4gICAgLy8gRGVmaW5lIHRoZSB2aWV3IG1vZGUgb2YgdGhlIGNyb3BwZXJcbiAgICB2aWV3TW9kZTogMCxcbiAgICAvLyAwLCAxLCAyLCAzXG5cbiAgICAvLyBEZWZpbmUgdGhlIGRyYWdnaW5nIG1vZGUgb2YgdGhlIGNyb3BwZXJcbiAgICBkcmFnTW9kZTogRFJBR19NT0RFX0NST1AsXG4gICAgLy8gJ2Nyb3AnLCAnbW92ZScgb3IgJ25vbmUnXG5cbiAgICAvLyBEZWZpbmUgdGhlIGluaXRpYWwgYXNwZWN0IHJhdGlvIG9mIHRoZSBjcm9wIGJveFxuICAgIGluaXRpYWxBc3BlY3RSYXRpbzogTmFOLFxuICAgIC8vIERlZmluZSB0aGUgYXNwZWN0IHJhdGlvIG9mIHRoZSBjcm9wIGJveFxuICAgIGFzcGVjdFJhdGlvOiBOYU4sXG4gICAgLy8gQW4gb2JqZWN0IHdpdGggdGhlIHByZXZpb3VzIGNyb3BwaW5nIHJlc3VsdCBkYXRhXG4gICAgZGF0YTogbnVsbCxcbiAgICAvLyBBIHNlbGVjdG9yIGZvciBhZGRpbmcgZXh0cmEgY29udGFpbmVycyB0byBwcmV2aWV3XG4gICAgcHJldmlldzogJycsXG4gICAgLy8gUmUtcmVuZGVyIHRoZSBjcm9wcGVyIHdoZW4gcmVzaXplIHRoZSB3aW5kb3dcbiAgICByZXNwb25zaXZlOiB0cnVlLFxuICAgIC8vIFJlc3RvcmUgdGhlIGNyb3BwZWQgYXJlYSBhZnRlciByZXNpemUgdGhlIHdpbmRvd1xuICAgIHJlc3RvcmU6IHRydWUsXG4gICAgLy8gQ2hlY2sgaWYgdGhlIGN1cnJlbnQgaW1hZ2UgaXMgYSBjcm9zcy1vcmlnaW4gaW1hZ2VcbiAgICBjaGVja0Nyb3NzT3JpZ2luOiB0cnVlLFxuICAgIC8vIENoZWNrIHRoZSBjdXJyZW50IGltYWdlJ3MgRXhpZiBPcmllbnRhdGlvbiBpbmZvcm1hdGlvblxuICAgIGNoZWNrT3JpZW50YXRpb246IHRydWUsXG4gICAgLy8gU2hvdyB0aGUgYmxhY2sgbW9kYWxcbiAgICBtb2RhbDogdHJ1ZSxcbiAgICAvLyBTaG93IHRoZSBkYXNoZWQgbGluZXMgZm9yIGd1aWRpbmdcbiAgICBndWlkZXM6IHRydWUsXG4gICAgLy8gU2hvdyB0aGUgY2VudGVyIGluZGljYXRvciBmb3IgZ3VpZGluZ1xuICAgIGNlbnRlcjogdHJ1ZSxcbiAgICAvLyBTaG93IHRoZSB3aGl0ZSBtb2RhbCB0byBoaWdobGlnaHQgdGhlIGNyb3AgYm94XG4gICAgaGlnaGxpZ2h0OiB0cnVlLFxuICAgIC8vIFNob3cgdGhlIGdyaWQgYmFja2dyb3VuZFxuICAgIGJhY2tncm91bmQ6IHRydWUsXG4gICAgLy8gRW5hYmxlIHRvIGNyb3AgdGhlIGltYWdlIGF1dG9tYXRpY2FsbHkgd2hlbiBpbml0aWFsaXplXG4gICAgYXV0b0Nyb3A6IHRydWUsXG4gICAgLy8gRGVmaW5lIHRoZSBwZXJjZW50YWdlIG9mIGF1dG9tYXRpYyBjcm9wcGluZyBhcmVhIHdoZW4gaW5pdGlhbGl6ZXNcbiAgICBhdXRvQ3JvcEFyZWE6IDAuOCxcbiAgICAvLyBFbmFibGUgdG8gbW92ZSB0aGUgaW1hZ2VcbiAgICBtb3ZhYmxlOiB0cnVlLFxuICAgIC8vIEVuYWJsZSB0byByb3RhdGUgdGhlIGltYWdlXG4gICAgcm90YXRhYmxlOiB0cnVlLFxuICAgIC8vIEVuYWJsZSB0byBzY2FsZSB0aGUgaW1hZ2VcbiAgICBzY2FsYWJsZTogdHJ1ZSxcbiAgICAvLyBFbmFibGUgdG8gem9vbSB0aGUgaW1hZ2VcbiAgICB6b29tYWJsZTogdHJ1ZSxcbiAgICAvLyBFbmFibGUgdG8gem9vbSB0aGUgaW1hZ2UgYnkgZHJhZ2dpbmcgdG91Y2hcbiAgICB6b29tT25Ub3VjaDogdHJ1ZSxcbiAgICAvLyBFbmFibGUgdG8gem9vbSB0aGUgaW1hZ2UgYnkgd2hlZWxpbmcgbW91c2VcbiAgICB6b29tT25XaGVlbDogdHJ1ZSxcbiAgICAvLyBEZWZpbmUgem9vbSByYXRpbyB3aGVuIHpvb20gdGhlIGltYWdlIGJ5IHdoZWVsaW5nIG1vdXNlXG4gICAgd2hlZWxab29tUmF0aW86IDAuMSxcbiAgICAvLyBFbmFibGUgdG8gbW92ZSB0aGUgY3JvcCBib3hcbiAgICBjcm9wQm94TW92YWJsZTogdHJ1ZSxcbiAgICAvLyBFbmFibGUgdG8gcmVzaXplIHRoZSBjcm9wIGJveFxuICAgIGNyb3BCb3hSZXNpemFibGU6IHRydWUsXG4gICAgLy8gVG9nZ2xlIGRyYWcgbW9kZSBiZXR3ZWVuIFwiY3JvcFwiIGFuZCBcIm1vdmVcIiB3aGVuIGNsaWNrIHR3aWNlIG9uIHRoZSBjcm9wcGVyXG4gICAgdG9nZ2xlRHJhZ01vZGVPbkRibGNsaWNrOiB0cnVlLFxuICAgIC8vIFNpemUgbGltaXRhdGlvblxuICAgIG1pbkNhbnZhc1dpZHRoOiAwLFxuICAgIG1pbkNhbnZhc0hlaWdodDogMCxcbiAgICBtaW5Dcm9wQm94V2lkdGg6IDAsXG4gICAgbWluQ3JvcEJveEhlaWdodDogMCxcbiAgICBtaW5Db250YWluZXJXaWR0aDogTUlOX0NPTlRBSU5FUl9XSURUSCxcbiAgICBtaW5Db250YWluZXJIZWlnaHQ6IE1JTl9DT05UQUlORVJfSEVJR0hULFxuICAgIC8vIFNob3J0Y3V0cyBvZiBldmVudHNcbiAgICByZWFkeTogbnVsbCxcbiAgICBjcm9wc3RhcnQ6IG51bGwsXG4gICAgY3JvcG1vdmU6IG51bGwsXG4gICAgY3JvcGVuZDogbnVsbCxcbiAgICBjcm9wOiBudWxsLFxuICAgIHpvb206IG51bGxcbiAgfTtcblxuICB2YXIgVEVNUExBVEUgPSAnPGRpdiBjbGFzcz1cImNyb3BwZXItY29udGFpbmVyXCIgdG91Y2gtYWN0aW9uPVwibm9uZVwiPicgKyAnPGRpdiBjbGFzcz1cImNyb3BwZXItd3JhcC1ib3hcIj4nICsgJzxkaXYgY2xhc3M9XCJjcm9wcGVyLWNhbnZhc1wiPjwvZGl2PicgKyAnPC9kaXY+JyArICc8ZGl2IGNsYXNzPVwiY3JvcHBlci1kcmFnLWJveFwiPjwvZGl2PicgKyAnPGRpdiBjbGFzcz1cImNyb3BwZXItY3JvcC1ib3hcIj4nICsgJzxzcGFuIGNsYXNzPVwiY3JvcHBlci12aWV3LWJveFwiPjwvc3Bhbj4nICsgJzxzcGFuIGNsYXNzPVwiY3JvcHBlci1kYXNoZWQgZGFzaGVkLWhcIj48L3NwYW4+JyArICc8c3BhbiBjbGFzcz1cImNyb3BwZXItZGFzaGVkIGRhc2hlZC12XCI+PC9zcGFuPicgKyAnPHNwYW4gY2xhc3M9XCJjcm9wcGVyLWNlbnRlclwiPjwvc3Bhbj4nICsgJzxzcGFuIGNsYXNzPVwiY3JvcHBlci1mYWNlXCI+PC9zcGFuPicgKyAnPHNwYW4gY2xhc3M9XCJjcm9wcGVyLWxpbmUgbGluZS1lXCIgZGF0YS1jcm9wcGVyLWFjdGlvbj1cImVcIj48L3NwYW4+JyArICc8c3BhbiBjbGFzcz1cImNyb3BwZXItbGluZSBsaW5lLW5cIiBkYXRhLWNyb3BwZXItYWN0aW9uPVwiblwiPjwvc3Bhbj4nICsgJzxzcGFuIGNsYXNzPVwiY3JvcHBlci1saW5lIGxpbmUtd1wiIGRhdGEtY3JvcHBlci1hY3Rpb249XCJ3XCI+PC9zcGFuPicgKyAnPHNwYW4gY2xhc3M9XCJjcm9wcGVyLWxpbmUgbGluZS1zXCIgZGF0YS1jcm9wcGVyLWFjdGlvbj1cInNcIj48L3NwYW4+JyArICc8c3BhbiBjbGFzcz1cImNyb3BwZXItcG9pbnQgcG9pbnQtZVwiIGRhdGEtY3JvcHBlci1hY3Rpb249XCJlXCI+PC9zcGFuPicgKyAnPHNwYW4gY2xhc3M9XCJjcm9wcGVyLXBvaW50IHBvaW50LW5cIiBkYXRhLWNyb3BwZXItYWN0aW9uPVwiblwiPjwvc3Bhbj4nICsgJzxzcGFuIGNsYXNzPVwiY3JvcHBlci1wb2ludCBwb2ludC13XCIgZGF0YS1jcm9wcGVyLWFjdGlvbj1cIndcIj48L3NwYW4+JyArICc8c3BhbiBjbGFzcz1cImNyb3BwZXItcG9pbnQgcG9pbnQtc1wiIGRhdGEtY3JvcHBlci1hY3Rpb249XCJzXCI+PC9zcGFuPicgKyAnPHNwYW4gY2xhc3M9XCJjcm9wcGVyLXBvaW50IHBvaW50LW5lXCIgZGF0YS1jcm9wcGVyLWFjdGlvbj1cIm5lXCI+PC9zcGFuPicgKyAnPHNwYW4gY2xhc3M9XCJjcm9wcGVyLXBvaW50IHBvaW50LW53XCIgZGF0YS1jcm9wcGVyLWFjdGlvbj1cIm53XCI+PC9zcGFuPicgKyAnPHNwYW4gY2xhc3M9XCJjcm9wcGVyLXBvaW50IHBvaW50LXN3XCIgZGF0YS1jcm9wcGVyLWFjdGlvbj1cInN3XCI+PC9zcGFuPicgKyAnPHNwYW4gY2xhc3M9XCJjcm9wcGVyLXBvaW50IHBvaW50LXNlXCIgZGF0YS1jcm9wcGVyLWFjdGlvbj1cInNlXCI+PC9zcGFuPicgKyAnPC9kaXY+JyArICc8L2Rpdj4nO1xuXG4gIC8qKlxuICAgKiBDaGVjayBpZiB0aGUgZ2l2ZW4gdmFsdWUgaXMgbm90IGEgbnVtYmVyLlxuICAgKi9cbiAgdmFyIGlzTmFOID0gTnVtYmVyLmlzTmFOIHx8IFdJTkRPVy5pc05hTjtcblxuICAvKipcbiAgICogQ2hlY2sgaWYgdGhlIGdpdmVuIHZhbHVlIGlzIGEgbnVtYmVyLlxuICAgKiBAcGFyYW0geyp9IHZhbHVlIC0gVGhlIHZhbHVlIHRvIGNoZWNrLlxuICAgKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgdGhlIGdpdmVuIHZhbHVlIGlzIGEgbnVtYmVyLCBlbHNlIGBmYWxzZWAuXG4gICAqL1xuICBmdW5jdGlvbiBpc051bWJlcih2YWx1ZSkge1xuICAgIHJldHVybiB0eXBlb2YgdmFsdWUgPT09ICdudW1iZXInICYmICFpc05hTih2YWx1ZSk7XG4gIH1cblxuICAvKipcbiAgICogQ2hlY2sgaWYgdGhlIGdpdmVuIHZhbHVlIGlzIGEgcG9zaXRpdmUgbnVtYmVyLlxuICAgKiBAcGFyYW0geyp9IHZhbHVlIC0gVGhlIHZhbHVlIHRvIGNoZWNrLlxuICAgKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgdGhlIGdpdmVuIHZhbHVlIGlzIGEgcG9zaXRpdmUgbnVtYmVyLCBlbHNlIGBmYWxzZWAuXG4gICAqL1xuICB2YXIgaXNQb3NpdGl2ZU51bWJlciA9IGZ1bmN0aW9uIGlzUG9zaXRpdmVOdW1iZXIodmFsdWUpIHtcbiAgICByZXR1cm4gdmFsdWUgPiAwICYmIHZhbHVlIDwgSW5maW5pdHk7XG4gIH07XG5cbiAgLyoqXG4gICAqIENoZWNrIGlmIHRoZSBnaXZlbiB2YWx1ZSBpcyB1bmRlZmluZWQuXG4gICAqIEBwYXJhbSB7Kn0gdmFsdWUgLSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gICAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiB0aGUgZ2l2ZW4gdmFsdWUgaXMgdW5kZWZpbmVkLCBlbHNlIGBmYWxzZWAuXG4gICAqL1xuICBmdW5jdGlvbiBpc1VuZGVmaW5lZCh2YWx1ZSkge1xuICAgIHJldHVybiB0eXBlb2YgdmFsdWUgPT09ICd1bmRlZmluZWQnO1xuICB9XG5cbiAgLyoqXG4gICAqIENoZWNrIGlmIHRoZSBnaXZlbiB2YWx1ZSBpcyBhbiBvYmplY3QuXG4gICAqIEBwYXJhbSB7Kn0gdmFsdWUgLSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gICAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiB0aGUgZ2l2ZW4gdmFsdWUgaXMgYW4gb2JqZWN0LCBlbHNlIGBmYWxzZWAuXG4gICAqL1xuICBmdW5jdGlvbiBpc09iamVjdCh2YWx1ZSkge1xuICAgIHJldHVybiBfdHlwZW9mKHZhbHVlKSA9PT0gJ29iamVjdCcgJiYgdmFsdWUgIT09IG51bGw7XG4gIH1cbiAgdmFyIGhhc093blByb3BlcnR5ID0gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eTtcblxuICAvKipcbiAgICogQ2hlY2sgaWYgdGhlIGdpdmVuIHZhbHVlIGlzIGEgcGxhaW4gb2JqZWN0LlxuICAgKiBAcGFyYW0geyp9IHZhbHVlIC0gVGhlIHZhbHVlIHRvIGNoZWNrLlxuICAgKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgdGhlIGdpdmVuIHZhbHVlIGlzIGEgcGxhaW4gb2JqZWN0LCBlbHNlIGBmYWxzZWAuXG4gICAqL1xuICBmdW5jdGlvbiBpc1BsYWluT2JqZWN0KHZhbHVlKSB7XG4gICAgaWYgKCFpc09iamVjdCh2YWx1ZSkpIHtcbiAgICAgIHJldHVybiBmYWxzZTtcbiAgICB9XG4gICAgdHJ5IHtcbiAgICAgIHZhciBfY29uc3RydWN0b3IgPSB2YWx1ZS5jb25zdHJ1Y3RvcjtcbiAgICAgIHZhciBwcm90b3R5cGUgPSBfY29uc3RydWN0b3IucHJvdG90eXBlO1xuICAgICAgcmV0dXJuIF9jb25zdHJ1Y3RvciAmJiBwcm90b3R5cGUgJiYgaGFzT3duUHJvcGVydHkuY2FsbChwcm90b3R5cGUsICdpc1Byb3RvdHlwZU9mJyk7XG4gICAgfSBjYXRjaCAoZXJyb3IpIHtcbiAgICAgIHJldHVybiBmYWxzZTtcbiAgICB9XG4gIH1cblxuICAvKipcbiAgICogQ2hlY2sgaWYgdGhlIGdpdmVuIHZhbHVlIGlzIGEgZnVuY3Rpb24uXG4gICAqIEBwYXJhbSB7Kn0gdmFsdWUgLSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gICAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiB0aGUgZ2l2ZW4gdmFsdWUgaXMgYSBmdW5jdGlvbiwgZWxzZSBgZmFsc2VgLlxuICAgKi9cbiAgZnVuY3Rpb24gaXNGdW5jdGlvbih2YWx1ZSkge1xuICAgIHJldHVybiB0eXBlb2YgdmFsdWUgPT09ICdmdW5jdGlvbic7XG4gIH1cbiAgdmFyIHNsaWNlID0gQXJyYXkucHJvdG90eXBlLnNsaWNlO1xuXG4gIC8qKlxuICAgKiBDb252ZXJ0IGFycmF5LWxpa2Ugb3IgaXRlcmFibGUgb2JqZWN0IHRvIGFuIGFycmF5LlxuICAgKiBAcGFyYW0geyp9IHZhbHVlIC0gVGhlIHZhbHVlIHRvIGNvbnZlcnQuXG4gICAqIEByZXR1cm5zIHtBcnJheX0gUmV0dXJucyBhIG5ldyBhcnJheS5cbiAgICovXG4gIGZ1bmN0aW9uIHRvQXJyYXkodmFsdWUpIHtcbiAgICByZXR1cm4gQXJyYXkuZnJvbSA/IEFycmF5LmZyb20odmFsdWUpIDogc2xpY2UuY2FsbCh2YWx1ZSk7XG4gIH1cblxuICAvKipcbiAgICogSXRlcmF0ZSB0aGUgZ2l2ZW4gZGF0YS5cbiAgICogQHBhcmFtIHsqfSBkYXRhIC0gVGhlIGRhdGEgdG8gaXRlcmF0ZS5cbiAgICogQHBhcmFtIHtGdW5jdGlvbn0gY2FsbGJhY2sgLSBUaGUgcHJvY2VzcyBmdW5jdGlvbiBmb3IgZWFjaCBlbGVtZW50LlxuICAgKiBAcmV0dXJucyB7Kn0gVGhlIG9yaWdpbmFsIGRhdGEuXG4gICAqL1xuICBmdW5jdGlvbiBmb3JFYWNoKGRhdGEsIGNhbGxiYWNrKSB7XG4gICAgaWYgKGRhdGEgJiYgaXNGdW5jdGlvbihjYWxsYmFjaykpIHtcbiAgICAgIGlmIChBcnJheS5pc0FycmF5KGRhdGEpIHx8IGlzTnVtYmVyKGRhdGEubGVuZ3RoKSAvKiBhcnJheS1saWtlICovKSB7XG4gICAgICAgIHRvQXJyYXkoZGF0YSkuZm9yRWFjaChmdW5jdGlvbiAodmFsdWUsIGtleSkge1xuICAgICAgICAgIGNhbGxiYWNrLmNhbGwoZGF0YSwgdmFsdWUsIGtleSwgZGF0YSk7XG4gICAgICAgIH0pO1xuICAgICAgfSBlbHNlIGlmIChpc09iamVjdChkYXRhKSkge1xuICAgICAgICBPYmplY3Qua2V5cyhkYXRhKS5mb3JFYWNoKGZ1bmN0aW9uIChrZXkpIHtcbiAgICAgICAgICBjYWxsYmFjay5jYWxsKGRhdGEsIGRhdGFba2V5XSwga2V5LCBkYXRhKTtcbiAgICAgICAgfSk7XG4gICAgICB9XG4gICAgfVxuICAgIHJldHVybiBkYXRhO1xuICB9XG5cbiAgLyoqXG4gICAqIEV4dGVuZCB0aGUgZ2l2ZW4gb2JqZWN0LlxuICAgKiBAcGFyYW0geyp9IHRhcmdldCAtIFRoZSB0YXJnZXQgb2JqZWN0IHRvIGV4dGVuZC5cbiAgICogQHBhcmFtIHsqfSBhcmdzIC0gVGhlIHJlc3Qgb2JqZWN0cyBmb3IgbWVyZ2luZyB0byB0aGUgdGFyZ2V0IG9iamVjdC5cbiAgICogQHJldHVybnMge09iamVjdH0gVGhlIGV4dGVuZGVkIG9iamVjdC5cbiAgICovXG4gIHZhciBhc3NpZ24gPSBPYmplY3QuYXNzaWduIHx8IGZ1bmN0aW9uIGFzc2lnbih0YXJnZXQpIHtcbiAgICBmb3IgKHZhciBfbGVuID0gYXJndW1lbnRzLmxlbmd0aCwgYXJncyA9IG5ldyBBcnJheShfbGVuID4gMSA/IF9sZW4gLSAxIDogMCksIF9rZXkgPSAxOyBfa2V5IDwgX2xlbjsgX2tleSsrKSB7XG4gICAgICBhcmdzW19rZXkgLSAxXSA9IGFyZ3VtZW50c1tfa2V5XTtcbiAgICB9XG4gICAgaWYgKGlzT2JqZWN0KHRhcmdldCkgJiYgYXJncy5sZW5ndGggPiAwKSB7XG4gICAgICBhcmdzLmZvckVhY2goZnVuY3Rpb24gKGFyZykge1xuICAgICAgICBpZiAoaXNPYmplY3QoYXJnKSkge1xuICAgICAgICAgIE9iamVjdC5rZXlzKGFyZykuZm9yRWFjaChmdW5jdGlvbiAoa2V5KSB7XG4gICAgICAgICAgICB0YXJnZXRba2V5XSA9IGFyZ1trZXldO1xuICAgICAgICAgIH0pO1xuICAgICAgICB9XG4gICAgICB9KTtcbiAgICB9XG4gICAgcmV0dXJuIHRhcmdldDtcbiAgfTtcbiAgdmFyIFJFR0VYUF9ERUNJTUFMUyA9IC9cXC5cXGQqKD86MHw5KXsxMn1cXGQqJC87XG5cbiAgLyoqXG4gICAqIE5vcm1hbGl6ZSBkZWNpbWFsIG51bWJlci5cbiAgICogQ2hlY2sgb3V0IHtAbGluayBodHRwczovLzAuMzAwMDAwMDAwMDAwMDAwMDQuY29tL31cbiAgICogQHBhcmFtIHtudW1iZXJ9IHZhbHVlIC0gVGhlIHZhbHVlIHRvIG5vcm1hbGl6ZS5cbiAgICogQHBhcmFtIHtudW1iZXJ9IFt0aW1lcz0xMDAwMDAwMDAwMDBdIC0gVGhlIHRpbWVzIGZvciBub3JtYWxpemluZy5cbiAgICogQHJldHVybnMge251bWJlcn0gUmV0dXJucyB0aGUgbm9ybWFsaXplZCBudW1iZXIuXG4gICAqL1xuICBmdW5jdGlvbiBub3JtYWxpemVEZWNpbWFsTnVtYmVyKHZhbHVlKSB7XG4gICAgdmFyIHRpbWVzID0gYXJndW1lbnRzLmxlbmd0aCA+IDEgJiYgYXJndW1lbnRzWzFdICE9PSB1bmRlZmluZWQgPyBhcmd1bWVudHNbMV0gOiAxMDAwMDAwMDAwMDA7XG4gICAgcmV0dXJuIFJFR0VYUF9ERUNJTUFMUy50ZXN0KHZhbHVlKSA/IE1hdGgucm91bmQodmFsdWUgKiB0aW1lcykgLyB0aW1lcyA6IHZhbHVlO1xuICB9XG4gIHZhciBSRUdFWFBfU1VGRklYID0gL153aWR0aHxoZWlnaHR8bGVmdHx0b3B8bWFyZ2luTGVmdHxtYXJnaW5Ub3AkLztcblxuICAvKipcbiAgICogQXBwbHkgc3R5bGVzIHRvIHRoZSBnaXZlbiBlbGVtZW50LlxuICAgKiBAcGFyYW0ge0VsZW1lbnR9IGVsZW1lbnQgLSBUaGUgdGFyZ2V0IGVsZW1lbnQuXG4gICAqIEBwYXJhbSB7T2JqZWN0fSBzdHlsZXMgLSBUaGUgc3R5bGVzIGZvciBhcHBseWluZy5cbiAgICovXG4gIGZ1bmN0aW9uIHNldFN0eWxlKGVsZW1lbnQsIHN0eWxlcykge1xuICAgIHZhciBzdHlsZSA9IGVsZW1lbnQuc3R5bGU7XG4gICAgZm9yRWFjaChzdHlsZXMsIGZ1bmN0aW9uICh2YWx1ZSwgcHJvcGVydHkpIHtcbiAgICAgIGlmIChSRUdFWFBfU1VGRklYLnRlc3QocHJvcGVydHkpICYmIGlzTnVtYmVyKHZhbHVlKSkge1xuICAgICAgICB2YWx1ZSA9IFwiXCIuY29uY2F0KHZhbHVlLCBcInB4XCIpO1xuICAgICAgfVxuICAgICAgc3R5bGVbcHJvcGVydHldID0gdmFsdWU7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogQ2hlY2sgaWYgdGhlIGdpdmVuIGVsZW1lbnQgaGFzIGEgc3BlY2lhbCBjbGFzcy5cbiAgICogQHBhcmFtIHtFbGVtZW50fSBlbGVtZW50IC0gVGhlIGVsZW1lbnQgdG8gY2hlY2suXG4gICAqIEBwYXJhbSB7c3RyaW5nfSB2YWx1ZSAtIFRoZSBjbGFzcyB0byBzZWFyY2guXG4gICAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiB0aGUgc3BlY2lhbCBjbGFzcyB3YXMgZm91bmQuXG4gICAqL1xuICBmdW5jdGlvbiBoYXNDbGFzcyhlbGVtZW50LCB2YWx1ZSkge1xuICAgIHJldHVybiBlbGVtZW50LmNsYXNzTGlzdCA/IGVsZW1lbnQuY2xhc3NMaXN0LmNvbnRhaW5zKHZhbHVlKSA6IGVsZW1lbnQuY2xhc3NOYW1lLmluZGV4T2YodmFsdWUpID4gLTE7XG4gIH1cblxuICAvKipcbiAgICogQWRkIGNsYXNzZXMgdG8gdGhlIGdpdmVuIGVsZW1lbnQuXG4gICAqIEBwYXJhbSB7RWxlbWVudH0gZWxlbWVudCAtIFRoZSB0YXJnZXQgZWxlbWVudC5cbiAgICogQHBhcmFtIHtzdHJpbmd9IHZhbHVlIC0gVGhlIGNsYXNzZXMgdG8gYmUgYWRkZWQuXG4gICAqL1xuICBmdW5jdGlvbiBhZGRDbGFzcyhlbGVtZW50LCB2YWx1ZSkge1xuICAgIGlmICghdmFsdWUpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgaWYgKGlzTnVtYmVyKGVsZW1lbnQubGVuZ3RoKSkge1xuICAgICAgZm9yRWFjaChlbGVtZW50LCBmdW5jdGlvbiAoZWxlbSkge1xuICAgICAgICBhZGRDbGFzcyhlbGVtLCB2YWx1ZSk7XG4gICAgICB9KTtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgaWYgKGVsZW1lbnQuY2xhc3NMaXN0KSB7XG4gICAgICBlbGVtZW50LmNsYXNzTGlzdC5hZGQodmFsdWUpO1xuICAgICAgcmV0dXJuO1xuICAgIH1cbiAgICB2YXIgY2xhc3NOYW1lID0gZWxlbWVudC5jbGFzc05hbWUudHJpbSgpO1xuICAgIGlmICghY2xhc3NOYW1lKSB7XG4gICAgICBlbGVtZW50LmNsYXNzTmFtZSA9IHZhbHVlO1xuICAgIH0gZWxzZSBpZiAoY2xhc3NOYW1lLmluZGV4T2YodmFsdWUpIDwgMCkge1xuICAgICAgZWxlbWVudC5jbGFzc05hbWUgPSBcIlwiLmNvbmNhdChjbGFzc05hbWUsIFwiIFwiKS5jb25jYXQodmFsdWUpO1xuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgKiBSZW1vdmUgY2xhc3NlcyBmcm9tIHRoZSBnaXZlbiBlbGVtZW50LlxuICAgKiBAcGFyYW0ge0VsZW1lbnR9IGVsZW1lbnQgLSBUaGUgdGFyZ2V0IGVsZW1lbnQuXG4gICAqIEBwYXJhbSB7c3RyaW5nfSB2YWx1ZSAtIFRoZSBjbGFzc2VzIHRvIGJlIHJlbW92ZWQuXG4gICAqL1xuICBmdW5jdGlvbiByZW1vdmVDbGFzcyhlbGVtZW50LCB2YWx1ZSkge1xuICAgIGlmICghdmFsdWUpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgaWYgKGlzTnVtYmVyKGVsZW1lbnQubGVuZ3RoKSkge1xuICAgICAgZm9yRWFjaChlbGVtZW50LCBmdW5jdGlvbiAoZWxlbSkge1xuICAgICAgICByZW1vdmVDbGFzcyhlbGVtLCB2YWx1ZSk7XG4gICAgICB9KTtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgaWYgKGVsZW1lbnQuY2xhc3NMaXN0KSB7XG4gICAgICBlbGVtZW50LmNsYXNzTGlzdC5yZW1vdmUodmFsdWUpO1xuICAgICAgcmV0dXJuO1xuICAgIH1cbiAgICBpZiAoZWxlbWVudC5jbGFzc05hbWUuaW5kZXhPZih2YWx1ZSkgPj0gMCkge1xuICAgICAgZWxlbWVudC5jbGFzc05hbWUgPSBlbGVtZW50LmNsYXNzTmFtZS5yZXBsYWNlKHZhbHVlLCAnJyk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIEFkZCBvciByZW1vdmUgY2xhc3NlcyBmcm9tIHRoZSBnaXZlbiBlbGVtZW50LlxuICAgKiBAcGFyYW0ge0VsZW1lbnR9IGVsZW1lbnQgLSBUaGUgdGFyZ2V0IGVsZW1lbnQuXG4gICAqIEBwYXJhbSB7c3RyaW5nfSB2YWx1ZSAtIFRoZSBjbGFzc2VzIHRvIGJlIHRvZ2dsZWQuXG4gICAqIEBwYXJhbSB7Ym9vbGVhbn0gYWRkZWQgLSBBZGQgb25seS5cbiAgICovXG4gIGZ1bmN0aW9uIHRvZ2dsZUNsYXNzKGVsZW1lbnQsIHZhbHVlLCBhZGRlZCkge1xuICAgIGlmICghdmFsdWUpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgaWYgKGlzTnVtYmVyKGVsZW1lbnQubGVuZ3RoKSkge1xuICAgICAgZm9yRWFjaChlbGVtZW50LCBmdW5jdGlvbiAoZWxlbSkge1xuICAgICAgICB0b2dnbGVDbGFzcyhlbGVtLCB2YWx1ZSwgYWRkZWQpO1xuICAgICAgfSk7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgLy8gSUUxMC0xMSBkb2Vzbid0IHN1cHBvcnQgdGhlIHNlY29uZCBwYXJhbWV0ZXIgb2YgYGNsYXNzTGlzdC50b2dnbGVgXG4gICAgaWYgKGFkZGVkKSB7XG4gICAgICBhZGRDbGFzcyhlbGVtZW50LCB2YWx1ZSk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHJlbW92ZUNsYXNzKGVsZW1lbnQsIHZhbHVlKTtcbiAgICB9XG4gIH1cbiAgdmFyIFJFR0VYUF9DQU1FTF9DQVNFID0gLyhbYS16XFxkXSkoW0EtWl0pL2c7XG5cbiAgLyoqXG4gICAqIFRyYW5zZm9ybSB0aGUgZ2l2ZW4gc3RyaW5nIGZyb20gY2FtZWxDYXNlIHRvIGtlYmFiLWNhc2VcbiAgICogQHBhcmFtIHtzdHJpbmd9IHZhbHVlIC0gVGhlIHZhbHVlIHRvIHRyYW5zZm9ybS5cbiAgICogQHJldHVybnMge3N0cmluZ30gVGhlIHRyYW5zZm9ybWVkIHZhbHVlLlxuICAgKi9cbiAgZnVuY3Rpb24gdG9QYXJhbUNhc2UodmFsdWUpIHtcbiAgICByZXR1cm4gdmFsdWUucmVwbGFjZShSRUdFWFBfQ0FNRUxfQ0FTRSwgJyQxLSQyJykudG9Mb3dlckNhc2UoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgZGF0YSBmcm9tIHRoZSBnaXZlbiBlbGVtZW50LlxuICAgKiBAcGFyYW0ge0VsZW1lbnR9IGVsZW1lbnQgLSBUaGUgdGFyZ2V0IGVsZW1lbnQuXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBuYW1lIC0gVGhlIGRhdGEga2V5IHRvIGdldC5cbiAgICogQHJldHVybnMge3N0cmluZ30gVGhlIGRhdGEgdmFsdWUuXG4gICAqL1xuICBmdW5jdGlvbiBnZXREYXRhKGVsZW1lbnQsIG5hbWUpIHtcbiAgICBpZiAoaXNPYmplY3QoZWxlbWVudFtuYW1lXSkpIHtcbiAgICAgIHJldHVybiBlbGVtZW50W25hbWVdO1xuICAgIH1cbiAgICBpZiAoZWxlbWVudC5kYXRhc2V0KSB7XG4gICAgICByZXR1cm4gZWxlbWVudC5kYXRhc2V0W25hbWVdO1xuICAgIH1cbiAgICByZXR1cm4gZWxlbWVudC5nZXRBdHRyaWJ1dGUoXCJkYXRhLVwiLmNvbmNhdCh0b1BhcmFtQ2FzZShuYW1lKSkpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNldCBkYXRhIHRvIHRoZSBnaXZlbiBlbGVtZW50LlxuICAgKiBAcGFyYW0ge0VsZW1lbnR9IGVsZW1lbnQgLSBUaGUgdGFyZ2V0IGVsZW1lbnQuXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBuYW1lIC0gVGhlIGRhdGEga2V5IHRvIHNldC5cbiAgICogQHBhcmFtIHtzdHJpbmd9IGRhdGEgLSBUaGUgZGF0YSB2YWx1ZS5cbiAgICovXG4gIGZ1bmN0aW9uIHNldERhdGEoZWxlbWVudCwgbmFtZSwgZGF0YSkge1xuICAgIGlmIChpc09iamVjdChkYXRhKSkge1xuICAgICAgZWxlbWVudFtuYW1lXSA9IGRhdGE7XG4gICAgfSBlbHNlIGlmIChlbGVtZW50LmRhdGFzZXQpIHtcbiAgICAgIGVsZW1lbnQuZGF0YXNldFtuYW1lXSA9IGRhdGE7XG4gICAgfSBlbHNlIHtcbiAgICAgIGVsZW1lbnQuc2V0QXR0cmlidXRlKFwiZGF0YS1cIi5jb25jYXQodG9QYXJhbUNhc2UobmFtZSkpLCBkYXRhKTtcbiAgICB9XG4gIH1cblxuICAvKipcbiAgICogUmVtb3ZlIGRhdGEgZnJvbSB0aGUgZ2l2ZW4gZWxlbWVudC5cbiAgICogQHBhcmFtIHtFbGVtZW50fSBlbGVtZW50IC0gVGhlIHRhcmdldCBlbGVtZW50LlxuICAgKiBAcGFyYW0ge3N0cmluZ30gbmFtZSAtIFRoZSBkYXRhIGtleSB0byByZW1vdmUuXG4gICAqL1xuICBmdW5jdGlvbiByZW1vdmVEYXRhKGVsZW1lbnQsIG5hbWUpIHtcbiAgICBpZiAoaXNPYmplY3QoZWxlbWVudFtuYW1lXSkpIHtcbiAgICAgIHRyeSB7XG4gICAgICAgIGRlbGV0ZSBlbGVtZW50W25hbWVdO1xuICAgICAgfSBjYXRjaCAoZXJyb3IpIHtcbiAgICAgICAgZWxlbWVudFtuYW1lXSA9IHVuZGVmaW5lZDtcbiAgICAgIH1cbiAgICB9IGVsc2UgaWYgKGVsZW1lbnQuZGF0YXNldCkge1xuICAgICAgLy8gIzEyOCBTYWZhcmkgbm90IGFsbG93cyB0byBkZWxldGUgZGF0YXNldCBwcm9wZXJ0eVxuICAgICAgdHJ5IHtcbiAgICAgICAgZGVsZXRlIGVsZW1lbnQuZGF0YXNldFtuYW1lXTtcbiAgICAgIH0gY2F0Y2ggKGVycm9yKSB7XG4gICAgICAgIGVsZW1lbnQuZGF0YXNldFtuYW1lXSA9IHVuZGVmaW5lZDtcbiAgICAgIH1cbiAgICB9IGVsc2Uge1xuICAgICAgZWxlbWVudC5yZW1vdmVBdHRyaWJ1dGUoXCJkYXRhLVwiLmNvbmNhdCh0b1BhcmFtQ2FzZShuYW1lKSkpO1xuICAgIH1cbiAgfVxuICB2YXIgUkVHRVhQX1NQQUNFUyA9IC9cXHNcXHMqLztcbiAgdmFyIG9uY2VTdXBwb3J0ZWQgPSBmdW5jdGlvbiAoKSB7XG4gICAgdmFyIHN1cHBvcnRlZCA9IGZhbHNlO1xuICAgIGlmIChJU19CUk9XU0VSKSB7XG4gICAgICB2YXIgb25jZSA9IGZhbHNlO1xuICAgICAgdmFyIGxpc3RlbmVyID0gZnVuY3Rpb24gbGlzdGVuZXIoKSB7fTtcbiAgICAgIHZhciBvcHRpb25zID0gT2JqZWN0LmRlZmluZVByb3BlcnR5KHt9LCAnb25jZScsIHtcbiAgICAgICAgZ2V0OiBmdW5jdGlvbiBnZXQoKSB7XG4gICAgICAgICAgc3VwcG9ydGVkID0gdHJ1ZTtcbiAgICAgICAgICByZXR1cm4gb25jZTtcbiAgICAgICAgfSxcbiAgICAgICAgLyoqXG4gICAgICAgICAqIFRoaXMgc2V0dGVyIGNhbiBmaXggYSBgVHlwZUVycm9yYCBpbiBzdHJpY3QgbW9kZVxuICAgICAgICAgKiB7QGxpbmsgaHR0cHM6Ly9kZXZlbG9wZXIubW96aWxsYS5vcmcvZW4tVVMvZG9jcy9XZWIvSmF2YVNjcmlwdC9SZWZlcmVuY2UvRXJyb3JzL0dldHRlcl9vbmx5fVxuICAgICAgICAgKiBAcGFyYW0ge2Jvb2xlYW59IHZhbHVlIC0gVGhlIHZhbHVlIHRvIHNldFxuICAgICAgICAgKi9cbiAgICAgICAgc2V0OiBmdW5jdGlvbiBzZXQodmFsdWUpIHtcbiAgICAgICAgICBvbmNlID0gdmFsdWU7XG4gICAgICAgIH1cbiAgICAgIH0pO1xuICAgICAgV0lORE9XLmFkZEV2ZW50TGlzdGVuZXIoJ3Rlc3QnLCBsaXN0ZW5lciwgb3B0aW9ucyk7XG4gICAgICBXSU5ET1cucmVtb3ZlRXZlbnRMaXN0ZW5lcigndGVzdCcsIGxpc3RlbmVyLCBvcHRpb25zKTtcbiAgICB9XG4gICAgcmV0dXJuIHN1cHBvcnRlZDtcbiAgfSgpO1xuXG4gIC8qKlxuICAgKiBSZW1vdmUgZXZlbnQgbGlzdGVuZXIgZnJvbSB0aGUgdGFyZ2V0IGVsZW1lbnQuXG4gICAqIEBwYXJhbSB7RWxlbWVudH0gZWxlbWVudCAtIFRoZSBldmVudCB0YXJnZXQuXG4gICAqIEBwYXJhbSB7c3RyaW5nfSB0eXBlIC0gVGhlIGV2ZW50IHR5cGUocykuXG4gICAqIEBwYXJhbSB7RnVuY3Rpb259IGxpc3RlbmVyIC0gVGhlIGV2ZW50IGxpc3RlbmVyLlxuICAgKiBAcGFyYW0ge09iamVjdH0gb3B0aW9ucyAtIFRoZSBldmVudCBvcHRpb25zLlxuICAgKi9cbiAgZnVuY3Rpb24gcmVtb3ZlTGlzdGVuZXIoZWxlbWVudCwgdHlwZSwgbGlzdGVuZXIpIHtcbiAgICB2YXIgb3B0aW9ucyA9IGFyZ3VtZW50cy5sZW5ndGggPiAzICYmIGFyZ3VtZW50c1szXSAhPT0gdW5kZWZpbmVkID8gYXJndW1lbnRzWzNdIDoge307XG4gICAgdmFyIGhhbmRsZXIgPSBsaXN0ZW5lcjtcbiAgICB0eXBlLnRyaW0oKS5zcGxpdChSRUdFWFBfU1BBQ0VTKS5mb3JFYWNoKGZ1bmN0aW9uIChldmVudCkge1xuICAgICAgaWYgKCFvbmNlU3VwcG9ydGVkKSB7XG4gICAgICAgIHZhciBsaXN0ZW5lcnMgPSBlbGVtZW50Lmxpc3RlbmVycztcbiAgICAgICAgaWYgKGxpc3RlbmVycyAmJiBsaXN0ZW5lcnNbZXZlbnRdICYmIGxpc3RlbmVyc1tldmVudF1bbGlzdGVuZXJdKSB7XG4gICAgICAgICAgaGFuZGxlciA9IGxpc3RlbmVyc1tldmVudF1bbGlzdGVuZXJdO1xuICAgICAgICAgIGRlbGV0ZSBsaXN0ZW5lcnNbZXZlbnRdW2xpc3RlbmVyXTtcbiAgICAgICAgICBpZiAoT2JqZWN0LmtleXMobGlzdGVuZXJzW2V2ZW50XSkubGVuZ3RoID09PSAwKSB7XG4gICAgICAgICAgICBkZWxldGUgbGlzdGVuZXJzW2V2ZW50XTtcbiAgICAgICAgICB9XG4gICAgICAgICAgaWYgKE9iamVjdC5rZXlzKGxpc3RlbmVycykubGVuZ3RoID09PSAwKSB7XG4gICAgICAgICAgICBkZWxldGUgZWxlbWVudC5saXN0ZW5lcnM7XG4gICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICB9XG4gICAgICBlbGVtZW50LnJlbW92ZUV2ZW50TGlzdGVuZXIoZXZlbnQsIGhhbmRsZXIsIG9wdGlvbnMpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEFkZCBldmVudCBsaXN0ZW5lciB0byB0aGUgdGFyZ2V0IGVsZW1lbnQuXG4gICAqIEBwYXJhbSB7RWxlbWVudH0gZWxlbWVudCAtIFRoZSBldmVudCB0YXJnZXQuXG4gICAqIEBwYXJhbSB7c3RyaW5nfSB0eXBlIC0gVGhlIGV2ZW50IHR5cGUocykuXG4gICAqIEBwYXJhbSB7RnVuY3Rpb259IGxpc3RlbmVyIC0gVGhlIGV2ZW50IGxpc3RlbmVyLlxuICAgKiBAcGFyYW0ge09iamVjdH0gb3B0aW9ucyAtIFRoZSBldmVudCBvcHRpb25zLlxuICAgKi9cbiAgZnVuY3Rpb24gYWRkTGlzdGVuZXIoZWxlbWVudCwgdHlwZSwgbGlzdGVuZXIpIHtcbiAgICB2YXIgb3B0aW9ucyA9IGFyZ3VtZW50cy5sZW5ndGggPiAzICYmIGFyZ3VtZW50c1szXSAhPT0gdW5kZWZpbmVkID8gYXJndW1lbnRzWzNdIDoge307XG4gICAgdmFyIF9oYW5kbGVyID0gbGlzdGVuZXI7XG4gICAgdHlwZS50cmltKCkuc3BsaXQoUkVHRVhQX1NQQUNFUykuZm9yRWFjaChmdW5jdGlvbiAoZXZlbnQpIHtcbiAgICAgIGlmIChvcHRpb25zLm9uY2UgJiYgIW9uY2VTdXBwb3J0ZWQpIHtcbiAgICAgICAgdmFyIF9lbGVtZW50JGxpc3RlbmVycyA9IGVsZW1lbnQubGlzdGVuZXJzLFxuICAgICAgICAgIGxpc3RlbmVycyA9IF9lbGVtZW50JGxpc3RlbmVycyA9PT0gdm9pZCAwID8ge30gOiBfZWxlbWVudCRsaXN0ZW5lcnM7XG4gICAgICAgIF9oYW5kbGVyID0gZnVuY3Rpb24gaGFuZGxlcigpIHtcbiAgICAgICAgICBkZWxldGUgbGlzdGVuZXJzW2V2ZW50XVtsaXN0ZW5lcl07XG4gICAgICAgICAgZWxlbWVudC5yZW1vdmVFdmVudExpc3RlbmVyKGV2ZW50LCBfaGFuZGxlciwgb3B0aW9ucyk7XG4gICAgICAgICAgZm9yICh2YXIgX2xlbjIgPSBhcmd1bWVudHMubGVuZ3RoLCBhcmdzID0gbmV3IEFycmF5KF9sZW4yKSwgX2tleTIgPSAwOyBfa2V5MiA8IF9sZW4yOyBfa2V5MisrKSB7XG4gICAgICAgICAgICBhcmdzW19rZXkyXSA9IGFyZ3VtZW50c1tfa2V5Ml07XG4gICAgICAgICAgfVxuICAgICAgICAgIGxpc3RlbmVyLmFwcGx5KGVsZW1lbnQsIGFyZ3MpO1xuICAgICAgICB9O1xuICAgICAgICBpZiAoIWxpc3RlbmVyc1tldmVudF0pIHtcbiAgICAgICAgICBsaXN0ZW5lcnNbZXZlbnRdID0ge307XG4gICAgICAgIH1cbiAgICAgICAgaWYgKGxpc3RlbmVyc1tldmVudF1bbGlzdGVuZXJdKSB7XG4gICAgICAgICAgZWxlbWVudC5yZW1vdmVFdmVudExpc3RlbmVyKGV2ZW50LCBsaXN0ZW5lcnNbZXZlbnRdW2xpc3RlbmVyXSwgb3B0aW9ucyk7XG4gICAgICAgIH1cbiAgICAgICAgbGlzdGVuZXJzW2V2ZW50XVtsaXN0ZW5lcl0gPSBfaGFuZGxlcjtcbiAgICAgICAgZWxlbWVudC5saXN0ZW5lcnMgPSBsaXN0ZW5lcnM7XG4gICAgICB9XG4gICAgICBlbGVtZW50LmFkZEV2ZW50TGlzdGVuZXIoZXZlbnQsIF9oYW5kbGVyLCBvcHRpb25zKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBEaXNwYXRjaCBldmVudCBvbiB0aGUgdGFyZ2V0IGVsZW1lbnQuXG4gICAqIEBwYXJhbSB7RWxlbWVudH0gZWxlbWVudCAtIFRoZSBldmVudCB0YXJnZXQuXG4gICAqIEBwYXJhbSB7c3RyaW5nfSB0eXBlIC0gVGhlIGV2ZW50IHR5cGUocykuXG4gICAqIEBwYXJhbSB7T2JqZWN0fSBkYXRhIC0gVGhlIGFkZGl0aW9uYWwgZXZlbnQgZGF0YS5cbiAgICogQHJldHVybnMge2Jvb2xlYW59IEluZGljYXRlIGlmIHRoZSBldmVudCBpcyBkZWZhdWx0IHByZXZlbnRlZCBvciBub3QuXG4gICAqL1xuICBmdW5jdGlvbiBkaXNwYXRjaEV2ZW50KGVsZW1lbnQsIHR5cGUsIGRhdGEpIHtcbiAgICB2YXIgZXZlbnQ7XG5cbiAgICAvLyBFdmVudCBhbmQgQ3VzdG9tRXZlbnQgb24gSUU5LTExIGFyZSBnbG9iYWwgb2JqZWN0cywgbm90IGNvbnN0cnVjdG9yc1xuICAgIGlmIChpc0Z1bmN0aW9uKEV2ZW50KSAmJiBpc0Z1bmN0aW9uKEN1c3RvbUV2ZW50KSkge1xuICAgICAgZXZlbnQgPSBuZXcgQ3VzdG9tRXZlbnQodHlwZSwge1xuICAgICAgICBkZXRhaWw6IGRhdGEsXG4gICAgICAgIGJ1YmJsZXM6IHRydWUsXG4gICAgICAgIGNhbmNlbGFibGU6IHRydWVcbiAgICAgIH0pO1xuICAgIH0gZWxzZSB7XG4gICAgICBldmVudCA9IGRvY3VtZW50LmNyZWF0ZUV2ZW50KCdDdXN0b21FdmVudCcpO1xuICAgICAgZXZlbnQuaW5pdEN1c3RvbUV2ZW50KHR5cGUsIHRydWUsIHRydWUsIGRhdGEpO1xuICAgIH1cbiAgICByZXR1cm4gZWxlbWVudC5kaXNwYXRjaEV2ZW50KGV2ZW50KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIG9mZnNldCBiYXNlIG9uIHRoZSBkb2N1bWVudC5cbiAgICogQHBhcmFtIHtFbGVtZW50fSBlbGVtZW50IC0gVGhlIHRhcmdldCBlbGVtZW50LlxuICAgKiBAcmV0dXJucyB7T2JqZWN0fSBUaGUgb2Zmc2V0IGRhdGEuXG4gICAqL1xuICBmdW5jdGlvbiBnZXRPZmZzZXQoZWxlbWVudCkge1xuICAgIHZhciBib3ggPSBlbGVtZW50LmdldEJvdW5kaW5nQ2xpZW50UmVjdCgpO1xuICAgIHJldHVybiB7XG4gICAgICBsZWZ0OiBib3gubGVmdCArICh3aW5kb3cucGFnZVhPZmZzZXQgLSBkb2N1bWVudC5kb2N1bWVudEVsZW1lbnQuY2xpZW50TGVmdCksXG4gICAgICB0b3A6IGJveC50b3AgKyAod2luZG93LnBhZ2VZT2Zmc2V0IC0gZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50LmNsaWVudFRvcClcbiAgICB9O1xuICB9XG4gIHZhciBsb2NhdGlvbiA9IFdJTkRPVy5sb2NhdGlvbjtcbiAgdmFyIFJFR0VYUF9PUklHSU5TID0gL14oXFx3KzopXFwvXFwvKFteOi8/I10qKTo/KFxcZCopL2k7XG5cbiAgLyoqXG4gICAqIENoZWNrIGlmIHRoZSBnaXZlbiBVUkwgaXMgYSBjcm9zcyBvcmlnaW4gVVJMLlxuICAgKiBAcGFyYW0ge3N0cmluZ30gdXJsIC0gVGhlIHRhcmdldCBVUkwuXG4gICAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiB0aGUgZ2l2ZW4gVVJMIGlzIGEgY3Jvc3Mgb3JpZ2luIFVSTCwgZWxzZSBgZmFsc2VgLlxuICAgKi9cbiAgZnVuY3Rpb24gaXNDcm9zc09yaWdpblVSTCh1cmwpIHtcbiAgICB2YXIgcGFydHMgPSB1cmwubWF0Y2goUkVHRVhQX09SSUdJTlMpO1xuICAgIHJldHVybiBwYXJ0cyAhPT0gbnVsbCAmJiAocGFydHNbMV0gIT09IGxvY2F0aW9uLnByb3RvY29sIHx8IHBhcnRzWzJdICE9PSBsb2NhdGlvbi5ob3N0bmFtZSB8fCBwYXJ0c1szXSAhPT0gbG9jYXRpb24ucG9ydCk7XG4gIH1cblxuICAvKipcbiAgICogQWRkIHRpbWVzdGFtcCB0byB0aGUgZ2l2ZW4gVVJMLlxuICAgKiBAcGFyYW0ge3N0cmluZ30gdXJsIC0gVGhlIHRhcmdldCBVUkwuXG4gICAqIEByZXR1cm5zIHtzdHJpbmd9IFRoZSByZXN1bHQgVVJMLlxuICAgKi9cbiAgZnVuY3Rpb24gYWRkVGltZXN0YW1wKHVybCkge1xuICAgIHZhciB0aW1lc3RhbXAgPSBcInRpbWVzdGFtcD1cIi5jb25jYXQobmV3IERhdGUoKS5nZXRUaW1lKCkpO1xuICAgIHJldHVybiB1cmwgKyAodXJsLmluZGV4T2YoJz8nKSA9PT0gLTEgPyAnPycgOiAnJicpICsgdGltZXN0YW1wO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0cmFuc2Zvcm1zIGJhc2Ugb24gdGhlIGdpdmVuIG9iamVjdC5cbiAgICogQHBhcmFtIHtPYmplY3R9IG9iaiAtIFRoZSB0YXJnZXQgb2JqZWN0LlxuICAgKiBAcmV0dXJucyB7c3RyaW5nfSBBIHN0cmluZyBjb250YWlucyB0cmFuc2Zvcm0gdmFsdWVzLlxuICAgKi9cbiAgZnVuY3Rpb24gZ2V0VHJhbnNmb3JtcyhfcmVmKSB7XG4gICAgdmFyIHJvdGF0ZSA9IF9yZWYucm90YXRlLFxuICAgICAgc2NhbGVYID0gX3JlZi5zY2FsZVgsXG4gICAgICBzY2FsZVkgPSBfcmVmLnNjYWxlWSxcbiAgICAgIHRyYW5zbGF0ZVggPSBfcmVmLnRyYW5zbGF0ZVgsXG4gICAgICB0cmFuc2xhdGVZID0gX3JlZi50cmFuc2xhdGVZO1xuICAgIHZhciB2YWx1ZXMgPSBbXTtcbiAgICBpZiAoaXNOdW1iZXIodHJhbnNsYXRlWCkgJiYgdHJhbnNsYXRlWCAhPT0gMCkge1xuICAgICAgdmFsdWVzLnB1c2goXCJ0cmFuc2xhdGVYKFwiLmNvbmNhdCh0cmFuc2xhdGVYLCBcInB4KVwiKSk7XG4gICAgfVxuICAgIGlmIChpc051bWJlcih0cmFuc2xhdGVZKSAmJiB0cmFuc2xhdGVZICE9PSAwKSB7XG4gICAgICB2YWx1ZXMucHVzaChcInRyYW5zbGF0ZVkoXCIuY29uY2F0KHRyYW5zbGF0ZVksIFwicHgpXCIpKTtcbiAgICB9XG5cbiAgICAvLyBSb3RhdGUgc2hvdWxkIGNvbWUgZmlyc3QgYmVmb3JlIHNjYWxlIHRvIG1hdGNoIG9yaWVudGF0aW9uIHRyYW5zZm9ybVxuICAgIGlmIChpc051bWJlcihyb3RhdGUpICYmIHJvdGF0ZSAhPT0gMCkge1xuICAgICAgdmFsdWVzLnB1c2goXCJyb3RhdGUoXCIuY29uY2F0KHJvdGF0ZSwgXCJkZWcpXCIpKTtcbiAgICB9XG4gICAgaWYgKGlzTnVtYmVyKHNjYWxlWCkgJiYgc2NhbGVYICE9PSAxKSB7XG4gICAgICB2YWx1ZXMucHVzaChcInNjYWxlWChcIi5jb25jYXQoc2NhbGVYLCBcIilcIikpO1xuICAgIH1cbiAgICBpZiAoaXNOdW1iZXIoc2NhbGVZKSAmJiBzY2FsZVkgIT09IDEpIHtcbiAgICAgIHZhbHVlcy5wdXNoKFwic2NhbGVZKFwiLmNvbmNhdChzY2FsZVksIFwiKVwiKSk7XG4gICAgfVxuICAgIHZhciB0cmFuc2Zvcm0gPSB2YWx1ZXMubGVuZ3RoID8gdmFsdWVzLmpvaW4oJyAnKSA6ICdub25lJztcbiAgICByZXR1cm4ge1xuICAgICAgV2Via2l0VHJhbnNmb3JtOiB0cmFuc2Zvcm0sXG4gICAgICBtc1RyYW5zZm9ybTogdHJhbnNmb3JtLFxuICAgICAgdHJhbnNmb3JtOiB0cmFuc2Zvcm1cbiAgICB9O1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgbWF4IHJhdGlvIG9mIGEgZ3JvdXAgb2YgcG9pbnRlcnMuXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBwb2ludGVycyAtIFRoZSB0YXJnZXQgcG9pbnRlcnMuXG4gICAqIEByZXR1cm5zIHtudW1iZXJ9IFRoZSByZXN1bHQgcmF0aW8uXG4gICAqL1xuICBmdW5jdGlvbiBnZXRNYXhab29tUmF0aW8ocG9pbnRlcnMpIHtcbiAgICB2YXIgcG9pbnRlcnMyID0gX29iamVjdFNwcmVhZDIoe30sIHBvaW50ZXJzKTtcbiAgICB2YXIgbWF4UmF0aW8gPSAwO1xuICAgIGZvckVhY2gocG9pbnRlcnMsIGZ1bmN0aW9uIChwb2ludGVyLCBwb2ludGVySWQpIHtcbiAgICAgIGRlbGV0ZSBwb2ludGVyczJbcG9pbnRlcklkXTtcbiAgICAgIGZvckVhY2gocG9pbnRlcnMyLCBmdW5jdGlvbiAocG9pbnRlcjIpIHtcbiAgICAgICAgdmFyIHgxID0gTWF0aC5hYnMocG9pbnRlci5zdGFydFggLSBwb2ludGVyMi5zdGFydFgpO1xuICAgICAgICB2YXIgeTEgPSBNYXRoLmFicyhwb2ludGVyLnN0YXJ0WSAtIHBvaW50ZXIyLnN0YXJ0WSk7XG4gICAgICAgIHZhciB4MiA9IE1hdGguYWJzKHBvaW50ZXIuZW5kWCAtIHBvaW50ZXIyLmVuZFgpO1xuICAgICAgICB2YXIgeTIgPSBNYXRoLmFicyhwb2ludGVyLmVuZFkgLSBwb2ludGVyMi5lbmRZKTtcbiAgICAgICAgdmFyIHoxID0gTWF0aC5zcXJ0KHgxICogeDEgKyB5MSAqIHkxKTtcbiAgICAgICAgdmFyIHoyID0gTWF0aC5zcXJ0KHgyICogeDIgKyB5MiAqIHkyKTtcbiAgICAgICAgdmFyIHJhdGlvID0gKHoyIC0gejEpIC8gejE7XG4gICAgICAgIGlmIChNYXRoLmFicyhyYXRpbykgPiBNYXRoLmFicyhtYXhSYXRpbykpIHtcbiAgICAgICAgICBtYXhSYXRpbyA9IHJhdGlvO1xuICAgICAgICB9XG4gICAgICB9KTtcbiAgICB9KTtcbiAgICByZXR1cm4gbWF4UmF0aW87XG4gIH1cblxuICAvKipcbiAgICogR2V0IGEgcG9pbnRlciBmcm9tIGFuIGV2ZW50IG9iamVjdC5cbiAgICogQHBhcmFtIHtPYmplY3R9IGV2ZW50IC0gVGhlIHRhcmdldCBldmVudCBvYmplY3QuXG4gICAqIEBwYXJhbSB7Ym9vbGVhbn0gZW5kT25seSAtIEluZGljYXRlcyBpZiBvbmx5IHJldHVybnMgdGhlIGVuZCBwb2ludCBjb29yZGluYXRlIG9yIG5vdC5cbiAgICogQHJldHVybnMge09iamVjdH0gVGhlIHJlc3VsdCBwb2ludGVyIGNvbnRhaW5zIHN0YXJ0IGFuZC9vciBlbmQgcG9pbnQgY29vcmRpbmF0ZXMuXG4gICAqL1xuICBmdW5jdGlvbiBnZXRQb2ludGVyKF9yZWYyLCBlbmRPbmx5KSB7XG4gICAgdmFyIHBhZ2VYID0gX3JlZjIucGFnZVgsXG4gICAgICBwYWdlWSA9IF9yZWYyLnBhZ2VZO1xuICAgIHZhciBlbmQgPSB7XG4gICAgICBlbmRYOiBwYWdlWCxcbiAgICAgIGVuZFk6IHBhZ2VZXG4gICAgfTtcbiAgICByZXR1cm4gZW5kT25seSA/IGVuZCA6IF9vYmplY3RTcHJlYWQyKHtcbiAgICAgIHN0YXJ0WDogcGFnZVgsXG4gICAgICBzdGFydFk6IHBhZ2VZXG4gICAgfSwgZW5kKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIGNlbnRlciBwb2ludCBjb29yZGluYXRlIG9mIGEgZ3JvdXAgb2YgcG9pbnRlcnMuXG4gICAqIEBwYXJhbSB7T2JqZWN0fSBwb2ludGVycyAtIFRoZSB0YXJnZXQgcG9pbnRlcnMuXG4gICAqIEByZXR1cm5zIHtPYmplY3R9IFRoZSBjZW50ZXIgcG9pbnQgY29vcmRpbmF0ZS5cbiAgICovXG4gIGZ1bmN0aW9uIGdldFBvaW50ZXJzQ2VudGVyKHBvaW50ZXJzKSB7XG4gICAgdmFyIHBhZ2VYID0gMDtcbiAgICB2YXIgcGFnZVkgPSAwO1xuICAgIHZhciBjb3VudCA9IDA7XG4gICAgZm9yRWFjaChwb2ludGVycywgZnVuY3Rpb24gKF9yZWYzKSB7XG4gICAgICB2YXIgc3RhcnRYID0gX3JlZjMuc3RhcnRYLFxuICAgICAgICBzdGFydFkgPSBfcmVmMy5zdGFydFk7XG4gICAgICBwYWdlWCArPSBzdGFydFg7XG4gICAgICBwYWdlWSArPSBzdGFydFk7XG4gICAgICBjb3VudCArPSAxO1xuICAgIH0pO1xuICAgIHBhZ2VYIC89IGNvdW50O1xuICAgIHBhZ2VZIC89IGNvdW50O1xuICAgIHJldHVybiB7XG4gICAgICBwYWdlWDogcGFnZVgsXG4gICAgICBwYWdlWTogcGFnZVlcbiAgICB9O1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgbWF4IHNpemVzIGluIGEgcmVjdGFuZ2xlIHVuZGVyIHRoZSBnaXZlbiBhc3BlY3QgcmF0aW8uXG4gICAqIEBwYXJhbSB7T2JqZWN0fSBkYXRhIC0gVGhlIG9yaWdpbmFsIHNpemVzLlxuICAgKiBAcGFyYW0ge3N0cmluZ30gW3R5cGU9J2NvbnRhaW4nXSAtIFRoZSBhZGp1c3QgdHlwZS5cbiAgICogQHJldHVybnMge09iamVjdH0gVGhlIHJlc3VsdCBzaXplcy5cbiAgICovXG4gIGZ1bmN0aW9uIGdldEFkanVzdGVkU2l6ZXMoX3JlZjQpIHtcbiAgICB2YXIgYXNwZWN0UmF0aW8gPSBfcmVmNC5hc3BlY3RSYXRpbyxcbiAgICAgIGhlaWdodCA9IF9yZWY0LmhlaWdodCxcbiAgICAgIHdpZHRoID0gX3JlZjQud2lkdGg7XG4gICAgdmFyIHR5cGUgPSBhcmd1bWVudHMubGVuZ3RoID4gMSAmJiBhcmd1bWVudHNbMV0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1sxXSA6ICdjb250YWluJztcbiAgICB2YXIgaXNWYWxpZFdpZHRoID0gaXNQb3NpdGl2ZU51bWJlcih3aWR0aCk7XG4gICAgdmFyIGlzVmFsaWRIZWlnaHQgPSBpc1Bvc2l0aXZlTnVtYmVyKGhlaWdodCk7XG4gICAgaWYgKGlzVmFsaWRXaWR0aCAmJiBpc1ZhbGlkSGVpZ2h0KSB7XG4gICAgICB2YXIgYWRqdXN0ZWRXaWR0aCA9IGhlaWdodCAqIGFzcGVjdFJhdGlvO1xuICAgICAgaWYgKHR5cGUgPT09ICdjb250YWluJyAmJiBhZGp1c3RlZFdpZHRoID4gd2lkdGggfHwgdHlwZSA9PT0gJ2NvdmVyJyAmJiBhZGp1c3RlZFdpZHRoIDwgd2lkdGgpIHtcbiAgICAgICAgaGVpZ2h0ID0gd2lkdGggLyBhc3BlY3RSYXRpbztcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIHdpZHRoID0gaGVpZ2h0ICogYXNwZWN0UmF0aW87XG4gICAgICB9XG4gICAgfSBlbHNlIGlmIChpc1ZhbGlkV2lkdGgpIHtcbiAgICAgIGhlaWdodCA9IHdpZHRoIC8gYXNwZWN0UmF0aW87XG4gICAgfSBlbHNlIGlmIChpc1ZhbGlkSGVpZ2h0KSB7XG4gICAgICB3aWR0aCA9IGhlaWdodCAqIGFzcGVjdFJhdGlvO1xuICAgIH1cbiAgICByZXR1cm4ge1xuICAgICAgd2lkdGg6IHdpZHRoLFxuICAgICAgaGVpZ2h0OiBoZWlnaHRcbiAgICB9O1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgbmV3IHNpemVzIG9mIGEgcmVjdGFuZ2xlIGFmdGVyIHJvdGF0ZWQuXG4gICAqIEBwYXJhbSB7T2JqZWN0fSBkYXRhIC0gVGhlIG9yaWdpbmFsIHNpemVzLlxuICAgKiBAcmV0dXJucyB7T2JqZWN0fSBUaGUgcmVzdWx0IHNpemVzLlxuICAgKi9cbiAgZnVuY3Rpb24gZ2V0Um90YXRlZFNpemVzKF9yZWY1KSB7XG4gICAgdmFyIHdpZHRoID0gX3JlZjUud2lkdGgsXG4gICAgICBoZWlnaHQgPSBfcmVmNS5oZWlnaHQsXG4gICAgICBkZWdyZWUgPSBfcmVmNS5kZWdyZWU7XG4gICAgZGVncmVlID0gTWF0aC5hYnMoZGVncmVlKSAlIDE4MDtcbiAgICBpZiAoZGVncmVlID09PSA5MCkge1xuICAgICAgcmV0dXJuIHtcbiAgICAgICAgd2lkdGg6IGhlaWdodCxcbiAgICAgICAgaGVpZ2h0OiB3aWR0aFxuICAgICAgfTtcbiAgICB9XG4gICAgdmFyIGFyYyA9IGRlZ3JlZSAlIDkwICogTWF0aC5QSSAvIDE4MDtcbiAgICB2YXIgc2luQXJjID0gTWF0aC5zaW4oYXJjKTtcbiAgICB2YXIgY29zQXJjID0gTWF0aC5jb3MoYXJjKTtcbiAgICB2YXIgbmV3V2lkdGggPSB3aWR0aCAqIGNvc0FyYyArIGhlaWdodCAqIHNpbkFyYztcbiAgICB2YXIgbmV3SGVpZ2h0ID0gd2lkdGggKiBzaW5BcmMgKyBoZWlnaHQgKiBjb3NBcmM7XG4gICAgcmV0dXJuIGRlZ3JlZSA+IDkwID8ge1xuICAgICAgd2lkdGg6IG5ld0hlaWdodCxcbiAgICAgIGhlaWdodDogbmV3V2lkdGhcbiAgICB9IDoge1xuICAgICAgd2lkdGg6IG5ld1dpZHRoLFxuICAgICAgaGVpZ2h0OiBuZXdIZWlnaHRcbiAgICB9O1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCBhIGNhbnZhcyB3aGljaCBkcmV3IHRoZSBnaXZlbiBpbWFnZS5cbiAgICogQHBhcmFtIHtIVE1MSW1hZ2VFbGVtZW50fSBpbWFnZSAtIFRoZSBpbWFnZSBmb3IgZHJhd2luZy5cbiAgICogQHBhcmFtIHtPYmplY3R9IGltYWdlRGF0YSAtIFRoZSBpbWFnZSBkYXRhLlxuICAgKiBAcGFyYW0ge09iamVjdH0gY2FudmFzRGF0YSAtIFRoZSBjYW52YXMgZGF0YS5cbiAgICogQHBhcmFtIHtPYmplY3R9IG9wdGlvbnMgLSBUaGUgb3B0aW9ucy5cbiAgICogQHJldHVybnMge0hUTUxDYW52YXNFbGVtZW50fSBUaGUgcmVzdWx0IGNhbnZhcy5cbiAgICovXG4gIGZ1bmN0aW9uIGdldFNvdXJjZUNhbnZhcyhpbWFnZSwgX3JlZjYsIF9yZWY3LCBfcmVmOCkge1xuICAgIHZhciBpbWFnZUFzcGVjdFJhdGlvID0gX3JlZjYuYXNwZWN0UmF0aW8sXG4gICAgICBpbWFnZU5hdHVyYWxXaWR0aCA9IF9yZWY2Lm5hdHVyYWxXaWR0aCxcbiAgICAgIGltYWdlTmF0dXJhbEhlaWdodCA9IF9yZWY2Lm5hdHVyYWxIZWlnaHQsXG4gICAgICBfcmVmNiRyb3RhdGUgPSBfcmVmNi5yb3RhdGUsXG4gICAgICByb3RhdGUgPSBfcmVmNiRyb3RhdGUgPT09IHZvaWQgMCA/IDAgOiBfcmVmNiRyb3RhdGUsXG4gICAgICBfcmVmNiRzY2FsZVggPSBfcmVmNi5zY2FsZVgsXG4gICAgICBzY2FsZVggPSBfcmVmNiRzY2FsZVggPT09IHZvaWQgMCA/IDEgOiBfcmVmNiRzY2FsZVgsXG4gICAgICBfcmVmNiRzY2FsZVkgPSBfcmVmNi5zY2FsZVksXG4gICAgICBzY2FsZVkgPSBfcmVmNiRzY2FsZVkgPT09IHZvaWQgMCA/IDEgOiBfcmVmNiRzY2FsZVk7XG4gICAgdmFyIGFzcGVjdFJhdGlvID0gX3JlZjcuYXNwZWN0UmF0aW8sXG4gICAgICBuYXR1cmFsV2lkdGggPSBfcmVmNy5uYXR1cmFsV2lkdGgsXG4gICAgICBuYXR1cmFsSGVpZ2h0ID0gX3JlZjcubmF0dXJhbEhlaWdodDtcbiAgICB2YXIgX3JlZjgkZmlsbENvbG9yID0gX3JlZjguZmlsbENvbG9yLFxuICAgICAgZmlsbENvbG9yID0gX3JlZjgkZmlsbENvbG9yID09PSB2b2lkIDAgPyAndHJhbnNwYXJlbnQnIDogX3JlZjgkZmlsbENvbG9yLFxuICAgICAgX3JlZjgkaW1hZ2VTbW9vdGhpbmdFID0gX3JlZjguaW1hZ2VTbW9vdGhpbmdFbmFibGVkLFxuICAgICAgaW1hZ2VTbW9vdGhpbmdFbmFibGVkID0gX3JlZjgkaW1hZ2VTbW9vdGhpbmdFID09PSB2b2lkIDAgPyB0cnVlIDogX3JlZjgkaW1hZ2VTbW9vdGhpbmdFLFxuICAgICAgX3JlZjgkaW1hZ2VTbW9vdGhpbmdRID0gX3JlZjguaW1hZ2VTbW9vdGhpbmdRdWFsaXR5LFxuICAgICAgaW1hZ2VTbW9vdGhpbmdRdWFsaXR5ID0gX3JlZjgkaW1hZ2VTbW9vdGhpbmdRID09PSB2b2lkIDAgPyAnbG93JyA6IF9yZWY4JGltYWdlU21vb3RoaW5nUSxcbiAgICAgIF9yZWY4JG1heFdpZHRoID0gX3JlZjgubWF4V2lkdGgsXG4gICAgICBtYXhXaWR0aCA9IF9yZWY4JG1heFdpZHRoID09PSB2b2lkIDAgPyBJbmZpbml0eSA6IF9yZWY4JG1heFdpZHRoLFxuICAgICAgX3JlZjgkbWF4SGVpZ2h0ID0gX3JlZjgubWF4SGVpZ2h0LFxuICAgICAgbWF4SGVpZ2h0ID0gX3JlZjgkbWF4SGVpZ2h0ID09PSB2b2lkIDAgPyBJbmZpbml0eSA6IF9yZWY4JG1heEhlaWdodCxcbiAgICAgIF9yZWY4JG1pbldpZHRoID0gX3JlZjgubWluV2lkdGgsXG4gICAgICBtaW5XaWR0aCA9IF9yZWY4JG1pbldpZHRoID09PSB2b2lkIDAgPyAwIDogX3JlZjgkbWluV2lkdGgsXG4gICAgICBfcmVmOCRtaW5IZWlnaHQgPSBfcmVmOC5taW5IZWlnaHQsXG4gICAgICBtaW5IZWlnaHQgPSBfcmVmOCRtaW5IZWlnaHQgPT09IHZvaWQgMCA/IDAgOiBfcmVmOCRtaW5IZWlnaHQ7XG4gICAgdmFyIGNhbnZhcyA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2NhbnZhcycpO1xuICAgIHZhciBjb250ZXh0ID0gY2FudmFzLmdldENvbnRleHQoJzJkJyk7XG4gICAgdmFyIG1heFNpemVzID0gZ2V0QWRqdXN0ZWRTaXplcyh7XG4gICAgICBhc3BlY3RSYXRpbzogYXNwZWN0UmF0aW8sXG4gICAgICB3aWR0aDogbWF4V2lkdGgsXG4gICAgICBoZWlnaHQ6IG1heEhlaWdodFxuICAgIH0pO1xuICAgIHZhciBtaW5TaXplcyA9IGdldEFkanVzdGVkU2l6ZXMoe1xuICAgICAgYXNwZWN0UmF0aW86IGFzcGVjdFJhdGlvLFxuICAgICAgd2lkdGg6IG1pbldpZHRoLFxuICAgICAgaGVpZ2h0OiBtaW5IZWlnaHRcbiAgICB9LCAnY292ZXInKTtcbiAgICB2YXIgd2lkdGggPSBNYXRoLm1pbihtYXhTaXplcy53aWR0aCwgTWF0aC5tYXgobWluU2l6ZXMud2lkdGgsIG5hdHVyYWxXaWR0aCkpO1xuICAgIHZhciBoZWlnaHQgPSBNYXRoLm1pbihtYXhTaXplcy5oZWlnaHQsIE1hdGgubWF4KG1pblNpemVzLmhlaWdodCwgbmF0dXJhbEhlaWdodCkpO1xuXG4gICAgLy8gTm90ZTogc2hvdWxkIGFsd2F5cyB1c2UgaW1hZ2UncyBuYXR1cmFsIHNpemVzIGZvciBkcmF3aW5nIGFzXG4gICAgLy8gaW1hZ2VEYXRhLm5hdHVyYWxXaWR0aCA9PT0gY2FudmFzRGF0YS5uYXR1cmFsSGVpZ2h0IHdoZW4gcm90YXRlICUgMTgwID09PSA5MFxuICAgIHZhciBkZXN0TWF4U2l6ZXMgPSBnZXRBZGp1c3RlZFNpemVzKHtcbiAgICAgIGFzcGVjdFJhdGlvOiBpbWFnZUFzcGVjdFJhdGlvLFxuICAgICAgd2lkdGg6IG1heFdpZHRoLFxuICAgICAgaGVpZ2h0OiBtYXhIZWlnaHRcbiAgICB9KTtcbiAgICB2YXIgZGVzdE1pblNpemVzID0gZ2V0QWRqdXN0ZWRTaXplcyh7XG4gICAgICBhc3BlY3RSYXRpbzogaW1hZ2VBc3BlY3RSYXRpbyxcbiAgICAgIHdpZHRoOiBtaW5XaWR0aCxcbiAgICAgIGhlaWdodDogbWluSGVpZ2h0XG4gICAgfSwgJ2NvdmVyJyk7XG4gICAgdmFyIGRlc3RXaWR0aCA9IE1hdGgubWluKGRlc3RNYXhTaXplcy53aWR0aCwgTWF0aC5tYXgoZGVzdE1pblNpemVzLndpZHRoLCBpbWFnZU5hdHVyYWxXaWR0aCkpO1xuICAgIHZhciBkZXN0SGVpZ2h0ID0gTWF0aC5taW4oZGVzdE1heFNpemVzLmhlaWdodCwgTWF0aC5tYXgoZGVzdE1pblNpemVzLmhlaWdodCwgaW1hZ2VOYXR1cmFsSGVpZ2h0KSk7XG4gICAgdmFyIHBhcmFtcyA9IFstZGVzdFdpZHRoIC8gMiwgLWRlc3RIZWlnaHQgLyAyLCBkZXN0V2lkdGgsIGRlc3RIZWlnaHRdO1xuICAgIGNhbnZhcy53aWR0aCA9IG5vcm1hbGl6ZURlY2ltYWxOdW1iZXIod2lkdGgpO1xuICAgIGNhbnZhcy5oZWlnaHQgPSBub3JtYWxpemVEZWNpbWFsTnVtYmVyKGhlaWdodCk7XG4gICAgY29udGV4dC5maWxsU3R5bGUgPSBmaWxsQ29sb3I7XG4gICAgY29udGV4dC5maWxsUmVjdCgwLCAwLCB3aWR0aCwgaGVpZ2h0KTtcbiAgICBjb250ZXh0LnNhdmUoKTtcbiAgICBjb250ZXh0LnRyYW5zbGF0ZSh3aWR0aCAvIDIsIGhlaWdodCAvIDIpO1xuICAgIGNvbnRleHQucm90YXRlKHJvdGF0ZSAqIE1hdGguUEkgLyAxODApO1xuICAgIGNvbnRleHQuc2NhbGUoc2NhbGVYLCBzY2FsZVkpO1xuICAgIGNvbnRleHQuaW1hZ2VTbW9vdGhpbmdFbmFibGVkID0gaW1hZ2VTbW9vdGhpbmdFbmFibGVkO1xuICAgIGNvbnRleHQuaW1hZ2VTbW9vdGhpbmdRdWFsaXR5ID0gaW1hZ2VTbW9vdGhpbmdRdWFsaXR5O1xuICAgIGNvbnRleHQuZHJhd0ltYWdlLmFwcGx5KGNvbnRleHQsIFtpbWFnZV0uY29uY2F0KF90b0NvbnN1bWFibGVBcnJheShwYXJhbXMubWFwKGZ1bmN0aW9uIChwYXJhbSkge1xuICAgICAgcmV0dXJuIE1hdGguZmxvb3Iobm9ybWFsaXplRGVjaW1hbE51bWJlcihwYXJhbSkpO1xuICAgIH0pKSkpO1xuICAgIGNvbnRleHQucmVzdG9yZSgpO1xuICAgIHJldHVybiBjYW52YXM7XG4gIH1cbiAgdmFyIGZyb21DaGFyQ29kZSA9IFN0cmluZy5mcm9tQ2hhckNvZGU7XG5cbiAgLyoqXG4gICAqIEdldCBzdHJpbmcgZnJvbSBjaGFyIGNvZGUgaW4gZGF0YSB2aWV3LlxuICAgKiBAcGFyYW0ge0RhdGFWaWV3fSBkYXRhVmlldyAtIFRoZSBkYXRhIHZpZXcgZm9yIHJlYWQuXG4gICAqIEBwYXJhbSB7bnVtYmVyfSBzdGFydCAtIFRoZSBzdGFydCBpbmRleC5cbiAgICogQHBhcmFtIHtudW1iZXJ9IGxlbmd0aCAtIFRoZSByZWFkIGxlbmd0aC5cbiAgICogQHJldHVybnMge3N0cmluZ30gVGhlIHJlYWQgcmVzdWx0LlxuICAgKi9cbiAgZnVuY3Rpb24gZ2V0U3RyaW5nRnJvbUNoYXJDb2RlKGRhdGFWaWV3LCBzdGFydCwgbGVuZ3RoKSB7XG4gICAgdmFyIHN0ciA9ICcnO1xuICAgIGxlbmd0aCArPSBzdGFydDtcbiAgICBmb3IgKHZhciBpID0gc3RhcnQ7IGkgPCBsZW5ndGg7IGkgKz0gMSkge1xuICAgICAgc3RyICs9IGZyb21DaGFyQ29kZShkYXRhVmlldy5nZXRVaW50OChpKSk7XG4gICAgfVxuICAgIHJldHVybiBzdHI7XG4gIH1cbiAgdmFyIFJFR0VYUF9EQVRBX1VSTF9IRUFEID0gL15kYXRhOi4qLC87XG5cbiAgLyoqXG4gICAqIFRyYW5zZm9ybSBEYXRhIFVSTCB0byBhcnJheSBidWZmZXIuXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBkYXRhVVJMIC0gVGhlIERhdGEgVVJMIHRvIHRyYW5zZm9ybS5cbiAgICogQHJldHVybnMge0FycmF5QnVmZmVyfSBUaGUgcmVzdWx0IGFycmF5IGJ1ZmZlci5cbiAgICovXG4gIGZ1bmN0aW9uIGRhdGFVUkxUb0FycmF5QnVmZmVyKGRhdGFVUkwpIHtcbiAgICB2YXIgYmFzZTY0ID0gZGF0YVVSTC5yZXBsYWNlKFJFR0VYUF9EQVRBX1VSTF9IRUFELCAnJyk7XG4gICAgdmFyIGJpbmFyeSA9IGF0b2IoYmFzZTY0KTtcbiAgICB2YXIgYXJyYXlCdWZmZXIgPSBuZXcgQXJyYXlCdWZmZXIoYmluYXJ5Lmxlbmd0aCk7XG4gICAgdmFyIHVpbnQ4ID0gbmV3IFVpbnQ4QXJyYXkoYXJyYXlCdWZmZXIpO1xuICAgIGZvckVhY2godWludDgsIGZ1bmN0aW9uICh2YWx1ZSwgaSkge1xuICAgICAgdWludDhbaV0gPSBiaW5hcnkuY2hhckNvZGVBdChpKTtcbiAgICB9KTtcbiAgICByZXR1cm4gYXJyYXlCdWZmZXI7XG4gIH1cblxuICAvKipcbiAgICogVHJhbnNmb3JtIGFycmF5IGJ1ZmZlciB0byBEYXRhIFVSTC5cbiAgICogQHBhcmFtIHtBcnJheUJ1ZmZlcn0gYXJyYXlCdWZmZXIgLSBUaGUgYXJyYXkgYnVmZmVyIHRvIHRyYW5zZm9ybS5cbiAgICogQHBhcmFtIHtzdHJpbmd9IG1pbWVUeXBlIC0gVGhlIG1pbWUgdHlwZSBvZiB0aGUgRGF0YSBVUkwuXG4gICAqIEByZXR1cm5zIHtzdHJpbmd9IFRoZSByZXN1bHQgRGF0YSBVUkwuXG4gICAqL1xuICBmdW5jdGlvbiBhcnJheUJ1ZmZlclRvRGF0YVVSTChhcnJheUJ1ZmZlciwgbWltZVR5cGUpIHtcbiAgICB2YXIgY2h1bmtzID0gW107XG5cbiAgICAvLyBDaHVuayBUeXBlZCBBcnJheSBmb3IgYmV0dGVyIHBlcmZvcm1hbmNlICgjNDM1KVxuICAgIHZhciBjaHVua1NpemUgPSA4MTkyO1xuICAgIHZhciB1aW50OCA9IG5ldyBVaW50OEFycmF5KGFycmF5QnVmZmVyKTtcbiAgICB3aGlsZSAodWludDgubGVuZ3RoID4gMCkge1xuICAgICAgLy8gWFhYOiBCYWJlbCdzIGB0b0NvbnN1bWFibGVBcnJheWAgaGVscGVyIHdpbGwgdGhyb3cgZXJyb3IgaW4gSUUgb3IgU2FmYXJpIDlcbiAgICAgIC8vIGVzbGludC1kaXNhYmxlLW5leHQtbGluZSBwcmVmZXItc3ByZWFkXG4gICAgICBjaHVua3MucHVzaChmcm9tQ2hhckNvZGUuYXBwbHkobnVsbCwgdG9BcnJheSh1aW50OC5zdWJhcnJheSgwLCBjaHVua1NpemUpKSkpO1xuICAgICAgdWludDggPSB1aW50OC5zdWJhcnJheShjaHVua1NpemUpO1xuICAgIH1cbiAgICByZXR1cm4gXCJkYXRhOlwiLmNvbmNhdChtaW1lVHlwZSwgXCI7YmFzZTY0LFwiKS5jb25jYXQoYnRvYShjaHVua3Muam9pbignJykpKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgb3JpZW50YXRpb24gdmFsdWUgZnJvbSBnaXZlbiBhcnJheSBidWZmZXIuXG4gICAqIEBwYXJhbSB7QXJyYXlCdWZmZXJ9IGFycmF5QnVmZmVyIC0gVGhlIGFycmF5IGJ1ZmZlciB0byByZWFkLlxuICAgKiBAcmV0dXJucyB7bnVtYmVyfSBUaGUgcmVhZCBvcmllbnRhdGlvbiB2YWx1ZS5cbiAgICovXG4gIGZ1bmN0aW9uIHJlc2V0QW5kR2V0T3JpZW50YXRpb24oYXJyYXlCdWZmZXIpIHtcbiAgICB2YXIgZGF0YVZpZXcgPSBuZXcgRGF0YVZpZXcoYXJyYXlCdWZmZXIpO1xuICAgIHZhciBvcmllbnRhdGlvbjtcblxuICAgIC8vIElnbm9yZXMgcmFuZ2UgZXJyb3Igd2hlbiB0aGUgaW1hZ2UgZG9lcyBub3QgaGF2ZSBjb3JyZWN0IEV4aWYgaW5mb3JtYXRpb25cbiAgICB0cnkge1xuICAgICAgdmFyIGxpdHRsZUVuZGlhbjtcbiAgICAgIHZhciBhcHAxU3RhcnQ7XG4gICAgICB2YXIgaWZkU3RhcnQ7XG5cbiAgICAgIC8vIE9ubHkgaGFuZGxlIEpQRUcgaW1hZ2UgKHN0YXJ0IGJ5IDB4RkZEOClcbiAgICAgIGlmIChkYXRhVmlldy5nZXRVaW50OCgwKSA9PT0gMHhGRiAmJiBkYXRhVmlldy5nZXRVaW50OCgxKSA9PT0gMHhEOCkge1xuICAgICAgICB2YXIgbGVuZ3RoID0gZGF0YVZpZXcuYnl0ZUxlbmd0aDtcbiAgICAgICAgdmFyIG9mZnNldCA9IDI7XG4gICAgICAgIHdoaWxlIChvZmZzZXQgKyAxIDwgbGVuZ3RoKSB7XG4gICAgICAgICAgaWYgKGRhdGFWaWV3LmdldFVpbnQ4KG9mZnNldCkgPT09IDB4RkYgJiYgZGF0YVZpZXcuZ2V0VWludDgob2Zmc2V0ICsgMSkgPT09IDB4RTEpIHtcbiAgICAgICAgICAgIGFwcDFTdGFydCA9IG9mZnNldDtcbiAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgIH1cbiAgICAgICAgICBvZmZzZXQgKz0gMTtcbiAgICAgICAgfVxuICAgICAgfVxuICAgICAgaWYgKGFwcDFTdGFydCkge1xuICAgICAgICB2YXIgZXhpZklEQ29kZSA9IGFwcDFTdGFydCArIDQ7XG4gICAgICAgIHZhciB0aWZmT2Zmc2V0ID0gYXBwMVN0YXJ0ICsgMTA7XG4gICAgICAgIGlmIChnZXRTdHJpbmdGcm9tQ2hhckNvZGUoZGF0YVZpZXcsIGV4aWZJRENvZGUsIDQpID09PSAnRXhpZicpIHtcbiAgICAgICAgICB2YXIgZW5kaWFubmVzcyA9IGRhdGFWaWV3LmdldFVpbnQxNih0aWZmT2Zmc2V0KTtcbiAgICAgICAgICBsaXR0bGVFbmRpYW4gPSBlbmRpYW5uZXNzID09PSAweDQ5NDk7XG4gICAgICAgICAgaWYgKGxpdHRsZUVuZGlhbiB8fCBlbmRpYW5uZXNzID09PSAweDRENEQgLyogYmlnRW5kaWFuICovKSB7XG4gICAgICAgICAgICBpZiAoZGF0YVZpZXcuZ2V0VWludDE2KHRpZmZPZmZzZXQgKyAyLCBsaXR0bGVFbmRpYW4pID09PSAweDAwMkEpIHtcbiAgICAgICAgICAgICAgdmFyIGZpcnN0SUZET2Zmc2V0ID0gZGF0YVZpZXcuZ2V0VWludDMyKHRpZmZPZmZzZXQgKyA0LCBsaXR0bGVFbmRpYW4pO1xuICAgICAgICAgICAgICBpZiAoZmlyc3RJRkRPZmZzZXQgPj0gMHgwMDAwMDAwOCkge1xuICAgICAgICAgICAgICAgIGlmZFN0YXJ0ID0gdGlmZk9mZnNldCArIGZpcnN0SUZET2Zmc2V0O1xuICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICB9XG4gICAgICBpZiAoaWZkU3RhcnQpIHtcbiAgICAgICAgdmFyIF9sZW5ndGggPSBkYXRhVmlldy5nZXRVaW50MTYoaWZkU3RhcnQsIGxpdHRsZUVuZGlhbik7XG4gICAgICAgIHZhciBfb2Zmc2V0O1xuICAgICAgICB2YXIgaTtcbiAgICAgICAgZm9yIChpID0gMDsgaSA8IF9sZW5ndGg7IGkgKz0gMSkge1xuICAgICAgICAgIF9vZmZzZXQgPSBpZmRTdGFydCArIGkgKiAxMiArIDI7XG4gICAgICAgICAgaWYgKGRhdGFWaWV3LmdldFVpbnQxNihfb2Zmc2V0LCBsaXR0bGVFbmRpYW4pID09PSAweDAxMTIgLyogT3JpZW50YXRpb24gKi8pIHtcbiAgICAgICAgICAgIC8vIDggaXMgdGhlIG9mZnNldCBvZiB0aGUgY3VycmVudCB0YWcncyB2YWx1ZVxuICAgICAgICAgICAgX29mZnNldCArPSA4O1xuXG4gICAgICAgICAgICAvLyBHZXQgdGhlIG9yaWdpbmFsIG9yaWVudGF0aW9uIHZhbHVlXG4gICAgICAgICAgICBvcmllbnRhdGlvbiA9IGRhdGFWaWV3LmdldFVpbnQxNihfb2Zmc2V0LCBsaXR0bGVFbmRpYW4pO1xuXG4gICAgICAgICAgICAvLyBPdmVycmlkZSB0aGUgb3JpZW50YXRpb24gd2l0aCBpdHMgZGVmYXVsdCB2YWx1ZVxuICAgICAgICAgICAgZGF0YVZpZXcuc2V0VWludDE2KF9vZmZzZXQsIDEsIGxpdHRsZUVuZGlhbik7XG4gICAgICAgICAgICBicmVhaztcbiAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICB9IGNhdGNoIChlcnJvcikge1xuICAgICAgb3JpZW50YXRpb24gPSAxO1xuICAgIH1cbiAgICByZXR1cm4gb3JpZW50YXRpb247XG4gIH1cblxuICAvKipcbiAgICogUGFyc2UgRXhpZiBPcmllbnRhdGlvbiB2YWx1ZS5cbiAgICogQHBhcmFtIHtudW1iZXJ9IG9yaWVudGF0aW9uIC0gVGhlIG9yaWVudGF0aW9uIHRvIHBhcnNlLlxuICAgKiBAcmV0dXJucyB7T2JqZWN0fSBUaGUgcGFyc2VkIHJlc3VsdC5cbiAgICovXG4gIGZ1bmN0aW9uIHBhcnNlT3JpZW50YXRpb24ob3JpZW50YXRpb24pIHtcbiAgICB2YXIgcm90YXRlID0gMDtcbiAgICB2YXIgc2NhbGVYID0gMTtcbiAgICB2YXIgc2NhbGVZID0gMTtcbiAgICBzd2l0Y2ggKG9yaWVudGF0aW9uKSB7XG4gICAgICAvLyBGbGlwIGhvcml6b250YWxcbiAgICAgIGNhc2UgMjpcbiAgICAgICAgc2NhbGVYID0gLTE7XG4gICAgICAgIGJyZWFrO1xuXG4gICAgICAvLyBSb3RhdGUgbGVmdCAxODBcdTAwQjBcbiAgICAgIGNhc2UgMzpcbiAgICAgICAgcm90YXRlID0gLTE4MDtcbiAgICAgICAgYnJlYWs7XG5cbiAgICAgIC8vIEZsaXAgdmVydGljYWxcbiAgICAgIGNhc2UgNDpcbiAgICAgICAgc2NhbGVZID0gLTE7XG4gICAgICAgIGJyZWFrO1xuXG4gICAgICAvLyBGbGlwIHZlcnRpY2FsIGFuZCByb3RhdGUgcmlnaHQgOTBcdTAwQjBcbiAgICAgIGNhc2UgNTpcbiAgICAgICAgcm90YXRlID0gOTA7XG4gICAgICAgIHNjYWxlWSA9IC0xO1xuICAgICAgICBicmVhaztcblxuICAgICAgLy8gUm90YXRlIHJpZ2h0IDkwXHUwMEIwXG4gICAgICBjYXNlIDY6XG4gICAgICAgIHJvdGF0ZSA9IDkwO1xuICAgICAgICBicmVhaztcblxuICAgICAgLy8gRmxpcCBob3Jpem9udGFsIGFuZCByb3RhdGUgcmlnaHQgOTBcdTAwQjBcbiAgICAgIGNhc2UgNzpcbiAgICAgICAgcm90YXRlID0gOTA7XG4gICAgICAgIHNjYWxlWCA9IC0xO1xuICAgICAgICBicmVhaztcblxuICAgICAgLy8gUm90YXRlIGxlZnQgOTBcdTAwQjBcbiAgICAgIGNhc2UgODpcbiAgICAgICAgcm90YXRlID0gLTkwO1xuICAgICAgICBicmVhaztcbiAgICB9XG4gICAgcmV0dXJuIHtcbiAgICAgIHJvdGF0ZTogcm90YXRlLFxuICAgICAgc2NhbGVYOiBzY2FsZVgsXG4gICAgICBzY2FsZVk6IHNjYWxlWVxuICAgIH07XG4gIH1cblxuICB2YXIgcmVuZGVyID0ge1xuICAgIHJlbmRlcjogZnVuY3Rpb24gcmVuZGVyKCkge1xuICAgICAgdGhpcy5pbml0Q29udGFpbmVyKCk7XG4gICAgICB0aGlzLmluaXRDYW52YXMoKTtcbiAgICAgIHRoaXMuaW5pdENyb3BCb3goKTtcbiAgICAgIHRoaXMucmVuZGVyQ2FudmFzKCk7XG4gICAgICBpZiAodGhpcy5jcm9wcGVkKSB7XG4gICAgICAgIHRoaXMucmVuZGVyQ3JvcEJveCgpO1xuICAgICAgfVxuICAgIH0sXG4gICAgaW5pdENvbnRhaW5lcjogZnVuY3Rpb24gaW5pdENvbnRhaW5lcigpIHtcbiAgICAgIHZhciBlbGVtZW50ID0gdGhpcy5lbGVtZW50LFxuICAgICAgICBvcHRpb25zID0gdGhpcy5vcHRpb25zLFxuICAgICAgICBjb250YWluZXIgPSB0aGlzLmNvbnRhaW5lcixcbiAgICAgICAgY3JvcHBlciA9IHRoaXMuY3JvcHBlcjtcbiAgICAgIHZhciBtaW5XaWR0aCA9IE51bWJlcihvcHRpb25zLm1pbkNvbnRhaW5lcldpZHRoKTtcbiAgICAgIHZhciBtaW5IZWlnaHQgPSBOdW1iZXIob3B0aW9ucy5taW5Db250YWluZXJIZWlnaHQpO1xuICAgICAgYWRkQ2xhc3MoY3JvcHBlciwgQ0xBU1NfSElEREVOKTtcbiAgICAgIHJlbW92ZUNsYXNzKGVsZW1lbnQsIENMQVNTX0hJRERFTik7XG4gICAgICB2YXIgY29udGFpbmVyRGF0YSA9IHtcbiAgICAgICAgd2lkdGg6IE1hdGgubWF4KGNvbnRhaW5lci5vZmZzZXRXaWR0aCwgbWluV2lkdGggPj0gMCA/IG1pbldpZHRoIDogTUlOX0NPTlRBSU5FUl9XSURUSCksXG4gICAgICAgIGhlaWdodDogTWF0aC5tYXgoY29udGFpbmVyLm9mZnNldEhlaWdodCwgbWluSGVpZ2h0ID49IDAgPyBtaW5IZWlnaHQgOiBNSU5fQ09OVEFJTkVSX0hFSUdIVClcbiAgICAgIH07XG4gICAgICB0aGlzLmNvbnRhaW5lckRhdGEgPSBjb250YWluZXJEYXRhO1xuICAgICAgc2V0U3R5bGUoY3JvcHBlciwge1xuICAgICAgICB3aWR0aDogY29udGFpbmVyRGF0YS53aWR0aCxcbiAgICAgICAgaGVpZ2h0OiBjb250YWluZXJEYXRhLmhlaWdodFxuICAgICAgfSk7XG4gICAgICBhZGRDbGFzcyhlbGVtZW50LCBDTEFTU19ISURERU4pO1xuICAgICAgcmVtb3ZlQ2xhc3MoY3JvcHBlciwgQ0xBU1NfSElEREVOKTtcbiAgICB9LFxuICAgIC8vIENhbnZhcyAoaW1hZ2Ugd3JhcHBlcilcbiAgICBpbml0Q2FudmFzOiBmdW5jdGlvbiBpbml0Q2FudmFzKCkge1xuICAgICAgdmFyIGNvbnRhaW5lckRhdGEgPSB0aGlzLmNvbnRhaW5lckRhdGEsXG4gICAgICAgIGltYWdlRGF0YSA9IHRoaXMuaW1hZ2VEYXRhO1xuICAgICAgdmFyIHZpZXdNb2RlID0gdGhpcy5vcHRpb25zLnZpZXdNb2RlO1xuICAgICAgdmFyIHJvdGF0ZWQgPSBNYXRoLmFicyhpbWFnZURhdGEucm90YXRlKSAlIDE4MCA9PT0gOTA7XG4gICAgICB2YXIgbmF0dXJhbFdpZHRoID0gcm90YXRlZCA/IGltYWdlRGF0YS5uYXR1cmFsSGVpZ2h0IDogaW1hZ2VEYXRhLm5hdHVyYWxXaWR0aDtcbiAgICAgIHZhciBuYXR1cmFsSGVpZ2h0ID0gcm90YXRlZCA/IGltYWdlRGF0YS5uYXR1cmFsV2lkdGggOiBpbWFnZURhdGEubmF0dXJhbEhlaWdodDtcbiAgICAgIHZhciBhc3BlY3RSYXRpbyA9IG5hdHVyYWxXaWR0aCAvIG5hdHVyYWxIZWlnaHQ7XG4gICAgICB2YXIgY2FudmFzV2lkdGggPSBjb250YWluZXJEYXRhLndpZHRoO1xuICAgICAgdmFyIGNhbnZhc0hlaWdodCA9IGNvbnRhaW5lckRhdGEuaGVpZ2h0O1xuICAgICAgaWYgKGNvbnRhaW5lckRhdGEuaGVpZ2h0ICogYXNwZWN0UmF0aW8gPiBjb250YWluZXJEYXRhLndpZHRoKSB7XG4gICAgICAgIGlmICh2aWV3TW9kZSA9PT0gMykge1xuICAgICAgICAgIGNhbnZhc1dpZHRoID0gY29udGFpbmVyRGF0YS5oZWlnaHQgKiBhc3BlY3RSYXRpbztcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICBjYW52YXNIZWlnaHQgPSBjb250YWluZXJEYXRhLndpZHRoIC8gYXNwZWN0UmF0aW87XG4gICAgICAgIH1cbiAgICAgIH0gZWxzZSBpZiAodmlld01vZGUgPT09IDMpIHtcbiAgICAgICAgY2FudmFzSGVpZ2h0ID0gY29udGFpbmVyRGF0YS53aWR0aCAvIGFzcGVjdFJhdGlvO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgY2FudmFzV2lkdGggPSBjb250YWluZXJEYXRhLmhlaWdodCAqIGFzcGVjdFJhdGlvO1xuICAgICAgfVxuICAgICAgdmFyIGNhbnZhc0RhdGEgPSB7XG4gICAgICAgIGFzcGVjdFJhdGlvOiBhc3BlY3RSYXRpbyxcbiAgICAgICAgbmF0dXJhbFdpZHRoOiBuYXR1cmFsV2lkdGgsXG4gICAgICAgIG5hdHVyYWxIZWlnaHQ6IG5hdHVyYWxIZWlnaHQsXG4gICAgICAgIHdpZHRoOiBjYW52YXNXaWR0aCxcbiAgICAgICAgaGVpZ2h0OiBjYW52YXNIZWlnaHRcbiAgICAgIH07XG4gICAgICB0aGlzLmNhbnZhc0RhdGEgPSBjYW52YXNEYXRhO1xuICAgICAgdGhpcy5saW1pdGVkID0gdmlld01vZGUgPT09IDEgfHwgdmlld01vZGUgPT09IDI7XG4gICAgICB0aGlzLmxpbWl0Q2FudmFzKHRydWUsIHRydWUpO1xuICAgICAgY2FudmFzRGF0YS53aWR0aCA9IE1hdGgubWluKE1hdGgubWF4KGNhbnZhc0RhdGEud2lkdGgsIGNhbnZhc0RhdGEubWluV2lkdGgpLCBjYW52YXNEYXRhLm1heFdpZHRoKTtcbiAgICAgIGNhbnZhc0RhdGEuaGVpZ2h0ID0gTWF0aC5taW4oTWF0aC5tYXgoY2FudmFzRGF0YS5oZWlnaHQsIGNhbnZhc0RhdGEubWluSGVpZ2h0KSwgY2FudmFzRGF0YS5tYXhIZWlnaHQpO1xuICAgICAgY2FudmFzRGF0YS5sZWZ0ID0gKGNvbnRhaW5lckRhdGEud2lkdGggLSBjYW52YXNEYXRhLndpZHRoKSAvIDI7XG4gICAgICBjYW52YXNEYXRhLnRvcCA9IChjb250YWluZXJEYXRhLmhlaWdodCAtIGNhbnZhc0RhdGEuaGVpZ2h0KSAvIDI7XG4gICAgICBjYW52YXNEYXRhLm9sZExlZnQgPSBjYW52YXNEYXRhLmxlZnQ7XG4gICAgICBjYW52YXNEYXRhLm9sZFRvcCA9IGNhbnZhc0RhdGEudG9wO1xuICAgICAgdGhpcy5pbml0aWFsQ2FudmFzRGF0YSA9IGFzc2lnbih7fSwgY2FudmFzRGF0YSk7XG4gICAgfSxcbiAgICBsaW1pdENhbnZhczogZnVuY3Rpb24gbGltaXRDYW52YXMoc2l6ZUxpbWl0ZWQsIHBvc2l0aW9uTGltaXRlZCkge1xuICAgICAgdmFyIG9wdGlvbnMgPSB0aGlzLm9wdGlvbnMsXG4gICAgICAgIGNvbnRhaW5lckRhdGEgPSB0aGlzLmNvbnRhaW5lckRhdGEsXG4gICAgICAgIGNhbnZhc0RhdGEgPSB0aGlzLmNhbnZhc0RhdGEsXG4gICAgICAgIGNyb3BCb3hEYXRhID0gdGhpcy5jcm9wQm94RGF0YTtcbiAgICAgIHZhciB2aWV3TW9kZSA9IG9wdGlvbnMudmlld01vZGU7XG4gICAgICB2YXIgYXNwZWN0UmF0aW8gPSBjYW52YXNEYXRhLmFzcGVjdFJhdGlvO1xuICAgICAgdmFyIGNyb3BwZWQgPSB0aGlzLmNyb3BwZWQgJiYgY3JvcEJveERhdGE7XG4gICAgICBpZiAoc2l6ZUxpbWl0ZWQpIHtcbiAgICAgICAgdmFyIG1pbkNhbnZhc1dpZHRoID0gTnVtYmVyKG9wdGlvbnMubWluQ2FudmFzV2lkdGgpIHx8IDA7XG4gICAgICAgIHZhciBtaW5DYW52YXNIZWlnaHQgPSBOdW1iZXIob3B0aW9ucy5taW5DYW52YXNIZWlnaHQpIHx8IDA7XG4gICAgICAgIGlmICh2aWV3TW9kZSA+IDEpIHtcbiAgICAgICAgICBtaW5DYW52YXNXaWR0aCA9IE1hdGgubWF4KG1pbkNhbnZhc1dpZHRoLCBjb250YWluZXJEYXRhLndpZHRoKTtcbiAgICAgICAgICBtaW5DYW52YXNIZWlnaHQgPSBNYXRoLm1heChtaW5DYW52YXNIZWlnaHQsIGNvbnRhaW5lckRhdGEuaGVpZ2h0KTtcbiAgICAgICAgICBpZiAodmlld01vZGUgPT09IDMpIHtcbiAgICAgICAgICAgIGlmIChtaW5DYW52YXNIZWlnaHQgKiBhc3BlY3RSYXRpbyA+IG1pbkNhbnZhc1dpZHRoKSB7XG4gICAgICAgICAgICAgIG1pbkNhbnZhc1dpZHRoID0gbWluQ2FudmFzSGVpZ2h0ICogYXNwZWN0UmF0aW87XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICBtaW5DYW52YXNIZWlnaHQgPSBtaW5DYW52YXNXaWR0aCAvIGFzcGVjdFJhdGlvO1xuICAgICAgICAgICAgfVxuICAgICAgICAgIH1cbiAgICAgICAgfSBlbHNlIGlmICh2aWV3TW9kZSA+IDApIHtcbiAgICAgICAgICBpZiAobWluQ2FudmFzV2lkdGgpIHtcbiAgICAgICAgICAgIG1pbkNhbnZhc1dpZHRoID0gTWF0aC5tYXgobWluQ2FudmFzV2lkdGgsIGNyb3BwZWQgPyBjcm9wQm94RGF0YS53aWR0aCA6IDApO1xuICAgICAgICAgIH0gZWxzZSBpZiAobWluQ2FudmFzSGVpZ2h0KSB7XG4gICAgICAgICAgICBtaW5DYW52YXNIZWlnaHQgPSBNYXRoLm1heChtaW5DYW52YXNIZWlnaHQsIGNyb3BwZWQgPyBjcm9wQm94RGF0YS5oZWlnaHQgOiAwKTtcbiAgICAgICAgICB9IGVsc2UgaWYgKGNyb3BwZWQpIHtcbiAgICAgICAgICAgIG1pbkNhbnZhc1dpZHRoID0gY3JvcEJveERhdGEud2lkdGg7XG4gICAgICAgICAgICBtaW5DYW52YXNIZWlnaHQgPSBjcm9wQm94RGF0YS5oZWlnaHQ7XG4gICAgICAgICAgICBpZiAobWluQ2FudmFzSGVpZ2h0ICogYXNwZWN0UmF0aW8gPiBtaW5DYW52YXNXaWR0aCkge1xuICAgICAgICAgICAgICBtaW5DYW52YXNXaWR0aCA9IG1pbkNhbnZhc0hlaWdodCAqIGFzcGVjdFJhdGlvO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgbWluQ2FudmFzSGVpZ2h0ID0gbWluQ2FudmFzV2lkdGggLyBhc3BlY3RSYXRpbztcbiAgICAgICAgICAgIH1cbiAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgICAgdmFyIF9nZXRBZGp1c3RlZFNpemVzID0gZ2V0QWRqdXN0ZWRTaXplcyh7XG4gICAgICAgICAgYXNwZWN0UmF0aW86IGFzcGVjdFJhdGlvLFxuICAgICAgICAgIHdpZHRoOiBtaW5DYW52YXNXaWR0aCxcbiAgICAgICAgICBoZWlnaHQ6IG1pbkNhbnZhc0hlaWdodFxuICAgICAgICB9KTtcbiAgICAgICAgbWluQ2FudmFzV2lkdGggPSBfZ2V0QWRqdXN0ZWRTaXplcy53aWR0aDtcbiAgICAgICAgbWluQ2FudmFzSGVpZ2h0ID0gX2dldEFkanVzdGVkU2l6ZXMuaGVpZ2h0O1xuICAgICAgICBjYW52YXNEYXRhLm1pbldpZHRoID0gbWluQ2FudmFzV2lkdGg7XG4gICAgICAgIGNhbnZhc0RhdGEubWluSGVpZ2h0ID0gbWluQ2FudmFzSGVpZ2h0O1xuICAgICAgICBjYW52YXNEYXRhLm1heFdpZHRoID0gSW5maW5pdHk7XG4gICAgICAgIGNhbnZhc0RhdGEubWF4SGVpZ2h0ID0gSW5maW5pdHk7XG4gICAgICB9XG4gICAgICBpZiAocG9zaXRpb25MaW1pdGVkKSB7XG4gICAgICAgIGlmICh2aWV3TW9kZSA+IChjcm9wcGVkID8gMCA6IDEpKSB7XG4gICAgICAgICAgdmFyIG5ld0NhbnZhc0xlZnQgPSBjb250YWluZXJEYXRhLndpZHRoIC0gY2FudmFzRGF0YS53aWR0aDtcbiAgICAgICAgICB2YXIgbmV3Q2FudmFzVG9wID0gY29udGFpbmVyRGF0YS5oZWlnaHQgLSBjYW52YXNEYXRhLmhlaWdodDtcbiAgICAgICAgICBjYW52YXNEYXRhLm1pbkxlZnQgPSBNYXRoLm1pbigwLCBuZXdDYW52YXNMZWZ0KTtcbiAgICAgICAgICBjYW52YXNEYXRhLm1pblRvcCA9IE1hdGgubWluKDAsIG5ld0NhbnZhc1RvcCk7XG4gICAgICAgICAgY2FudmFzRGF0YS5tYXhMZWZ0ID0gTWF0aC5tYXgoMCwgbmV3Q2FudmFzTGVmdCk7XG4gICAgICAgICAgY2FudmFzRGF0YS5tYXhUb3AgPSBNYXRoLm1heCgwLCBuZXdDYW52YXNUb3ApO1xuICAgICAgICAgIGlmIChjcm9wcGVkICYmIHRoaXMubGltaXRlZCkge1xuICAgICAgICAgICAgY2FudmFzRGF0YS5taW5MZWZ0ID0gTWF0aC5taW4oY3JvcEJveERhdGEubGVmdCwgY3JvcEJveERhdGEubGVmdCArIChjcm9wQm94RGF0YS53aWR0aCAtIGNhbnZhc0RhdGEud2lkdGgpKTtcbiAgICAgICAgICAgIGNhbnZhc0RhdGEubWluVG9wID0gTWF0aC5taW4oY3JvcEJveERhdGEudG9wLCBjcm9wQm94RGF0YS50b3AgKyAoY3JvcEJveERhdGEuaGVpZ2h0IC0gY2FudmFzRGF0YS5oZWlnaHQpKTtcbiAgICAgICAgICAgIGNhbnZhc0RhdGEubWF4TGVmdCA9IGNyb3BCb3hEYXRhLmxlZnQ7XG4gICAgICAgICAgICBjYW52YXNEYXRhLm1heFRvcCA9IGNyb3BCb3hEYXRhLnRvcDtcbiAgICAgICAgICAgIGlmICh2aWV3TW9kZSA9PT0gMikge1xuICAgICAgICAgICAgICBpZiAoY2FudmFzRGF0YS53aWR0aCA+PSBjb250YWluZXJEYXRhLndpZHRoKSB7XG4gICAgICAgICAgICAgICAgY2FudmFzRGF0YS5taW5MZWZ0ID0gTWF0aC5taW4oMCwgbmV3Q2FudmFzTGVmdCk7XG4gICAgICAgICAgICAgICAgY2FudmFzRGF0YS5tYXhMZWZ0ID0gTWF0aC5tYXgoMCwgbmV3Q2FudmFzTGVmdCk7XG4gICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgaWYgKGNhbnZhc0RhdGEuaGVpZ2h0ID49IGNvbnRhaW5lckRhdGEuaGVpZ2h0KSB7XG4gICAgICAgICAgICAgICAgY2FudmFzRGF0YS5taW5Ub3AgPSBNYXRoLm1pbigwLCBuZXdDYW52YXNUb3ApO1xuICAgICAgICAgICAgICAgIGNhbnZhc0RhdGEubWF4VG9wID0gTWF0aC5tYXgoMCwgbmV3Q2FudmFzVG9wKTtcbiAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICAgIH1cbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICBjYW52YXNEYXRhLm1pbkxlZnQgPSAtY2FudmFzRGF0YS53aWR0aDtcbiAgICAgICAgICBjYW52YXNEYXRhLm1pblRvcCA9IC1jYW52YXNEYXRhLmhlaWdodDtcbiAgICAgICAgICBjYW52YXNEYXRhLm1heExlZnQgPSBjb250YWluZXJEYXRhLndpZHRoO1xuICAgICAgICAgIGNhbnZhc0RhdGEubWF4VG9wID0gY29udGFpbmVyRGF0YS5oZWlnaHQ7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICB9LFxuICAgIHJlbmRlckNhbnZhczogZnVuY3Rpb24gcmVuZGVyQ2FudmFzKGNoYW5nZWQsIHRyYW5zZm9ybWVkKSB7XG4gICAgICB2YXIgY2FudmFzRGF0YSA9IHRoaXMuY2FudmFzRGF0YSxcbiAgICAgICAgaW1hZ2VEYXRhID0gdGhpcy5pbWFnZURhdGE7XG4gICAgICBpZiAodHJhbnNmb3JtZWQpIHtcbiAgICAgICAgdmFyIF9nZXRSb3RhdGVkU2l6ZXMgPSBnZXRSb3RhdGVkU2l6ZXMoe1xuICAgICAgICAgICAgd2lkdGg6IGltYWdlRGF0YS5uYXR1cmFsV2lkdGggKiBNYXRoLmFicyhpbWFnZURhdGEuc2NhbGVYIHx8IDEpLFxuICAgICAgICAgICAgaGVpZ2h0OiBpbWFnZURhdGEubmF0dXJhbEhlaWdodCAqIE1hdGguYWJzKGltYWdlRGF0YS5zY2FsZVkgfHwgMSksXG4gICAgICAgICAgICBkZWdyZWU6IGltYWdlRGF0YS5yb3RhdGUgfHwgMFxuICAgICAgICAgIH0pLFxuICAgICAgICAgIG5hdHVyYWxXaWR0aCA9IF9nZXRSb3RhdGVkU2l6ZXMud2lkdGgsXG4gICAgICAgICAgbmF0dXJhbEhlaWdodCA9IF9nZXRSb3RhdGVkU2l6ZXMuaGVpZ2h0O1xuICAgICAgICB2YXIgd2lkdGggPSBjYW52YXNEYXRhLndpZHRoICogKG5hdHVyYWxXaWR0aCAvIGNhbnZhc0RhdGEubmF0dXJhbFdpZHRoKTtcbiAgICAgICAgdmFyIGhlaWdodCA9IGNhbnZhc0RhdGEuaGVpZ2h0ICogKG5hdHVyYWxIZWlnaHQgLyBjYW52YXNEYXRhLm5hdHVyYWxIZWlnaHQpO1xuICAgICAgICBjYW52YXNEYXRhLmxlZnQgLT0gKHdpZHRoIC0gY2FudmFzRGF0YS53aWR0aCkgLyAyO1xuICAgICAgICBjYW52YXNEYXRhLnRvcCAtPSAoaGVpZ2h0IC0gY2FudmFzRGF0YS5oZWlnaHQpIC8gMjtcbiAgICAgICAgY2FudmFzRGF0YS53aWR0aCA9IHdpZHRoO1xuICAgICAgICBjYW52YXNEYXRhLmhlaWdodCA9IGhlaWdodDtcbiAgICAgICAgY2FudmFzRGF0YS5hc3BlY3RSYXRpbyA9IG5hdHVyYWxXaWR0aCAvIG5hdHVyYWxIZWlnaHQ7XG4gICAgICAgIGNhbnZhc0RhdGEubmF0dXJhbFdpZHRoID0gbmF0dXJhbFdpZHRoO1xuICAgICAgICBjYW52YXNEYXRhLm5hdHVyYWxIZWlnaHQgPSBuYXR1cmFsSGVpZ2h0O1xuICAgICAgICB0aGlzLmxpbWl0Q2FudmFzKHRydWUsIGZhbHNlKTtcbiAgICAgIH1cbiAgICAgIGlmIChjYW52YXNEYXRhLndpZHRoID4gY2FudmFzRGF0YS5tYXhXaWR0aCB8fCBjYW52YXNEYXRhLndpZHRoIDwgY2FudmFzRGF0YS5taW5XaWR0aCkge1xuICAgICAgICBjYW52YXNEYXRhLmxlZnQgPSBjYW52YXNEYXRhLm9sZExlZnQ7XG4gICAgICB9XG4gICAgICBpZiAoY2FudmFzRGF0YS5oZWlnaHQgPiBjYW52YXNEYXRhLm1heEhlaWdodCB8fCBjYW52YXNEYXRhLmhlaWdodCA8IGNhbnZhc0RhdGEubWluSGVpZ2h0KSB7XG4gICAgICAgIGNhbnZhc0RhdGEudG9wID0gY2FudmFzRGF0YS5vbGRUb3A7XG4gICAgICB9XG4gICAgICBjYW52YXNEYXRhLndpZHRoID0gTWF0aC5taW4oTWF0aC5tYXgoY2FudmFzRGF0YS53aWR0aCwgY2FudmFzRGF0YS5taW5XaWR0aCksIGNhbnZhc0RhdGEubWF4V2lkdGgpO1xuICAgICAgY2FudmFzRGF0YS5oZWlnaHQgPSBNYXRoLm1pbihNYXRoLm1heChjYW52YXNEYXRhLmhlaWdodCwgY2FudmFzRGF0YS5taW5IZWlnaHQpLCBjYW52YXNEYXRhLm1heEhlaWdodCk7XG4gICAgICB0aGlzLmxpbWl0Q2FudmFzKGZhbHNlLCB0cnVlKTtcbiAgICAgIGNhbnZhc0RhdGEubGVmdCA9IE1hdGgubWluKE1hdGgubWF4KGNhbnZhc0RhdGEubGVmdCwgY2FudmFzRGF0YS5taW5MZWZ0KSwgY2FudmFzRGF0YS5tYXhMZWZ0KTtcbiAgICAgIGNhbnZhc0RhdGEudG9wID0gTWF0aC5taW4oTWF0aC5tYXgoY2FudmFzRGF0YS50b3AsIGNhbnZhc0RhdGEubWluVG9wKSwgY2FudmFzRGF0YS5tYXhUb3ApO1xuICAgICAgY2FudmFzRGF0YS5vbGRMZWZ0ID0gY2FudmFzRGF0YS5sZWZ0O1xuICAgICAgY2FudmFzRGF0YS5vbGRUb3AgPSBjYW52YXNEYXRhLnRvcDtcbiAgICAgIHNldFN0eWxlKHRoaXMuY2FudmFzLCBhc3NpZ24oe1xuICAgICAgICB3aWR0aDogY2FudmFzRGF0YS53aWR0aCxcbiAgICAgICAgaGVpZ2h0OiBjYW52YXNEYXRhLmhlaWdodFxuICAgICAgfSwgZ2V0VHJhbnNmb3Jtcyh7XG4gICAgICAgIHRyYW5zbGF0ZVg6IGNhbnZhc0RhdGEubGVmdCxcbiAgICAgICAgdHJhbnNsYXRlWTogY2FudmFzRGF0YS50b3BcbiAgICAgIH0pKSk7XG4gICAgICB0aGlzLnJlbmRlckltYWdlKGNoYW5nZWQpO1xuICAgICAgaWYgKHRoaXMuY3JvcHBlZCAmJiB0aGlzLmxpbWl0ZWQpIHtcbiAgICAgICAgdGhpcy5saW1pdENyb3BCb3godHJ1ZSwgdHJ1ZSk7XG4gICAgICB9XG4gICAgfSxcbiAgICByZW5kZXJJbWFnZTogZnVuY3Rpb24gcmVuZGVySW1hZ2UoY2hhbmdlZCkge1xuICAgICAgdmFyIGNhbnZhc0RhdGEgPSB0aGlzLmNhbnZhc0RhdGEsXG4gICAgICAgIGltYWdlRGF0YSA9IHRoaXMuaW1hZ2VEYXRhO1xuICAgICAgdmFyIHdpZHRoID0gaW1hZ2VEYXRhLm5hdHVyYWxXaWR0aCAqIChjYW52YXNEYXRhLndpZHRoIC8gY2FudmFzRGF0YS5uYXR1cmFsV2lkdGgpO1xuICAgICAgdmFyIGhlaWdodCA9IGltYWdlRGF0YS5uYXR1cmFsSGVpZ2h0ICogKGNhbnZhc0RhdGEuaGVpZ2h0IC8gY2FudmFzRGF0YS5uYXR1cmFsSGVpZ2h0KTtcbiAgICAgIGFzc2lnbihpbWFnZURhdGEsIHtcbiAgICAgICAgd2lkdGg6IHdpZHRoLFxuICAgICAgICBoZWlnaHQ6IGhlaWdodCxcbiAgICAgICAgbGVmdDogKGNhbnZhc0RhdGEud2lkdGggLSB3aWR0aCkgLyAyLFxuICAgICAgICB0b3A6IChjYW52YXNEYXRhLmhlaWdodCAtIGhlaWdodCkgLyAyXG4gICAgICB9KTtcbiAgICAgIHNldFN0eWxlKHRoaXMuaW1hZ2UsIGFzc2lnbih7XG4gICAgICAgIHdpZHRoOiBpbWFnZURhdGEud2lkdGgsXG4gICAgICAgIGhlaWdodDogaW1hZ2VEYXRhLmhlaWdodFxuICAgICAgfSwgZ2V0VHJhbnNmb3Jtcyhhc3NpZ24oe1xuICAgICAgICB0cmFuc2xhdGVYOiBpbWFnZURhdGEubGVmdCxcbiAgICAgICAgdHJhbnNsYXRlWTogaW1hZ2VEYXRhLnRvcFxuICAgICAgfSwgaW1hZ2VEYXRhKSkpKTtcbiAgICAgIGlmIChjaGFuZ2VkKSB7XG4gICAgICAgIHRoaXMub3V0cHV0KCk7XG4gICAgICB9XG4gICAgfSxcbiAgICBpbml0Q3JvcEJveDogZnVuY3Rpb24gaW5pdENyb3BCb3goKSB7XG4gICAgICB2YXIgb3B0aW9ucyA9IHRoaXMub3B0aW9ucyxcbiAgICAgICAgY2FudmFzRGF0YSA9IHRoaXMuY2FudmFzRGF0YTtcbiAgICAgIHZhciBhc3BlY3RSYXRpbyA9IG9wdGlvbnMuYXNwZWN0UmF0aW8gfHwgb3B0aW9ucy5pbml0aWFsQXNwZWN0UmF0aW87XG4gICAgICB2YXIgYXV0b0Nyb3BBcmVhID0gTnVtYmVyKG9wdGlvbnMuYXV0b0Nyb3BBcmVhKSB8fCAwLjg7XG4gICAgICB2YXIgY3JvcEJveERhdGEgPSB7XG4gICAgICAgIHdpZHRoOiBjYW52YXNEYXRhLndpZHRoLFxuICAgICAgICBoZWlnaHQ6IGNhbnZhc0RhdGEuaGVpZ2h0XG4gICAgICB9O1xuICAgICAgaWYgKGFzcGVjdFJhdGlvKSB7XG4gICAgICAgIGlmIChjYW52YXNEYXRhLmhlaWdodCAqIGFzcGVjdFJhdGlvID4gY2FudmFzRGF0YS53aWR0aCkge1xuICAgICAgICAgIGNyb3BCb3hEYXRhLmhlaWdodCA9IGNyb3BCb3hEYXRhLndpZHRoIC8gYXNwZWN0UmF0aW87XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgY3JvcEJveERhdGEud2lkdGggPSBjcm9wQm94RGF0YS5oZWlnaHQgKiBhc3BlY3RSYXRpbztcbiAgICAgICAgfVxuICAgICAgfVxuICAgICAgdGhpcy5jcm9wQm94RGF0YSA9IGNyb3BCb3hEYXRhO1xuICAgICAgdGhpcy5saW1pdENyb3BCb3godHJ1ZSwgdHJ1ZSk7XG5cbiAgICAgIC8vIEluaXRpYWxpemUgYXV0byBjcm9wIGFyZWFcbiAgICAgIGNyb3BCb3hEYXRhLndpZHRoID0gTWF0aC5taW4oTWF0aC5tYXgoY3JvcEJveERhdGEud2lkdGgsIGNyb3BCb3hEYXRhLm1pbldpZHRoKSwgY3JvcEJveERhdGEubWF4V2lkdGgpO1xuICAgICAgY3JvcEJveERhdGEuaGVpZ2h0ID0gTWF0aC5taW4oTWF0aC5tYXgoY3JvcEJveERhdGEuaGVpZ2h0LCBjcm9wQm94RGF0YS5taW5IZWlnaHQpLCBjcm9wQm94RGF0YS5tYXhIZWlnaHQpO1xuXG4gICAgICAvLyBUaGUgd2lkdGgvaGVpZ2h0IG9mIGF1dG8gY3JvcCBhcmVhIG11c3QgbGFyZ2UgdGhhbiBcIm1pbldpZHRoL0hlaWdodFwiXG4gICAgICBjcm9wQm94RGF0YS53aWR0aCA9IE1hdGgubWF4KGNyb3BCb3hEYXRhLm1pbldpZHRoLCBjcm9wQm94RGF0YS53aWR0aCAqIGF1dG9Dcm9wQXJlYSk7XG4gICAgICBjcm9wQm94RGF0YS5oZWlnaHQgPSBNYXRoLm1heChjcm9wQm94RGF0YS5taW5IZWlnaHQsIGNyb3BCb3hEYXRhLmhlaWdodCAqIGF1dG9Dcm9wQXJlYSk7XG4gICAgICBjcm9wQm94RGF0YS5sZWZ0ID0gY2FudmFzRGF0YS5sZWZ0ICsgKGNhbnZhc0RhdGEud2lkdGggLSBjcm9wQm94RGF0YS53aWR0aCkgLyAyO1xuICAgICAgY3JvcEJveERhdGEudG9wID0gY2FudmFzRGF0YS50b3AgKyAoY2FudmFzRGF0YS5oZWlnaHQgLSBjcm9wQm94RGF0YS5oZWlnaHQpIC8gMjtcbiAgICAgIGNyb3BCb3hEYXRhLm9sZExlZnQgPSBjcm9wQm94RGF0YS5sZWZ0O1xuICAgICAgY3JvcEJveERhdGEub2xkVG9wID0gY3JvcEJveERhdGEudG9wO1xuICAgICAgdGhpcy5pbml0aWFsQ3JvcEJveERhdGEgPSBhc3NpZ24oe30sIGNyb3BCb3hEYXRhKTtcbiAgICB9LFxuICAgIGxpbWl0Q3JvcEJveDogZnVuY3Rpb24gbGltaXRDcm9wQm94KHNpemVMaW1pdGVkLCBwb3NpdGlvbkxpbWl0ZWQpIHtcbiAgICAgIHZhciBvcHRpb25zID0gdGhpcy5vcHRpb25zLFxuICAgICAgICBjb250YWluZXJEYXRhID0gdGhpcy5jb250YWluZXJEYXRhLFxuICAgICAgICBjYW52YXNEYXRhID0gdGhpcy5jYW52YXNEYXRhLFxuICAgICAgICBjcm9wQm94RGF0YSA9IHRoaXMuY3JvcEJveERhdGEsXG4gICAgICAgIGxpbWl0ZWQgPSB0aGlzLmxpbWl0ZWQ7XG4gICAgICB2YXIgYXNwZWN0UmF0aW8gPSBvcHRpb25zLmFzcGVjdFJhdGlvO1xuICAgICAgaWYgKHNpemVMaW1pdGVkKSB7XG4gICAgICAgIHZhciBtaW5Dcm9wQm94V2lkdGggPSBOdW1iZXIob3B0aW9ucy5taW5Dcm9wQm94V2lkdGgpIHx8IDA7XG4gICAgICAgIHZhciBtaW5Dcm9wQm94SGVpZ2h0ID0gTnVtYmVyKG9wdGlvbnMubWluQ3JvcEJveEhlaWdodCkgfHwgMDtcbiAgICAgICAgdmFyIG1heENyb3BCb3hXaWR0aCA9IGxpbWl0ZWQgPyBNYXRoLm1pbihjb250YWluZXJEYXRhLndpZHRoLCBjYW52YXNEYXRhLndpZHRoLCBjYW52YXNEYXRhLndpZHRoICsgY2FudmFzRGF0YS5sZWZ0LCBjb250YWluZXJEYXRhLndpZHRoIC0gY2FudmFzRGF0YS5sZWZ0KSA6IGNvbnRhaW5lckRhdGEud2lkdGg7XG4gICAgICAgIHZhciBtYXhDcm9wQm94SGVpZ2h0ID0gbGltaXRlZCA/IE1hdGgubWluKGNvbnRhaW5lckRhdGEuaGVpZ2h0LCBjYW52YXNEYXRhLmhlaWdodCwgY2FudmFzRGF0YS5oZWlnaHQgKyBjYW52YXNEYXRhLnRvcCwgY29udGFpbmVyRGF0YS5oZWlnaHQgLSBjYW52YXNEYXRhLnRvcCkgOiBjb250YWluZXJEYXRhLmhlaWdodDtcblxuICAgICAgICAvLyBUaGUgbWluL21heENyb3BCb3hXaWR0aC9IZWlnaHQgbXVzdCBiZSBsZXNzIHRoYW4gY29udGFpbmVyJ3Mgd2lkdGgvaGVpZ2h0XG4gICAgICAgIG1pbkNyb3BCb3hXaWR0aCA9IE1hdGgubWluKG1pbkNyb3BCb3hXaWR0aCwgY29udGFpbmVyRGF0YS53aWR0aCk7XG4gICAgICAgIG1pbkNyb3BCb3hIZWlnaHQgPSBNYXRoLm1pbihtaW5Dcm9wQm94SGVpZ2h0LCBjb250YWluZXJEYXRhLmhlaWdodCk7XG4gICAgICAgIGlmIChhc3BlY3RSYXRpbykge1xuICAgICAgICAgIGlmIChtaW5Dcm9wQm94V2lkdGggJiYgbWluQ3JvcEJveEhlaWdodCkge1xuICAgICAgICAgICAgaWYgKG1pbkNyb3BCb3hIZWlnaHQgKiBhc3BlY3RSYXRpbyA+IG1pbkNyb3BCb3hXaWR0aCkge1xuICAgICAgICAgICAgICBtaW5Dcm9wQm94SGVpZ2h0ID0gbWluQ3JvcEJveFdpZHRoIC8gYXNwZWN0UmF0aW87XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICBtaW5Dcm9wQm94V2lkdGggPSBtaW5Dcm9wQm94SGVpZ2h0ICogYXNwZWN0UmF0aW87XG4gICAgICAgICAgICB9XG4gICAgICAgICAgfSBlbHNlIGlmIChtaW5Dcm9wQm94V2lkdGgpIHtcbiAgICAgICAgICAgIG1pbkNyb3BCb3hIZWlnaHQgPSBtaW5Dcm9wQm94V2lkdGggLyBhc3BlY3RSYXRpbztcbiAgICAgICAgICB9IGVsc2UgaWYgKG1pbkNyb3BCb3hIZWlnaHQpIHtcbiAgICAgICAgICAgIG1pbkNyb3BCb3hXaWR0aCA9IG1pbkNyb3BCb3hIZWlnaHQgKiBhc3BlY3RSYXRpbztcbiAgICAgICAgICB9XG4gICAgICAgICAgaWYgKG1heENyb3BCb3hIZWlnaHQgKiBhc3BlY3RSYXRpbyA+IG1heENyb3BCb3hXaWR0aCkge1xuICAgICAgICAgICAgbWF4Q3JvcEJveEhlaWdodCA9IG1heENyb3BCb3hXaWR0aCAvIGFzcGVjdFJhdGlvO1xuICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICBtYXhDcm9wQm94V2lkdGggPSBtYXhDcm9wQm94SGVpZ2h0ICogYXNwZWN0UmF0aW87XG4gICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgLy8gVGhlIG1pbldpZHRoL0hlaWdodCBtdXN0IGJlIGxlc3MgdGhhbiBtYXhXaWR0aC9IZWlnaHRcbiAgICAgICAgY3JvcEJveERhdGEubWluV2lkdGggPSBNYXRoLm1pbihtaW5Dcm9wQm94V2lkdGgsIG1heENyb3BCb3hXaWR0aCk7XG4gICAgICAgIGNyb3BCb3hEYXRhLm1pbkhlaWdodCA9IE1hdGgubWluKG1pbkNyb3BCb3hIZWlnaHQsIG1heENyb3BCb3hIZWlnaHQpO1xuICAgICAgICBjcm9wQm94RGF0YS5tYXhXaWR0aCA9IG1heENyb3BCb3hXaWR0aDtcbiAgICAgICAgY3JvcEJveERhdGEubWF4SGVpZ2h0ID0gbWF4Q3JvcEJveEhlaWdodDtcbiAgICAgIH1cbiAgICAgIGlmIChwb3NpdGlvbkxpbWl0ZWQpIHtcbiAgICAgICAgaWYgKGxpbWl0ZWQpIHtcbiAgICAgICAgICBjcm9wQm94RGF0YS5taW5MZWZ0ID0gTWF0aC5tYXgoMCwgY2FudmFzRGF0YS5sZWZ0KTtcbiAgICAgICAgICBjcm9wQm94RGF0YS5taW5Ub3AgPSBNYXRoLm1heCgwLCBjYW52YXNEYXRhLnRvcCk7XG4gICAgICAgICAgY3JvcEJveERhdGEubWF4TGVmdCA9IE1hdGgubWluKGNvbnRhaW5lckRhdGEud2lkdGgsIGNhbnZhc0RhdGEubGVmdCArIGNhbnZhc0RhdGEud2lkdGgpIC0gY3JvcEJveERhdGEud2lkdGg7XG4gICAgICAgICAgY3JvcEJveERhdGEubWF4VG9wID0gTWF0aC5taW4oY29udGFpbmVyRGF0YS5oZWlnaHQsIGNhbnZhc0RhdGEudG9wICsgY2FudmFzRGF0YS5oZWlnaHQpIC0gY3JvcEJveERhdGEuaGVpZ2h0O1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgIGNyb3BCb3hEYXRhLm1pbkxlZnQgPSAwO1xuICAgICAgICAgIGNyb3BCb3hEYXRhLm1pblRvcCA9IDA7XG4gICAgICAgICAgY3JvcEJveERhdGEubWF4TGVmdCA9IGNvbnRhaW5lckRhdGEud2lkdGggLSBjcm9wQm94RGF0YS53aWR0aDtcbiAgICAgICAgICBjcm9wQm94RGF0YS5tYXhUb3AgPSBjb250YWluZXJEYXRhLmhlaWdodCAtIGNyb3BCb3hEYXRhLmhlaWdodDtcbiAgICAgICAgfVxuICAgICAgfVxuICAgIH0sXG4gICAgcmVuZGVyQ3JvcEJveDogZnVuY3Rpb24gcmVuZGVyQ3JvcEJveCgpIHtcbiAgICAgIHZhciBvcHRpb25zID0gdGhpcy5vcHRpb25zLFxuICAgICAgICBjb250YWluZXJEYXRhID0gdGhpcy5jb250YWluZXJEYXRhLFxuICAgICAgICBjcm9wQm94RGF0YSA9IHRoaXMuY3JvcEJveERhdGE7XG4gICAgICBpZiAoY3JvcEJveERhdGEud2lkdGggPiBjcm9wQm94RGF0YS5tYXhXaWR0aCB8fCBjcm9wQm94RGF0YS53aWR0aCA8IGNyb3BCb3hEYXRhLm1pbldpZHRoKSB7XG4gICAgICAgIGNyb3BCb3hEYXRhLmxlZnQgPSBjcm9wQm94RGF0YS5vbGRMZWZ0O1xuICAgICAgfVxuICAgICAgaWYgKGNyb3BCb3hEYXRhLmhlaWdodCA+IGNyb3BCb3hEYXRhLm1heEhlaWdodCB8fCBjcm9wQm94RGF0YS5oZWlnaHQgPCBjcm9wQm94RGF0YS5taW5IZWlnaHQpIHtcbiAgICAgICAgY3JvcEJveERhdGEudG9wID0gY3JvcEJveERhdGEub2xkVG9wO1xuICAgICAgfVxuICAgICAgY3JvcEJveERhdGEud2lkdGggPSBNYXRoLm1pbihNYXRoLm1heChjcm9wQm94RGF0YS53aWR0aCwgY3JvcEJveERhdGEubWluV2lkdGgpLCBjcm9wQm94RGF0YS5tYXhXaWR0aCk7XG4gICAgICBjcm9wQm94RGF0YS5oZWlnaHQgPSBNYXRoLm1pbihNYXRoLm1heChjcm9wQm94RGF0YS5oZWlnaHQsIGNyb3BCb3hEYXRhLm1pbkhlaWdodCksIGNyb3BCb3hEYXRhLm1heEhlaWdodCk7XG4gICAgICB0aGlzLmxpbWl0Q3JvcEJveChmYWxzZSwgdHJ1ZSk7XG4gICAgICBjcm9wQm94RGF0YS5sZWZ0ID0gTWF0aC5taW4oTWF0aC5tYXgoY3JvcEJveERhdGEubGVmdCwgY3JvcEJveERhdGEubWluTGVmdCksIGNyb3BCb3hEYXRhLm1heExlZnQpO1xuICAgICAgY3JvcEJveERhdGEudG9wID0gTWF0aC5taW4oTWF0aC5tYXgoY3JvcEJveERhdGEudG9wLCBjcm9wQm94RGF0YS5taW5Ub3ApLCBjcm9wQm94RGF0YS5tYXhUb3ApO1xuICAgICAgY3JvcEJveERhdGEub2xkTGVmdCA9IGNyb3BCb3hEYXRhLmxlZnQ7XG4gICAgICBjcm9wQm94RGF0YS5vbGRUb3AgPSBjcm9wQm94RGF0YS50b3A7XG4gICAgICBpZiAob3B0aW9ucy5tb3ZhYmxlICYmIG9wdGlvbnMuY3JvcEJveE1vdmFibGUpIHtcbiAgICAgICAgLy8gVHVybiB0byBtb3ZlIHRoZSBjYW52YXMgd2hlbiB0aGUgY3JvcCBib3ggaXMgZXF1YWwgdG8gdGhlIGNvbnRhaW5lclxuICAgICAgICBzZXREYXRhKHRoaXMuZmFjZSwgREFUQV9BQ1RJT04sIGNyb3BCb3hEYXRhLndpZHRoID49IGNvbnRhaW5lckRhdGEud2lkdGggJiYgY3JvcEJveERhdGEuaGVpZ2h0ID49IGNvbnRhaW5lckRhdGEuaGVpZ2h0ID8gQUNUSU9OX01PVkUgOiBBQ1RJT05fQUxMKTtcbiAgICAgIH1cbiAgICAgIHNldFN0eWxlKHRoaXMuY3JvcEJveCwgYXNzaWduKHtcbiAgICAgICAgd2lkdGg6IGNyb3BCb3hEYXRhLndpZHRoLFxuICAgICAgICBoZWlnaHQ6IGNyb3BCb3hEYXRhLmhlaWdodFxuICAgICAgfSwgZ2V0VHJhbnNmb3Jtcyh7XG4gICAgICAgIHRyYW5zbGF0ZVg6IGNyb3BCb3hEYXRhLmxlZnQsXG4gICAgICAgIHRyYW5zbGF0ZVk6IGNyb3BCb3hEYXRhLnRvcFxuICAgICAgfSkpKTtcbiAgICAgIGlmICh0aGlzLmNyb3BwZWQgJiYgdGhpcy5saW1pdGVkKSB7XG4gICAgICAgIHRoaXMubGltaXRDYW52YXModHJ1ZSwgdHJ1ZSk7XG4gICAgICB9XG4gICAgICBpZiAoIXRoaXMuZGlzYWJsZWQpIHtcbiAgICAgICAgdGhpcy5vdXRwdXQoKTtcbiAgICAgIH1cbiAgICB9LFxuICAgIG91dHB1dDogZnVuY3Rpb24gb3V0cHV0KCkge1xuICAgICAgdGhpcy5wcmV2aWV3KCk7XG4gICAgICBkaXNwYXRjaEV2ZW50KHRoaXMuZWxlbWVudCwgRVZFTlRfQ1JPUCwgdGhpcy5nZXREYXRhKCkpO1xuICAgIH1cbiAgfTtcblxuICB2YXIgcHJldmlldyA9IHtcbiAgICBpbml0UHJldmlldzogZnVuY3Rpb24gaW5pdFByZXZpZXcoKSB7XG4gICAgICB2YXIgZWxlbWVudCA9IHRoaXMuZWxlbWVudCxcbiAgICAgICAgY3Jvc3NPcmlnaW4gPSB0aGlzLmNyb3NzT3JpZ2luO1xuICAgICAgdmFyIHByZXZpZXcgPSB0aGlzLm9wdGlvbnMucHJldmlldztcbiAgICAgIHZhciB1cmwgPSBjcm9zc09yaWdpbiA/IHRoaXMuY3Jvc3NPcmlnaW5VcmwgOiB0aGlzLnVybDtcbiAgICAgIHZhciBhbHQgPSBlbGVtZW50LmFsdCB8fCAnVGhlIGltYWdlIHRvIHByZXZpZXcnO1xuICAgICAgdmFyIGltYWdlID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnaW1nJyk7XG4gICAgICBpZiAoY3Jvc3NPcmlnaW4pIHtcbiAgICAgICAgaW1hZ2UuY3Jvc3NPcmlnaW4gPSBjcm9zc09yaWdpbjtcbiAgICAgIH1cbiAgICAgIGltYWdlLnNyYyA9IHVybDtcbiAgICAgIGltYWdlLmFsdCA9IGFsdDtcbiAgICAgIHRoaXMudmlld0JveC5hcHBlbmRDaGlsZChpbWFnZSk7XG4gICAgICB0aGlzLnZpZXdCb3hJbWFnZSA9IGltYWdlO1xuICAgICAgaWYgKCFwcmV2aWV3KSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIHZhciBwcmV2aWV3cyA9IHByZXZpZXc7XG4gICAgICBpZiAodHlwZW9mIHByZXZpZXcgPT09ICdzdHJpbmcnKSB7XG4gICAgICAgIHByZXZpZXdzID0gZWxlbWVudC5vd25lckRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwocHJldmlldyk7XG4gICAgICB9IGVsc2UgaWYgKHByZXZpZXcucXVlcnlTZWxlY3Rvcikge1xuICAgICAgICBwcmV2aWV3cyA9IFtwcmV2aWV3XTtcbiAgICAgIH1cbiAgICAgIHRoaXMucHJldmlld3MgPSBwcmV2aWV3cztcbiAgICAgIGZvckVhY2gocHJldmlld3MsIGZ1bmN0aW9uIChlbCkge1xuICAgICAgICB2YXIgaW1nID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnaW1nJyk7XG5cbiAgICAgICAgLy8gU2F2ZSB0aGUgb3JpZ2luYWwgc2l6ZSBmb3IgcmVjb3ZlclxuICAgICAgICBzZXREYXRhKGVsLCBEQVRBX1BSRVZJRVcsIHtcbiAgICAgICAgICB3aWR0aDogZWwub2Zmc2V0V2lkdGgsXG4gICAgICAgICAgaGVpZ2h0OiBlbC5vZmZzZXRIZWlnaHQsXG4gICAgICAgICAgaHRtbDogZWwuaW5uZXJIVE1MXG4gICAgICAgIH0pO1xuICAgICAgICBpZiAoY3Jvc3NPcmlnaW4pIHtcbiAgICAgICAgICBpbWcuY3Jvc3NPcmlnaW4gPSBjcm9zc09yaWdpbjtcbiAgICAgICAgfVxuICAgICAgICBpbWcuc3JjID0gdXJsO1xuICAgICAgICBpbWcuYWx0ID0gYWx0O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBPdmVycmlkZSBpbWcgZWxlbWVudCBzdHlsZXNcbiAgICAgICAgICogQWRkIGBkaXNwbGF5OmJsb2NrYCB0byBhdm9pZCBtYXJnaW4gdG9wIGlzc3VlXG4gICAgICAgICAqIEFkZCBgaGVpZ2h0OmF1dG9gIHRvIG92ZXJyaWRlIGBoZWlnaHRgIGF0dHJpYnV0ZSBvbiBJRThcbiAgICAgICAgICogKE9jY3VyIG9ubHkgd2hlbiBtYXJnaW4tdG9wIDw9IC1oZWlnaHQpXG4gICAgICAgICAqL1xuICAgICAgICBpbWcuc3R5bGUuY3NzVGV4dCA9ICdkaXNwbGF5OmJsb2NrOycgKyAnd2lkdGg6MTAwJTsnICsgJ2hlaWdodDphdXRvOycgKyAnbWluLXdpZHRoOjAhaW1wb3J0YW50OycgKyAnbWluLWhlaWdodDowIWltcG9ydGFudDsnICsgJ21heC13aWR0aDpub25lIWltcG9ydGFudDsnICsgJ21heC1oZWlnaHQ6bm9uZSFpbXBvcnRhbnQ7JyArICdpbWFnZS1vcmllbnRhdGlvbjowZGVnIWltcG9ydGFudDtcIic7XG4gICAgICAgIGVsLmlubmVySFRNTCA9ICcnO1xuICAgICAgICBlbC5hcHBlbmRDaGlsZChpbWcpO1xuICAgICAgfSk7XG4gICAgfSxcbiAgICByZXNldFByZXZpZXc6IGZ1bmN0aW9uIHJlc2V0UHJldmlldygpIHtcbiAgICAgIGZvckVhY2godGhpcy5wcmV2aWV3cywgZnVuY3Rpb24gKGVsZW1lbnQpIHtcbiAgICAgICAgdmFyIGRhdGEgPSBnZXREYXRhKGVsZW1lbnQsIERBVEFfUFJFVklFVyk7XG4gICAgICAgIHNldFN0eWxlKGVsZW1lbnQsIHtcbiAgICAgICAgICB3aWR0aDogZGF0YS53aWR0aCxcbiAgICAgICAgICBoZWlnaHQ6IGRhdGEuaGVpZ2h0XG4gICAgICAgIH0pO1xuICAgICAgICBlbGVtZW50LmlubmVySFRNTCA9IGRhdGEuaHRtbDtcbiAgICAgICAgcmVtb3ZlRGF0YShlbGVtZW50LCBEQVRBX1BSRVZJRVcpO1xuICAgICAgfSk7XG4gICAgfSxcbiAgICBwcmV2aWV3OiBmdW5jdGlvbiBwcmV2aWV3KCkge1xuICAgICAgdmFyIGltYWdlRGF0YSA9IHRoaXMuaW1hZ2VEYXRhLFxuICAgICAgICBjYW52YXNEYXRhID0gdGhpcy5jYW52YXNEYXRhLFxuICAgICAgICBjcm9wQm94RGF0YSA9IHRoaXMuY3JvcEJveERhdGE7XG4gICAgICB2YXIgY3JvcEJveFdpZHRoID0gY3JvcEJveERhdGEud2lkdGgsXG4gICAgICAgIGNyb3BCb3hIZWlnaHQgPSBjcm9wQm94RGF0YS5oZWlnaHQ7XG4gICAgICB2YXIgd2lkdGggPSBpbWFnZURhdGEud2lkdGgsXG4gICAgICAgIGhlaWdodCA9IGltYWdlRGF0YS5oZWlnaHQ7XG4gICAgICB2YXIgbGVmdCA9IGNyb3BCb3hEYXRhLmxlZnQgLSBjYW52YXNEYXRhLmxlZnQgLSBpbWFnZURhdGEubGVmdDtcbiAgICAgIHZhciB0b3AgPSBjcm9wQm94RGF0YS50b3AgLSBjYW52YXNEYXRhLnRvcCAtIGltYWdlRGF0YS50b3A7XG4gICAgICBpZiAoIXRoaXMuY3JvcHBlZCB8fCB0aGlzLmRpc2FibGVkKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIHNldFN0eWxlKHRoaXMudmlld0JveEltYWdlLCBhc3NpZ24oe1xuICAgICAgICB3aWR0aDogd2lkdGgsXG4gICAgICAgIGhlaWdodDogaGVpZ2h0XG4gICAgICB9LCBnZXRUcmFuc2Zvcm1zKGFzc2lnbih7XG4gICAgICAgIHRyYW5zbGF0ZVg6IC1sZWZ0LFxuICAgICAgICB0cmFuc2xhdGVZOiAtdG9wXG4gICAgICB9LCBpbWFnZURhdGEpKSkpO1xuICAgICAgZm9yRWFjaCh0aGlzLnByZXZpZXdzLCBmdW5jdGlvbiAoZWxlbWVudCkge1xuICAgICAgICB2YXIgZGF0YSA9IGdldERhdGEoZWxlbWVudCwgREFUQV9QUkVWSUVXKTtcbiAgICAgICAgdmFyIG9yaWdpbmFsV2lkdGggPSBkYXRhLndpZHRoO1xuICAgICAgICB2YXIgb3JpZ2luYWxIZWlnaHQgPSBkYXRhLmhlaWdodDtcbiAgICAgICAgdmFyIG5ld1dpZHRoID0gb3JpZ2luYWxXaWR0aDtcbiAgICAgICAgdmFyIG5ld0hlaWdodCA9IG9yaWdpbmFsSGVpZ2h0O1xuICAgICAgICB2YXIgcmF0aW8gPSAxO1xuICAgICAgICBpZiAoY3JvcEJveFdpZHRoKSB7XG4gICAgICAgICAgcmF0aW8gPSBvcmlnaW5hbFdpZHRoIC8gY3JvcEJveFdpZHRoO1xuICAgICAgICAgIG5ld0hlaWdodCA9IGNyb3BCb3hIZWlnaHQgKiByYXRpbztcbiAgICAgICAgfVxuICAgICAgICBpZiAoY3JvcEJveEhlaWdodCAmJiBuZXdIZWlnaHQgPiBvcmlnaW5hbEhlaWdodCkge1xuICAgICAgICAgIHJhdGlvID0gb3JpZ2luYWxIZWlnaHQgLyBjcm9wQm94SGVpZ2h0O1xuICAgICAgICAgIG5ld1dpZHRoID0gY3JvcEJveFdpZHRoICogcmF0aW87XG4gICAgICAgICAgbmV3SGVpZ2h0ID0gb3JpZ2luYWxIZWlnaHQ7XG4gICAgICAgIH1cbiAgICAgICAgc2V0U3R5bGUoZWxlbWVudCwge1xuICAgICAgICAgIHdpZHRoOiBuZXdXaWR0aCxcbiAgICAgICAgICBoZWlnaHQ6IG5ld0hlaWdodFxuICAgICAgICB9KTtcbiAgICAgICAgc2V0U3R5bGUoZWxlbWVudC5nZXRFbGVtZW50c0J5VGFnTmFtZSgnaW1nJylbMF0sIGFzc2lnbih7XG4gICAgICAgICAgd2lkdGg6IHdpZHRoICogcmF0aW8sXG4gICAgICAgICAgaGVpZ2h0OiBoZWlnaHQgKiByYXRpb1xuICAgICAgICB9LCBnZXRUcmFuc2Zvcm1zKGFzc2lnbih7XG4gICAgICAgICAgdHJhbnNsYXRlWDogLWxlZnQgKiByYXRpbyxcbiAgICAgICAgICB0cmFuc2xhdGVZOiAtdG9wICogcmF0aW9cbiAgICAgICAgfSwgaW1hZ2VEYXRhKSkpKTtcbiAgICAgIH0pO1xuICAgIH1cbiAgfTtcblxuICB2YXIgZXZlbnRzID0ge1xuICAgIGJpbmQ6IGZ1bmN0aW9uIGJpbmQoKSB7XG4gICAgICB2YXIgZWxlbWVudCA9IHRoaXMuZWxlbWVudCxcbiAgICAgICAgb3B0aW9ucyA9IHRoaXMub3B0aW9ucyxcbiAgICAgICAgY3JvcHBlciA9IHRoaXMuY3JvcHBlcjtcbiAgICAgIGlmIChpc0Z1bmN0aW9uKG9wdGlvbnMuY3JvcHN0YXJ0KSkge1xuICAgICAgICBhZGRMaXN0ZW5lcihlbGVtZW50LCBFVkVOVF9DUk9QX1NUQVJULCBvcHRpb25zLmNyb3BzdGFydCk7XG4gICAgICB9XG4gICAgICBpZiAoaXNGdW5jdGlvbihvcHRpb25zLmNyb3Btb3ZlKSkge1xuICAgICAgICBhZGRMaXN0ZW5lcihlbGVtZW50LCBFVkVOVF9DUk9QX01PVkUsIG9wdGlvbnMuY3JvcG1vdmUpO1xuICAgICAgfVxuICAgICAgaWYgKGlzRnVuY3Rpb24ob3B0aW9ucy5jcm9wZW5kKSkge1xuICAgICAgICBhZGRMaXN0ZW5lcihlbGVtZW50LCBFVkVOVF9DUk9QX0VORCwgb3B0aW9ucy5jcm9wZW5kKTtcbiAgICAgIH1cbiAgICAgIGlmIChpc0Z1bmN0aW9uKG9wdGlvbnMuY3JvcCkpIHtcbiAgICAgICAgYWRkTGlzdGVuZXIoZWxlbWVudCwgRVZFTlRfQ1JPUCwgb3B0aW9ucy5jcm9wKTtcbiAgICAgIH1cbiAgICAgIGlmIChpc0Z1bmN0aW9uKG9wdGlvbnMuem9vbSkpIHtcbiAgICAgICAgYWRkTGlzdGVuZXIoZWxlbWVudCwgRVZFTlRfWk9PTSwgb3B0aW9ucy56b29tKTtcbiAgICAgIH1cbiAgICAgIGFkZExpc3RlbmVyKGNyb3BwZXIsIEVWRU5UX1BPSU5URVJfRE9XTiwgdGhpcy5vbkNyb3BTdGFydCA9IHRoaXMuY3JvcFN0YXJ0LmJpbmQodGhpcykpO1xuICAgICAgaWYgKG9wdGlvbnMuem9vbWFibGUgJiYgb3B0aW9ucy56b29tT25XaGVlbCkge1xuICAgICAgICBhZGRMaXN0ZW5lcihjcm9wcGVyLCBFVkVOVF9XSEVFTCwgdGhpcy5vbldoZWVsID0gdGhpcy53aGVlbC5iaW5kKHRoaXMpLCB7XG4gICAgICAgICAgcGFzc2l2ZTogZmFsc2UsXG4gICAgICAgICAgY2FwdHVyZTogdHJ1ZVxuICAgICAgICB9KTtcbiAgICAgIH1cbiAgICAgIGlmIChvcHRpb25zLnRvZ2dsZURyYWdNb2RlT25EYmxjbGljaykge1xuICAgICAgICBhZGRMaXN0ZW5lcihjcm9wcGVyLCBFVkVOVF9EQkxDTElDSywgdGhpcy5vbkRibGNsaWNrID0gdGhpcy5kYmxjbGljay5iaW5kKHRoaXMpKTtcbiAgICAgIH1cbiAgICAgIGFkZExpc3RlbmVyKGVsZW1lbnQub3duZXJEb2N1bWVudCwgRVZFTlRfUE9JTlRFUl9NT1ZFLCB0aGlzLm9uQ3JvcE1vdmUgPSB0aGlzLmNyb3BNb3ZlLmJpbmQodGhpcykpO1xuICAgICAgYWRkTGlzdGVuZXIoZWxlbWVudC5vd25lckRvY3VtZW50LCBFVkVOVF9QT0lOVEVSX1VQLCB0aGlzLm9uQ3JvcEVuZCA9IHRoaXMuY3JvcEVuZC5iaW5kKHRoaXMpKTtcbiAgICAgIGlmIChvcHRpb25zLnJlc3BvbnNpdmUpIHtcbiAgICAgICAgYWRkTGlzdGVuZXIod2luZG93LCBFVkVOVF9SRVNJWkUsIHRoaXMub25SZXNpemUgPSB0aGlzLnJlc2l6ZS5iaW5kKHRoaXMpKTtcbiAgICAgIH1cbiAgICB9LFxuICAgIHVuYmluZDogZnVuY3Rpb24gdW5iaW5kKCkge1xuICAgICAgdmFyIGVsZW1lbnQgPSB0aGlzLmVsZW1lbnQsXG4gICAgICAgIG9wdGlvbnMgPSB0aGlzLm9wdGlvbnMsXG4gICAgICAgIGNyb3BwZXIgPSB0aGlzLmNyb3BwZXI7XG4gICAgICBpZiAoaXNGdW5jdGlvbihvcHRpb25zLmNyb3BzdGFydCkpIHtcbiAgICAgICAgcmVtb3ZlTGlzdGVuZXIoZWxlbWVudCwgRVZFTlRfQ1JPUF9TVEFSVCwgb3B0aW9ucy5jcm9wc3RhcnQpO1xuICAgICAgfVxuICAgICAgaWYgKGlzRnVuY3Rpb24ob3B0aW9ucy5jcm9wbW92ZSkpIHtcbiAgICAgICAgcmVtb3ZlTGlzdGVuZXIoZWxlbWVudCwgRVZFTlRfQ1JPUF9NT1ZFLCBvcHRpb25zLmNyb3Btb3ZlKTtcbiAgICAgIH1cbiAgICAgIGlmIChpc0Z1bmN0aW9uKG9wdGlvbnMuY3JvcGVuZCkpIHtcbiAgICAgICAgcmVtb3ZlTGlzdGVuZXIoZWxlbWVudCwgRVZFTlRfQ1JPUF9FTkQsIG9wdGlvbnMuY3JvcGVuZCk7XG4gICAgICB9XG4gICAgICBpZiAoaXNGdW5jdGlvbihvcHRpb25zLmNyb3ApKSB7XG4gICAgICAgIHJlbW92ZUxpc3RlbmVyKGVsZW1lbnQsIEVWRU5UX0NST1AsIG9wdGlvbnMuY3JvcCk7XG4gICAgICB9XG4gICAgICBpZiAoaXNGdW5jdGlvbihvcHRpb25zLnpvb20pKSB7XG4gICAgICAgIHJlbW92ZUxpc3RlbmVyKGVsZW1lbnQsIEVWRU5UX1pPT00sIG9wdGlvbnMuem9vbSk7XG4gICAgICB9XG4gICAgICByZW1vdmVMaXN0ZW5lcihjcm9wcGVyLCBFVkVOVF9QT0lOVEVSX0RPV04sIHRoaXMub25Dcm9wU3RhcnQpO1xuICAgICAgaWYgKG9wdGlvbnMuem9vbWFibGUgJiYgb3B0aW9ucy56b29tT25XaGVlbCkge1xuICAgICAgICByZW1vdmVMaXN0ZW5lcihjcm9wcGVyLCBFVkVOVF9XSEVFTCwgdGhpcy5vbldoZWVsLCB7XG4gICAgICAgICAgcGFzc2l2ZTogZmFsc2UsXG4gICAgICAgICAgY2FwdHVyZTogdHJ1ZVxuICAgICAgICB9KTtcbiAgICAgIH1cbiAgICAgIGlmIChvcHRpb25zLnRvZ2dsZURyYWdNb2RlT25EYmxjbGljaykge1xuICAgICAgICByZW1vdmVMaXN0ZW5lcihjcm9wcGVyLCBFVkVOVF9EQkxDTElDSywgdGhpcy5vbkRibGNsaWNrKTtcbiAgICAgIH1cbiAgICAgIHJlbW92ZUxpc3RlbmVyKGVsZW1lbnQub3duZXJEb2N1bWVudCwgRVZFTlRfUE9JTlRFUl9NT1ZFLCB0aGlzLm9uQ3JvcE1vdmUpO1xuICAgICAgcmVtb3ZlTGlzdGVuZXIoZWxlbWVudC5vd25lckRvY3VtZW50LCBFVkVOVF9QT0lOVEVSX1VQLCB0aGlzLm9uQ3JvcEVuZCk7XG4gICAgICBpZiAob3B0aW9ucy5yZXNwb25zaXZlKSB7XG4gICAgICAgIHJlbW92ZUxpc3RlbmVyKHdpbmRvdywgRVZFTlRfUkVTSVpFLCB0aGlzLm9uUmVzaXplKTtcbiAgICAgIH1cbiAgICB9XG4gIH07XG5cbiAgdmFyIGhhbmRsZXJzID0ge1xuICAgIHJlc2l6ZTogZnVuY3Rpb24gcmVzaXplKCkge1xuICAgICAgaWYgKHRoaXMuZGlzYWJsZWQpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgICAgdmFyIG9wdGlvbnMgPSB0aGlzLm9wdGlvbnMsXG4gICAgICAgIGNvbnRhaW5lciA9IHRoaXMuY29udGFpbmVyLFxuICAgICAgICBjb250YWluZXJEYXRhID0gdGhpcy5jb250YWluZXJEYXRhO1xuICAgICAgdmFyIHJhdGlvWCA9IGNvbnRhaW5lci5vZmZzZXRXaWR0aCAvIGNvbnRhaW5lckRhdGEud2lkdGg7XG4gICAgICB2YXIgcmF0aW9ZID0gY29udGFpbmVyLm9mZnNldEhlaWdodCAvIGNvbnRhaW5lckRhdGEuaGVpZ2h0O1xuICAgICAgdmFyIHJhdGlvID0gTWF0aC5hYnMocmF0aW9YIC0gMSkgPiBNYXRoLmFicyhyYXRpb1kgLSAxKSA/IHJhdGlvWCA6IHJhdGlvWTtcblxuICAgICAgLy8gUmVzaXplIHdoZW4gd2lkdGggY2hhbmdlZCBvciBoZWlnaHQgY2hhbmdlZFxuICAgICAgaWYgKHJhdGlvICE9PSAxKSB7XG4gICAgICAgIHZhciBjYW52YXNEYXRhO1xuICAgICAgICB2YXIgY3JvcEJveERhdGE7XG4gICAgICAgIGlmIChvcHRpb25zLnJlc3RvcmUpIHtcbiAgICAgICAgICBjYW52YXNEYXRhID0gdGhpcy5nZXRDYW52YXNEYXRhKCk7XG4gICAgICAgICAgY3JvcEJveERhdGEgPSB0aGlzLmdldENyb3BCb3hEYXRhKCk7XG4gICAgICAgIH1cbiAgICAgICAgdGhpcy5yZW5kZXIoKTtcbiAgICAgICAgaWYgKG9wdGlvbnMucmVzdG9yZSkge1xuICAgICAgICAgIHRoaXMuc2V0Q2FudmFzRGF0YShmb3JFYWNoKGNhbnZhc0RhdGEsIGZ1bmN0aW9uIChuLCBpKSB7XG4gICAgICAgICAgICBjYW52YXNEYXRhW2ldID0gbiAqIHJhdGlvO1xuICAgICAgICAgIH0pKTtcbiAgICAgICAgICB0aGlzLnNldENyb3BCb3hEYXRhKGZvckVhY2goY3JvcEJveERhdGEsIGZ1bmN0aW9uIChuLCBpKSB7XG4gICAgICAgICAgICBjcm9wQm94RGF0YVtpXSA9IG4gKiByYXRpbztcbiAgICAgICAgICB9KSk7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICB9LFxuICAgIGRibGNsaWNrOiBmdW5jdGlvbiBkYmxjbGljaygpIHtcbiAgICAgIGlmICh0aGlzLmRpc2FibGVkIHx8IHRoaXMub3B0aW9ucy5kcmFnTW9kZSA9PT0gRFJBR19NT0RFX05PTkUpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgICAgdGhpcy5zZXREcmFnTW9kZShoYXNDbGFzcyh0aGlzLmRyYWdCb3gsIENMQVNTX0NST1ApID8gRFJBR19NT0RFX01PVkUgOiBEUkFHX01PREVfQ1JPUCk7XG4gICAgfSxcbiAgICB3aGVlbDogZnVuY3Rpb24gd2hlZWwoZXZlbnQpIHtcbiAgICAgIHZhciBfdGhpcyA9IHRoaXM7XG4gICAgICB2YXIgcmF0aW8gPSBOdW1iZXIodGhpcy5vcHRpb25zLndoZWVsWm9vbVJhdGlvKSB8fCAwLjE7XG4gICAgICB2YXIgZGVsdGEgPSAxO1xuICAgICAgaWYgKHRoaXMuZGlzYWJsZWQpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgLy8gTGltaXQgd2hlZWwgc3BlZWQgdG8gcHJldmVudCB6b29tIHRvbyBmYXN0ICgjMjEpXG4gICAgICBpZiAodGhpcy53aGVlbGluZykge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICB0aGlzLndoZWVsaW5nID0gdHJ1ZTtcbiAgICAgIHNldFRpbWVvdXQoZnVuY3Rpb24gKCkge1xuICAgICAgICBfdGhpcy53aGVlbGluZyA9IGZhbHNlO1xuICAgICAgfSwgNTApO1xuICAgICAgaWYgKGV2ZW50LmRlbHRhWSkge1xuICAgICAgICBkZWx0YSA9IGV2ZW50LmRlbHRhWSA+IDAgPyAxIDogLTE7XG4gICAgICB9IGVsc2UgaWYgKGV2ZW50LndoZWVsRGVsdGEpIHtcbiAgICAgICAgZGVsdGEgPSAtZXZlbnQud2hlZWxEZWx0YSAvIDEyMDtcbiAgICAgIH0gZWxzZSBpZiAoZXZlbnQuZGV0YWlsKSB7XG4gICAgICAgIGRlbHRhID0gZXZlbnQuZGV0YWlsID4gMCA/IDEgOiAtMTtcbiAgICAgIH1cbiAgICAgIHRoaXMuem9vbSgtZGVsdGEgKiByYXRpbywgZXZlbnQpO1xuICAgIH0sXG4gICAgY3JvcFN0YXJ0OiBmdW5jdGlvbiBjcm9wU3RhcnQoZXZlbnQpIHtcbiAgICAgIHZhciBidXR0b25zID0gZXZlbnQuYnV0dG9ucyxcbiAgICAgICAgYnV0dG9uID0gZXZlbnQuYnV0dG9uO1xuICAgICAgaWYgKHRoaXMuZGlzYWJsZWRcblxuICAgICAgLy8gSGFuZGxlIG1vdXNlIGV2ZW50IGFuZCBwb2ludGVyIGV2ZW50IGFuZCBpZ25vcmUgdG91Y2ggZXZlbnRcbiAgICAgIHx8IChldmVudC50eXBlID09PSAnbW91c2Vkb3duJyB8fCBldmVudC50eXBlID09PSAncG9pbnRlcmRvd24nICYmIGV2ZW50LnBvaW50ZXJUeXBlID09PSAnbW91c2UnKSAmJiAoXG4gICAgICAvLyBObyBwcmltYXJ5IGJ1dHRvbiAoVXN1YWxseSB0aGUgbGVmdCBidXR0b24pXG4gICAgICBpc051bWJlcihidXR0b25zKSAmJiBidXR0b25zICE9PSAxIHx8IGlzTnVtYmVyKGJ1dHRvbikgJiYgYnV0dG9uICE9PSAwXG5cbiAgICAgIC8vIE9wZW4gY29udGV4dCBtZW51XG4gICAgICB8fCBldmVudC5jdHJsS2V5KSkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICB2YXIgb3B0aW9ucyA9IHRoaXMub3B0aW9ucyxcbiAgICAgICAgcG9pbnRlcnMgPSB0aGlzLnBvaW50ZXJzO1xuICAgICAgdmFyIGFjdGlvbjtcbiAgICAgIGlmIChldmVudC5jaGFuZ2VkVG91Y2hlcykge1xuICAgICAgICAvLyBIYW5kbGUgdG91Y2ggZXZlbnRcbiAgICAgICAgZm9yRWFjaChldmVudC5jaGFuZ2VkVG91Y2hlcywgZnVuY3Rpb24gKHRvdWNoKSB7XG4gICAgICAgICAgcG9pbnRlcnNbdG91Y2guaWRlbnRpZmllcl0gPSBnZXRQb2ludGVyKHRvdWNoKTtcbiAgICAgICAgfSk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICAvLyBIYW5kbGUgbW91c2UgZXZlbnQgYW5kIHBvaW50ZXIgZXZlbnRcbiAgICAgICAgcG9pbnRlcnNbZXZlbnQucG9pbnRlcklkIHx8IDBdID0gZ2V0UG9pbnRlcihldmVudCk7XG4gICAgICB9XG4gICAgICBpZiAoT2JqZWN0LmtleXMocG9pbnRlcnMpLmxlbmd0aCA+IDEgJiYgb3B0aW9ucy56b29tYWJsZSAmJiBvcHRpb25zLnpvb21PblRvdWNoKSB7XG4gICAgICAgIGFjdGlvbiA9IEFDVElPTl9aT09NO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgYWN0aW9uID0gZ2V0RGF0YShldmVudC50YXJnZXQsIERBVEFfQUNUSU9OKTtcbiAgICAgIH1cbiAgICAgIGlmICghUkVHRVhQX0FDVElPTlMudGVzdChhY3Rpb24pKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIGlmIChkaXNwYXRjaEV2ZW50KHRoaXMuZWxlbWVudCwgRVZFTlRfQ1JPUF9TVEFSVCwge1xuICAgICAgICBvcmlnaW5hbEV2ZW50OiBldmVudCxcbiAgICAgICAgYWN0aW9uOiBhY3Rpb25cbiAgICAgIH0pID09PSBmYWxzZSkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG5cbiAgICAgIC8vIFRoaXMgbGluZSBpcyByZXF1aXJlZCBmb3IgcHJldmVudGluZyBwYWdlIHpvb21pbmcgaW4gaU9TIGJyb3dzZXJzXG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgdGhpcy5hY3Rpb24gPSBhY3Rpb247XG4gICAgICB0aGlzLmNyb3BwaW5nID0gZmFsc2U7XG4gICAgICBpZiAoYWN0aW9uID09PSBBQ1RJT05fQ1JPUCkge1xuICAgICAgICB0aGlzLmNyb3BwaW5nID0gdHJ1ZTtcbiAgICAgICAgYWRkQ2xhc3ModGhpcy5kcmFnQm94LCBDTEFTU19NT0RBTCk7XG4gICAgICB9XG4gICAgfSxcbiAgICBjcm9wTW92ZTogZnVuY3Rpb24gY3JvcE1vdmUoZXZlbnQpIHtcbiAgICAgIHZhciBhY3Rpb24gPSB0aGlzLmFjdGlvbjtcbiAgICAgIGlmICh0aGlzLmRpc2FibGVkIHx8ICFhY3Rpb24pIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgICAgdmFyIHBvaW50ZXJzID0gdGhpcy5wb2ludGVycztcbiAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICBpZiAoZGlzcGF0Y2hFdmVudCh0aGlzLmVsZW1lbnQsIEVWRU5UX0NST1BfTU9WRSwge1xuICAgICAgICBvcmlnaW5hbEV2ZW50OiBldmVudCxcbiAgICAgICAgYWN0aW9uOiBhY3Rpb25cbiAgICAgIH0pID09PSBmYWxzZSkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICBpZiAoZXZlbnQuY2hhbmdlZFRvdWNoZXMpIHtcbiAgICAgICAgZm9yRWFjaChldmVudC5jaGFuZ2VkVG91Y2hlcywgZnVuY3Rpb24gKHRvdWNoKSB7XG4gICAgICAgICAgLy8gVGhlIGZpcnN0IHBhcmFtZXRlciBzaG91bGQgbm90IGJlIHVuZGVmaW5lZCAoIzQzMilcbiAgICAgICAgICBhc3NpZ24ocG9pbnRlcnNbdG91Y2guaWRlbnRpZmllcl0gfHwge30sIGdldFBvaW50ZXIodG91Y2gsIHRydWUpKTtcbiAgICAgICAgfSk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBhc3NpZ24ocG9pbnRlcnNbZXZlbnQucG9pbnRlcklkIHx8IDBdIHx8IHt9LCBnZXRQb2ludGVyKGV2ZW50LCB0cnVlKSk7XG4gICAgICB9XG4gICAgICB0aGlzLmNoYW5nZShldmVudCk7XG4gICAgfSxcbiAgICBjcm9wRW5kOiBmdW5jdGlvbiBjcm9wRW5kKGV2ZW50KSB7XG4gICAgICBpZiAodGhpcy5kaXNhYmxlZCkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICB2YXIgYWN0aW9uID0gdGhpcy5hY3Rpb24sXG4gICAgICAgIHBvaW50ZXJzID0gdGhpcy5wb2ludGVycztcbiAgICAgIGlmIChldmVudC5jaGFuZ2VkVG91Y2hlcykge1xuICAgICAgICBmb3JFYWNoKGV2ZW50LmNoYW5nZWRUb3VjaGVzLCBmdW5jdGlvbiAodG91Y2gpIHtcbiAgICAgICAgICBkZWxldGUgcG9pbnRlcnNbdG91Y2guaWRlbnRpZmllcl07XG4gICAgICAgIH0pO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgZGVsZXRlIHBvaW50ZXJzW2V2ZW50LnBvaW50ZXJJZCB8fCAwXTtcbiAgICAgIH1cbiAgICAgIGlmICghYWN0aW9uKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICBpZiAoIU9iamVjdC5rZXlzKHBvaW50ZXJzKS5sZW5ndGgpIHtcbiAgICAgICAgdGhpcy5hY3Rpb24gPSAnJztcbiAgICAgIH1cbiAgICAgIGlmICh0aGlzLmNyb3BwaW5nKSB7XG4gICAgICAgIHRoaXMuY3JvcHBpbmcgPSBmYWxzZTtcbiAgICAgICAgdG9nZ2xlQ2xhc3ModGhpcy5kcmFnQm94LCBDTEFTU19NT0RBTCwgdGhpcy5jcm9wcGVkICYmIHRoaXMub3B0aW9ucy5tb2RhbCk7XG4gICAgICB9XG4gICAgICBkaXNwYXRjaEV2ZW50KHRoaXMuZWxlbWVudCwgRVZFTlRfQ1JPUF9FTkQsIHtcbiAgICAgICAgb3JpZ2luYWxFdmVudDogZXZlbnQsXG4gICAgICAgIGFjdGlvbjogYWN0aW9uXG4gICAgICB9KTtcbiAgICB9XG4gIH07XG5cbiAgdmFyIGNoYW5nZSA9IHtcbiAgICBjaGFuZ2U6IGZ1bmN0aW9uIGNoYW5nZShldmVudCkge1xuICAgICAgdmFyIG9wdGlvbnMgPSB0aGlzLm9wdGlvbnMsXG4gICAgICAgIGNhbnZhc0RhdGEgPSB0aGlzLmNhbnZhc0RhdGEsXG4gICAgICAgIGNvbnRhaW5lckRhdGEgPSB0aGlzLmNvbnRhaW5lckRhdGEsXG4gICAgICAgIGNyb3BCb3hEYXRhID0gdGhpcy5jcm9wQm94RGF0YSxcbiAgICAgICAgcG9pbnRlcnMgPSB0aGlzLnBvaW50ZXJzO1xuICAgICAgdmFyIGFjdGlvbiA9IHRoaXMuYWN0aW9uO1xuICAgICAgdmFyIGFzcGVjdFJhdGlvID0gb3B0aW9ucy5hc3BlY3RSYXRpbztcbiAgICAgIHZhciBsZWZ0ID0gY3JvcEJveERhdGEubGVmdCxcbiAgICAgICAgdG9wID0gY3JvcEJveERhdGEudG9wLFxuICAgICAgICB3aWR0aCA9IGNyb3BCb3hEYXRhLndpZHRoLFxuICAgICAgICBoZWlnaHQgPSBjcm9wQm94RGF0YS5oZWlnaHQ7XG4gICAgICB2YXIgcmlnaHQgPSBsZWZ0ICsgd2lkdGg7XG4gICAgICB2YXIgYm90dG9tID0gdG9wICsgaGVpZ2h0O1xuICAgICAgdmFyIG1pbkxlZnQgPSAwO1xuICAgICAgdmFyIG1pblRvcCA9IDA7XG4gICAgICB2YXIgbWF4V2lkdGggPSBjb250YWluZXJEYXRhLndpZHRoO1xuICAgICAgdmFyIG1heEhlaWdodCA9IGNvbnRhaW5lckRhdGEuaGVpZ2h0O1xuICAgICAgdmFyIHJlbmRlcmFibGUgPSB0cnVlO1xuICAgICAgdmFyIG9mZnNldDtcblxuICAgICAgLy8gTG9ja2luZyBhc3BlY3QgcmF0aW8gaW4gXCJmcmVlIG1vZGVcIiBieSBob2xkaW5nIHNoaWZ0IGtleVxuICAgICAgaWYgKCFhc3BlY3RSYXRpbyAmJiBldmVudC5zaGlmdEtleSkge1xuICAgICAgICBhc3BlY3RSYXRpbyA9IHdpZHRoICYmIGhlaWdodCA/IHdpZHRoIC8gaGVpZ2h0IDogMTtcbiAgICAgIH1cbiAgICAgIGlmICh0aGlzLmxpbWl0ZWQpIHtcbiAgICAgICAgbWluTGVmdCA9IGNyb3BCb3hEYXRhLm1pbkxlZnQ7XG4gICAgICAgIG1pblRvcCA9IGNyb3BCb3hEYXRhLm1pblRvcDtcbiAgICAgICAgbWF4V2lkdGggPSBtaW5MZWZ0ICsgTWF0aC5taW4oY29udGFpbmVyRGF0YS53aWR0aCwgY2FudmFzRGF0YS53aWR0aCwgY2FudmFzRGF0YS5sZWZ0ICsgY2FudmFzRGF0YS53aWR0aCk7XG4gICAgICAgIG1heEhlaWdodCA9IG1pblRvcCArIE1hdGgubWluKGNvbnRhaW5lckRhdGEuaGVpZ2h0LCBjYW52YXNEYXRhLmhlaWdodCwgY2FudmFzRGF0YS50b3AgKyBjYW52YXNEYXRhLmhlaWdodCk7XG4gICAgICB9XG4gICAgICB2YXIgcG9pbnRlciA9IHBvaW50ZXJzW09iamVjdC5rZXlzKHBvaW50ZXJzKVswXV07XG4gICAgICB2YXIgcmFuZ2UgPSB7XG4gICAgICAgIHg6IHBvaW50ZXIuZW5kWCAtIHBvaW50ZXIuc3RhcnRYLFxuICAgICAgICB5OiBwb2ludGVyLmVuZFkgLSBwb2ludGVyLnN0YXJ0WVxuICAgICAgfTtcbiAgICAgIHZhciBjaGVjayA9IGZ1bmN0aW9uIGNoZWNrKHNpZGUpIHtcbiAgICAgICAgc3dpdGNoIChzaWRlKSB7XG4gICAgICAgICAgY2FzZSBBQ1RJT05fRUFTVDpcbiAgICAgICAgICAgIGlmIChyaWdodCArIHJhbmdlLnggPiBtYXhXaWR0aCkge1xuICAgICAgICAgICAgICByYW5nZS54ID0gbWF4V2lkdGggLSByaWdodDtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgIGNhc2UgQUNUSU9OX1dFU1Q6XG4gICAgICAgICAgICBpZiAobGVmdCArIHJhbmdlLnggPCBtaW5MZWZ0KSB7XG4gICAgICAgICAgICAgIHJhbmdlLnggPSBtaW5MZWZ0IC0gbGVmdDtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgIGNhc2UgQUNUSU9OX05PUlRIOlxuICAgICAgICAgICAgaWYgKHRvcCArIHJhbmdlLnkgPCBtaW5Ub3ApIHtcbiAgICAgICAgICAgICAgcmFuZ2UueSA9IG1pblRvcCAtIHRvcDtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgIGNhc2UgQUNUSU9OX1NPVVRIOlxuICAgICAgICAgICAgaWYgKGJvdHRvbSArIHJhbmdlLnkgPiBtYXhIZWlnaHQpIHtcbiAgICAgICAgICAgICAgcmFuZ2UueSA9IG1heEhlaWdodCAtIGJvdHRvbTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICB9XG4gICAgICB9O1xuICAgICAgc3dpdGNoIChhY3Rpb24pIHtcbiAgICAgICAgLy8gTW92ZSBjcm9wIGJveFxuICAgICAgICBjYXNlIEFDVElPTl9BTEw6XG4gICAgICAgICAgbGVmdCArPSByYW5nZS54O1xuICAgICAgICAgIHRvcCArPSByYW5nZS55O1xuICAgICAgICAgIGJyZWFrO1xuXG4gICAgICAgIC8vIFJlc2l6ZSBjcm9wIGJveFxuICAgICAgICBjYXNlIEFDVElPTl9FQVNUOlxuICAgICAgICAgIGlmIChyYW5nZS54ID49IDAgJiYgKHJpZ2h0ID49IG1heFdpZHRoIHx8IGFzcGVjdFJhdGlvICYmICh0b3AgPD0gbWluVG9wIHx8IGJvdHRvbSA+PSBtYXhIZWlnaHQpKSkge1xuICAgICAgICAgICAgcmVuZGVyYWJsZSA9IGZhbHNlO1xuICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgfVxuICAgICAgICAgIGNoZWNrKEFDVElPTl9FQVNUKTtcbiAgICAgICAgICB3aWR0aCArPSByYW5nZS54O1xuICAgICAgICAgIGlmICh3aWR0aCA8IDApIHtcbiAgICAgICAgICAgIGFjdGlvbiA9IEFDVElPTl9XRVNUO1xuICAgICAgICAgICAgd2lkdGggPSAtd2lkdGg7XG4gICAgICAgICAgICBsZWZ0IC09IHdpZHRoO1xuICAgICAgICAgIH1cbiAgICAgICAgICBpZiAoYXNwZWN0UmF0aW8pIHtcbiAgICAgICAgICAgIGhlaWdodCA9IHdpZHRoIC8gYXNwZWN0UmF0aW87XG4gICAgICAgICAgICB0b3AgKz0gKGNyb3BCb3hEYXRhLmhlaWdodCAtIGhlaWdodCkgLyAyO1xuICAgICAgICAgIH1cbiAgICAgICAgICBicmVhaztcbiAgICAgICAgY2FzZSBBQ1RJT05fTk9SVEg6XG4gICAgICAgICAgaWYgKHJhbmdlLnkgPD0gMCAmJiAodG9wIDw9IG1pblRvcCB8fCBhc3BlY3RSYXRpbyAmJiAobGVmdCA8PSBtaW5MZWZ0IHx8IHJpZ2h0ID49IG1heFdpZHRoKSkpIHtcbiAgICAgICAgICAgIHJlbmRlcmFibGUgPSBmYWxzZTtcbiAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgIH1cbiAgICAgICAgICBjaGVjayhBQ1RJT05fTk9SVEgpO1xuICAgICAgICAgIGhlaWdodCAtPSByYW5nZS55O1xuICAgICAgICAgIHRvcCArPSByYW5nZS55O1xuICAgICAgICAgIGlmIChoZWlnaHQgPCAwKSB7XG4gICAgICAgICAgICBhY3Rpb24gPSBBQ1RJT05fU09VVEg7XG4gICAgICAgICAgICBoZWlnaHQgPSAtaGVpZ2h0O1xuICAgICAgICAgICAgdG9wIC09IGhlaWdodDtcbiAgICAgICAgICB9XG4gICAgICAgICAgaWYgKGFzcGVjdFJhdGlvKSB7XG4gICAgICAgICAgICB3aWR0aCA9IGhlaWdodCAqIGFzcGVjdFJhdGlvO1xuICAgICAgICAgICAgbGVmdCArPSAoY3JvcEJveERhdGEud2lkdGggLSB3aWR0aCkgLyAyO1xuICAgICAgICAgIH1cbiAgICAgICAgICBicmVhaztcbiAgICAgICAgY2FzZSBBQ1RJT05fV0VTVDpcbiAgICAgICAgICBpZiAocmFuZ2UueCA8PSAwICYmIChsZWZ0IDw9IG1pbkxlZnQgfHwgYXNwZWN0UmF0aW8gJiYgKHRvcCA8PSBtaW5Ub3AgfHwgYm90dG9tID49IG1heEhlaWdodCkpKSB7XG4gICAgICAgICAgICByZW5kZXJhYmxlID0gZmFsc2U7XG4gICAgICAgICAgICBicmVhaztcbiAgICAgICAgICB9XG4gICAgICAgICAgY2hlY2soQUNUSU9OX1dFU1QpO1xuICAgICAgICAgIHdpZHRoIC09IHJhbmdlLng7XG4gICAgICAgICAgbGVmdCArPSByYW5nZS54O1xuICAgICAgICAgIGlmICh3aWR0aCA8IDApIHtcbiAgICAgICAgICAgIGFjdGlvbiA9IEFDVElPTl9FQVNUO1xuICAgICAgICAgICAgd2lkdGggPSAtd2lkdGg7XG4gICAgICAgICAgICBsZWZ0IC09IHdpZHRoO1xuICAgICAgICAgIH1cbiAgICAgICAgICBpZiAoYXNwZWN0UmF0aW8pIHtcbiAgICAgICAgICAgIGhlaWdodCA9IHdpZHRoIC8gYXNwZWN0UmF0aW87XG4gICAgICAgICAgICB0b3AgKz0gKGNyb3BCb3hEYXRhLmhlaWdodCAtIGhlaWdodCkgLyAyO1xuICAgICAgICAgIH1cbiAgICAgICAgICBicmVhaztcbiAgICAgICAgY2FzZSBBQ1RJT05fU09VVEg6XG4gICAgICAgICAgaWYgKHJhbmdlLnkgPj0gMCAmJiAoYm90dG9tID49IG1heEhlaWdodCB8fCBhc3BlY3RSYXRpbyAmJiAobGVmdCA8PSBtaW5MZWZ0IHx8IHJpZ2h0ID49IG1heFdpZHRoKSkpIHtcbiAgICAgICAgICAgIHJlbmRlcmFibGUgPSBmYWxzZTtcbiAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgIH1cbiAgICAgICAgICBjaGVjayhBQ1RJT05fU09VVEgpO1xuICAgICAgICAgIGhlaWdodCArPSByYW5nZS55O1xuICAgICAgICAgIGlmIChoZWlnaHQgPCAwKSB7XG4gICAgICAgICAgICBhY3Rpb24gPSBBQ1RJT05fTk9SVEg7XG4gICAgICAgICAgICBoZWlnaHQgPSAtaGVpZ2h0O1xuICAgICAgICAgICAgdG9wIC09IGhlaWdodDtcbiAgICAgICAgICB9XG4gICAgICAgICAgaWYgKGFzcGVjdFJhdGlvKSB7XG4gICAgICAgICAgICB3aWR0aCA9IGhlaWdodCAqIGFzcGVjdFJhdGlvO1xuICAgICAgICAgICAgbGVmdCArPSAoY3JvcEJveERhdGEud2lkdGggLSB3aWR0aCkgLyAyO1xuICAgICAgICAgIH1cbiAgICAgICAgICBicmVhaztcbiAgICAgICAgY2FzZSBBQ1RJT05fTk9SVEhfRUFTVDpcbiAgICAgICAgICBpZiAoYXNwZWN0UmF0aW8pIHtcbiAgICAgICAgICAgIGlmIChyYW5nZS55IDw9IDAgJiYgKHRvcCA8PSBtaW5Ub3AgfHwgcmlnaHQgPj0gbWF4V2lkdGgpKSB7XG4gICAgICAgICAgICAgIHJlbmRlcmFibGUgPSBmYWxzZTtcbiAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBjaGVjayhBQ1RJT05fTk9SVEgpO1xuICAgICAgICAgICAgaGVpZ2h0IC09IHJhbmdlLnk7XG4gICAgICAgICAgICB0b3AgKz0gcmFuZ2UueTtcbiAgICAgICAgICAgIHdpZHRoID0gaGVpZ2h0ICogYXNwZWN0UmF0aW87XG4gICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIGNoZWNrKEFDVElPTl9OT1JUSCk7XG4gICAgICAgICAgICBjaGVjayhBQ1RJT05fRUFTVCk7XG4gICAgICAgICAgICBpZiAocmFuZ2UueCA+PSAwKSB7XG4gICAgICAgICAgICAgIGlmIChyaWdodCA8IG1heFdpZHRoKSB7XG4gICAgICAgICAgICAgICAgd2lkdGggKz0gcmFuZ2UueDtcbiAgICAgICAgICAgICAgfSBlbHNlIGlmIChyYW5nZS55IDw9IDAgJiYgdG9wIDw9IG1pblRvcCkge1xuICAgICAgICAgICAgICAgIHJlbmRlcmFibGUgPSBmYWxzZTtcbiAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgd2lkdGggKz0gcmFuZ2UueDtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGlmIChyYW5nZS55IDw9IDApIHtcbiAgICAgICAgICAgICAgaWYgKHRvcCA+IG1pblRvcCkge1xuICAgICAgICAgICAgICAgIGhlaWdodCAtPSByYW5nZS55O1xuICAgICAgICAgICAgICAgIHRvcCArPSByYW5nZS55O1xuICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICBoZWlnaHQgLT0gcmFuZ2UueTtcbiAgICAgICAgICAgICAgdG9wICs9IHJhbmdlLnk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgfVxuICAgICAgICAgIGlmICh3aWR0aCA8IDAgJiYgaGVpZ2h0IDwgMCkge1xuICAgICAgICAgICAgYWN0aW9uID0gQUNUSU9OX1NPVVRIX1dFU1Q7XG4gICAgICAgICAgICBoZWlnaHQgPSAtaGVpZ2h0O1xuICAgICAgICAgICAgd2lkdGggPSAtd2lkdGg7XG4gICAgICAgICAgICB0b3AgLT0gaGVpZ2h0O1xuICAgICAgICAgICAgbGVmdCAtPSB3aWR0aDtcbiAgICAgICAgICB9IGVsc2UgaWYgKHdpZHRoIDwgMCkge1xuICAgICAgICAgICAgYWN0aW9uID0gQUNUSU9OX05PUlRIX1dFU1Q7XG4gICAgICAgICAgICB3aWR0aCA9IC13aWR0aDtcbiAgICAgICAgICAgIGxlZnQgLT0gd2lkdGg7XG4gICAgICAgICAgfSBlbHNlIGlmIChoZWlnaHQgPCAwKSB7XG4gICAgICAgICAgICBhY3Rpb24gPSBBQ1RJT05fU09VVEhfRUFTVDtcbiAgICAgICAgICAgIGhlaWdodCA9IC1oZWlnaHQ7XG4gICAgICAgICAgICB0b3AgLT0gaGVpZ2h0O1xuICAgICAgICAgIH1cbiAgICAgICAgICBicmVhaztcbiAgICAgICAgY2FzZSBBQ1RJT05fTk9SVEhfV0VTVDpcbiAgICAgICAgICBpZiAoYXNwZWN0UmF0aW8pIHtcbiAgICAgICAgICAgIGlmIChyYW5nZS55IDw9IDAgJiYgKHRvcCA8PSBtaW5Ub3AgfHwgbGVmdCA8PSBtaW5MZWZ0KSkge1xuICAgICAgICAgICAgICByZW5kZXJhYmxlID0gZmFsc2U7XG4gICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgY2hlY2soQUNUSU9OX05PUlRIKTtcbiAgICAgICAgICAgIGhlaWdodCAtPSByYW5nZS55O1xuICAgICAgICAgICAgdG9wICs9IHJhbmdlLnk7XG4gICAgICAgICAgICB3aWR0aCA9IGhlaWdodCAqIGFzcGVjdFJhdGlvO1xuICAgICAgICAgICAgbGVmdCArPSBjcm9wQm94RGF0YS53aWR0aCAtIHdpZHRoO1xuICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICBjaGVjayhBQ1RJT05fTk9SVEgpO1xuICAgICAgICAgICAgY2hlY2soQUNUSU9OX1dFU1QpO1xuICAgICAgICAgICAgaWYgKHJhbmdlLnggPD0gMCkge1xuICAgICAgICAgICAgICBpZiAobGVmdCA+IG1pbkxlZnQpIHtcbiAgICAgICAgICAgICAgICB3aWR0aCAtPSByYW5nZS54O1xuICAgICAgICAgICAgICAgIGxlZnQgKz0gcmFuZ2UueDtcbiAgICAgICAgICAgICAgfSBlbHNlIGlmIChyYW5nZS55IDw9IDAgJiYgdG9wIDw9IG1pblRvcCkge1xuICAgICAgICAgICAgICAgIHJlbmRlcmFibGUgPSBmYWxzZTtcbiAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgd2lkdGggLT0gcmFuZ2UueDtcbiAgICAgICAgICAgICAgbGVmdCArPSByYW5nZS54O1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgaWYgKHJhbmdlLnkgPD0gMCkge1xuICAgICAgICAgICAgICBpZiAodG9wID4gbWluVG9wKSB7XG4gICAgICAgICAgICAgICAgaGVpZ2h0IC09IHJhbmdlLnk7XG4gICAgICAgICAgICAgICAgdG9wICs9IHJhbmdlLnk7XG4gICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgIGhlaWdodCAtPSByYW5nZS55O1xuICAgICAgICAgICAgICB0b3AgKz0gcmFuZ2UueTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICB9XG4gICAgICAgICAgaWYgKHdpZHRoIDwgMCAmJiBoZWlnaHQgPCAwKSB7XG4gICAgICAgICAgICBhY3Rpb24gPSBBQ1RJT05fU09VVEhfRUFTVDtcbiAgICAgICAgICAgIGhlaWdodCA9IC1oZWlnaHQ7XG4gICAgICAgICAgICB3aWR0aCA9IC13aWR0aDtcbiAgICAgICAgICAgIHRvcCAtPSBoZWlnaHQ7XG4gICAgICAgICAgICBsZWZ0IC09IHdpZHRoO1xuICAgICAgICAgIH0gZWxzZSBpZiAod2lkdGggPCAwKSB7XG4gICAgICAgICAgICBhY3Rpb24gPSBBQ1RJT05fTk9SVEhfRUFTVDtcbiAgICAgICAgICAgIHdpZHRoID0gLXdpZHRoO1xuICAgICAgICAgICAgbGVmdCAtPSB3aWR0aDtcbiAgICAgICAgICB9IGVsc2UgaWYgKGhlaWdodCA8IDApIHtcbiAgICAgICAgICAgIGFjdGlvbiA9IEFDVElPTl9TT1VUSF9XRVNUO1xuICAgICAgICAgICAgaGVpZ2h0ID0gLWhlaWdodDtcbiAgICAgICAgICAgIHRvcCAtPSBoZWlnaHQ7XG4gICAgICAgICAgfVxuICAgICAgICAgIGJyZWFrO1xuICAgICAgICBjYXNlIEFDVElPTl9TT1VUSF9XRVNUOlxuICAgICAgICAgIGlmIChhc3BlY3RSYXRpbykge1xuICAgICAgICAgICAgaWYgKHJhbmdlLnggPD0gMCAmJiAobGVmdCA8PSBtaW5MZWZ0IHx8IGJvdHRvbSA+PSBtYXhIZWlnaHQpKSB7XG4gICAgICAgICAgICAgIHJlbmRlcmFibGUgPSBmYWxzZTtcbiAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBjaGVjayhBQ1RJT05fV0VTVCk7XG4gICAgICAgICAgICB3aWR0aCAtPSByYW5nZS54O1xuICAgICAgICAgICAgbGVmdCArPSByYW5nZS54O1xuICAgICAgICAgICAgaGVpZ2h0ID0gd2lkdGggLyBhc3BlY3RSYXRpbztcbiAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgY2hlY2soQUNUSU9OX1NPVVRIKTtcbiAgICAgICAgICAgIGNoZWNrKEFDVElPTl9XRVNUKTtcbiAgICAgICAgICAgIGlmIChyYW5nZS54IDw9IDApIHtcbiAgICAgICAgICAgICAgaWYgKGxlZnQgPiBtaW5MZWZ0KSB7XG4gICAgICAgICAgICAgICAgd2lkdGggLT0gcmFuZ2UueDtcbiAgICAgICAgICAgICAgICBsZWZ0ICs9IHJhbmdlLng7XG4gICAgICAgICAgICAgIH0gZWxzZSBpZiAocmFuZ2UueSA+PSAwICYmIGJvdHRvbSA+PSBtYXhIZWlnaHQpIHtcbiAgICAgICAgICAgICAgICByZW5kZXJhYmxlID0gZmFsc2U7XG4gICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgIHdpZHRoIC09IHJhbmdlLng7XG4gICAgICAgICAgICAgIGxlZnQgKz0gcmFuZ2UueDtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGlmIChyYW5nZS55ID49IDApIHtcbiAgICAgICAgICAgICAgaWYgKGJvdHRvbSA8IG1heEhlaWdodCkge1xuICAgICAgICAgICAgICAgIGhlaWdodCArPSByYW5nZS55O1xuICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICBoZWlnaHQgKz0gcmFuZ2UueTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICB9XG4gICAgICAgICAgaWYgKHdpZHRoIDwgMCAmJiBoZWlnaHQgPCAwKSB7XG4gICAgICAgICAgICBhY3Rpb24gPSBBQ1RJT05fTk9SVEhfRUFTVDtcbiAgICAgICAgICAgIGhlaWdodCA9IC1oZWlnaHQ7XG4gICAgICAgICAgICB3aWR0aCA9IC13aWR0aDtcbiAgICAgICAgICAgIHRvcCAtPSBoZWlnaHQ7XG4gICAgICAgICAgICBsZWZ0IC09IHdpZHRoO1xuICAgICAgICAgIH0gZWxzZSBpZiAod2lkdGggPCAwKSB7XG4gICAgICAgICAgICBhY3Rpb24gPSBBQ1RJT05fU09VVEhfRUFTVDtcbiAgICAgICAgICAgIHdpZHRoID0gLXdpZHRoO1xuICAgICAgICAgICAgbGVmdCAtPSB3aWR0aDtcbiAgICAgICAgICB9IGVsc2UgaWYgKGhlaWdodCA8IDApIHtcbiAgICAgICAgICAgIGFjdGlvbiA9IEFDVElPTl9OT1JUSF9XRVNUO1xuICAgICAgICAgICAgaGVpZ2h0ID0gLWhlaWdodDtcbiAgICAgICAgICAgIHRvcCAtPSBoZWlnaHQ7XG4gICAgICAgICAgfVxuICAgICAgICAgIGJyZWFrO1xuICAgICAgICBjYXNlIEFDVElPTl9TT1VUSF9FQVNUOlxuICAgICAgICAgIGlmIChhc3BlY3RSYXRpbykge1xuICAgICAgICAgICAgaWYgKHJhbmdlLnggPj0gMCAmJiAocmlnaHQgPj0gbWF4V2lkdGggfHwgYm90dG9tID49IG1heEhlaWdodCkpIHtcbiAgICAgICAgICAgICAgcmVuZGVyYWJsZSA9IGZhbHNlO1xuICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGNoZWNrKEFDVElPTl9FQVNUKTtcbiAgICAgICAgICAgIHdpZHRoICs9IHJhbmdlLng7XG4gICAgICAgICAgICBoZWlnaHQgPSB3aWR0aCAvIGFzcGVjdFJhdGlvO1xuICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICBjaGVjayhBQ1RJT05fU09VVEgpO1xuICAgICAgICAgICAgY2hlY2soQUNUSU9OX0VBU1QpO1xuICAgICAgICAgICAgaWYgKHJhbmdlLnggPj0gMCkge1xuICAgICAgICAgICAgICBpZiAocmlnaHQgPCBtYXhXaWR0aCkge1xuICAgICAgICAgICAgICAgIHdpZHRoICs9IHJhbmdlLng7XG4gICAgICAgICAgICAgIH0gZWxzZSBpZiAocmFuZ2UueSA+PSAwICYmIGJvdHRvbSA+PSBtYXhIZWlnaHQpIHtcbiAgICAgICAgICAgICAgICByZW5kZXJhYmxlID0gZmFsc2U7XG4gICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgIHdpZHRoICs9IHJhbmdlLng7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBpZiAocmFuZ2UueSA+PSAwKSB7XG4gICAgICAgICAgICAgIGlmIChib3R0b20gPCBtYXhIZWlnaHQpIHtcbiAgICAgICAgICAgICAgICBoZWlnaHQgKz0gcmFuZ2UueTtcbiAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgaGVpZ2h0ICs9IHJhbmdlLnk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgfVxuICAgICAgICAgIGlmICh3aWR0aCA8IDAgJiYgaGVpZ2h0IDwgMCkge1xuICAgICAgICAgICAgYWN0aW9uID0gQUNUSU9OX05PUlRIX1dFU1Q7XG4gICAgICAgICAgICBoZWlnaHQgPSAtaGVpZ2h0O1xuICAgICAgICAgICAgd2lkdGggPSAtd2lkdGg7XG4gICAgICAgICAgICB0b3AgLT0gaGVpZ2h0O1xuICAgICAgICAgICAgbGVmdCAtPSB3aWR0aDtcbiAgICAgICAgICB9IGVsc2UgaWYgKHdpZHRoIDwgMCkge1xuICAgICAgICAgICAgYWN0aW9uID0gQUNUSU9OX1NPVVRIX1dFU1Q7XG4gICAgICAgICAgICB3aWR0aCA9IC13aWR0aDtcbiAgICAgICAgICAgIGxlZnQgLT0gd2lkdGg7XG4gICAgICAgICAgfSBlbHNlIGlmIChoZWlnaHQgPCAwKSB7XG4gICAgICAgICAgICBhY3Rpb24gPSBBQ1RJT05fTk9SVEhfRUFTVDtcbiAgICAgICAgICAgIGhlaWdodCA9IC1oZWlnaHQ7XG4gICAgICAgICAgICB0b3AgLT0gaGVpZ2h0O1xuICAgICAgICAgIH1cbiAgICAgICAgICBicmVhaztcblxuICAgICAgICAvLyBNb3ZlIGNhbnZhc1xuICAgICAgICBjYXNlIEFDVElPTl9NT1ZFOlxuICAgICAgICAgIHRoaXMubW92ZShyYW5nZS54LCByYW5nZS55KTtcbiAgICAgICAgICByZW5kZXJhYmxlID0gZmFsc2U7XG4gICAgICAgICAgYnJlYWs7XG5cbiAgICAgICAgLy8gWm9vbSBjYW52YXNcbiAgICAgICAgY2FzZSBBQ1RJT05fWk9PTTpcbiAgICAgICAgICB0aGlzLnpvb20oZ2V0TWF4Wm9vbVJhdGlvKHBvaW50ZXJzKSwgZXZlbnQpO1xuICAgICAgICAgIHJlbmRlcmFibGUgPSBmYWxzZTtcbiAgICAgICAgICBicmVhaztcblxuICAgICAgICAvLyBDcmVhdGUgY3JvcCBib3hcbiAgICAgICAgY2FzZSBBQ1RJT05fQ1JPUDpcbiAgICAgICAgICBpZiAoIXJhbmdlLnggfHwgIXJhbmdlLnkpIHtcbiAgICAgICAgICAgIHJlbmRlcmFibGUgPSBmYWxzZTtcbiAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgIH1cbiAgICAgICAgICBvZmZzZXQgPSBnZXRPZmZzZXQodGhpcy5jcm9wcGVyKTtcbiAgICAgICAgICBsZWZ0ID0gcG9pbnRlci5zdGFydFggLSBvZmZzZXQubGVmdDtcbiAgICAgICAgICB0b3AgPSBwb2ludGVyLnN0YXJ0WSAtIG9mZnNldC50b3A7XG4gICAgICAgICAgd2lkdGggPSBjcm9wQm94RGF0YS5taW5XaWR0aDtcbiAgICAgICAgICBoZWlnaHQgPSBjcm9wQm94RGF0YS5taW5IZWlnaHQ7XG4gICAgICAgICAgaWYgKHJhbmdlLnggPiAwKSB7XG4gICAgICAgICAgICBhY3Rpb24gPSByYW5nZS55ID4gMCA/IEFDVElPTl9TT1VUSF9FQVNUIDogQUNUSU9OX05PUlRIX0VBU1Q7XG4gICAgICAgICAgfSBlbHNlIGlmIChyYW5nZS54IDwgMCkge1xuICAgICAgICAgICAgbGVmdCAtPSB3aWR0aDtcbiAgICAgICAgICAgIGFjdGlvbiA9IHJhbmdlLnkgPiAwID8gQUNUSU9OX1NPVVRIX1dFU1QgOiBBQ1RJT05fTk9SVEhfV0VTVDtcbiAgICAgICAgICB9XG4gICAgICAgICAgaWYgKHJhbmdlLnkgPCAwKSB7XG4gICAgICAgICAgICB0b3AgLT0gaGVpZ2h0O1xuICAgICAgICAgIH1cblxuICAgICAgICAgIC8vIFNob3cgdGhlIGNyb3AgYm94IGlmIGlzIGhpZGRlblxuICAgICAgICAgIGlmICghdGhpcy5jcm9wcGVkKSB7XG4gICAgICAgICAgICByZW1vdmVDbGFzcyh0aGlzLmNyb3BCb3gsIENMQVNTX0hJRERFTik7XG4gICAgICAgICAgICB0aGlzLmNyb3BwZWQgPSB0cnVlO1xuICAgICAgICAgICAgaWYgKHRoaXMubGltaXRlZCkge1xuICAgICAgICAgICAgICB0aGlzLmxpbWl0Q3JvcEJveCh0cnVlLCB0cnVlKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICB9XG4gICAgICAgICAgYnJlYWs7XG4gICAgICB9XG4gICAgICBpZiAocmVuZGVyYWJsZSkge1xuICAgICAgICBjcm9wQm94RGF0YS53aWR0aCA9IHdpZHRoO1xuICAgICAgICBjcm9wQm94RGF0YS5oZWlnaHQgPSBoZWlnaHQ7XG4gICAgICAgIGNyb3BCb3hEYXRhLmxlZnQgPSBsZWZ0O1xuICAgICAgICBjcm9wQm94RGF0YS50b3AgPSB0b3A7XG4gICAgICAgIHRoaXMuYWN0aW9uID0gYWN0aW9uO1xuICAgICAgICB0aGlzLnJlbmRlckNyb3BCb3goKTtcbiAgICAgIH1cblxuICAgICAgLy8gT3ZlcnJpZGVcbiAgICAgIGZvckVhY2gocG9pbnRlcnMsIGZ1bmN0aW9uIChwKSB7XG4gICAgICAgIHAuc3RhcnRYID0gcC5lbmRYO1xuICAgICAgICBwLnN0YXJ0WSA9IHAuZW5kWTtcbiAgICAgIH0pO1xuICAgIH1cbiAgfTtcblxuICB2YXIgbWV0aG9kcyA9IHtcbiAgICAvLyBTaG93IHRoZSBjcm9wIGJveCBtYW51YWxseVxuICAgIGNyb3A6IGZ1bmN0aW9uIGNyb3AoKSB7XG4gICAgICBpZiAodGhpcy5yZWFkeSAmJiAhdGhpcy5jcm9wcGVkICYmICF0aGlzLmRpc2FibGVkKSB7XG4gICAgICAgIHRoaXMuY3JvcHBlZCA9IHRydWU7XG4gICAgICAgIHRoaXMubGltaXRDcm9wQm94KHRydWUsIHRydWUpO1xuICAgICAgICBpZiAodGhpcy5vcHRpb25zLm1vZGFsKSB7XG4gICAgICAgICAgYWRkQ2xhc3ModGhpcy5kcmFnQm94LCBDTEFTU19NT0RBTCk7XG4gICAgICAgIH1cbiAgICAgICAgcmVtb3ZlQ2xhc3ModGhpcy5jcm9wQm94LCBDTEFTU19ISURERU4pO1xuICAgICAgICB0aGlzLnNldENyb3BCb3hEYXRhKHRoaXMuaW5pdGlhbENyb3BCb3hEYXRhKTtcbiAgICAgIH1cbiAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG4gICAgLy8gUmVzZXQgdGhlIGltYWdlIGFuZCBjcm9wIGJveCB0byB0aGVpciBpbml0aWFsIHN0YXRlc1xuICAgIHJlc2V0OiBmdW5jdGlvbiByZXNldCgpIHtcbiAgICAgIGlmICh0aGlzLnJlYWR5ICYmICF0aGlzLmRpc2FibGVkKSB7XG4gICAgICAgIHRoaXMuaW1hZ2VEYXRhID0gYXNzaWduKHt9LCB0aGlzLmluaXRpYWxJbWFnZURhdGEpO1xuICAgICAgICB0aGlzLmNhbnZhc0RhdGEgPSBhc3NpZ24oe30sIHRoaXMuaW5pdGlhbENhbnZhc0RhdGEpO1xuICAgICAgICB0aGlzLmNyb3BCb3hEYXRhID0gYXNzaWduKHt9LCB0aGlzLmluaXRpYWxDcm9wQm94RGF0YSk7XG4gICAgICAgIHRoaXMucmVuZGVyQ2FudmFzKCk7XG4gICAgICAgIGlmICh0aGlzLmNyb3BwZWQpIHtcbiAgICAgICAgICB0aGlzLnJlbmRlckNyb3BCb3goKTtcbiAgICAgICAgfVxuICAgICAgfVxuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcbiAgICAvLyBDbGVhciB0aGUgY3JvcCBib3hcbiAgICBjbGVhcjogZnVuY3Rpb24gY2xlYXIoKSB7XG4gICAgICBpZiAodGhpcy5jcm9wcGVkICYmICF0aGlzLmRpc2FibGVkKSB7XG4gICAgICAgIGFzc2lnbih0aGlzLmNyb3BCb3hEYXRhLCB7XG4gICAgICAgICAgbGVmdDogMCxcbiAgICAgICAgICB0b3A6IDAsXG4gICAgICAgICAgd2lkdGg6IDAsXG4gICAgICAgICAgaGVpZ2h0OiAwXG4gICAgICAgIH0pO1xuICAgICAgICB0aGlzLmNyb3BwZWQgPSBmYWxzZTtcbiAgICAgICAgdGhpcy5yZW5kZXJDcm9wQm94KCk7XG4gICAgICAgIHRoaXMubGltaXRDYW52YXModHJ1ZSwgdHJ1ZSk7XG5cbiAgICAgICAgLy8gUmVuZGVyIGNhbnZhcyBhZnRlciBjcm9wIGJveCByZW5kZXJlZFxuICAgICAgICB0aGlzLnJlbmRlckNhbnZhcygpO1xuICAgICAgICByZW1vdmVDbGFzcyh0aGlzLmRyYWdCb3gsIENMQVNTX01PREFMKTtcbiAgICAgICAgYWRkQ2xhc3ModGhpcy5jcm9wQm94LCBDTEFTU19ISURERU4pO1xuICAgICAgfVxuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcbiAgICAvKipcbiAgICAgKiBSZXBsYWNlIHRoZSBpbWFnZSdzIHNyYyBhbmQgcmVidWlsZCB0aGUgY3JvcHBlclxuICAgICAqIEBwYXJhbSB7c3RyaW5nfSB1cmwgLSBUaGUgbmV3IFVSTC5cbiAgICAgKiBAcGFyYW0ge2Jvb2xlYW59IFtoYXNTYW1lU2l6ZV0gLSBJbmRpY2F0ZSBpZiB0aGUgbmV3IGltYWdlIGhhcyB0aGUgc2FtZSBzaXplIGFzIHRoZSBvbGQgb25lLlxuICAgICAqIEByZXR1cm5zIHtDcm9wcGVyfSB0aGlzXG4gICAgICovXG4gICAgcmVwbGFjZTogZnVuY3Rpb24gcmVwbGFjZSh1cmwpIHtcbiAgICAgIHZhciBoYXNTYW1lU2l6ZSA9IGFyZ3VtZW50cy5sZW5ndGggPiAxICYmIGFyZ3VtZW50c1sxXSAhPT0gdW5kZWZpbmVkID8gYXJndW1lbnRzWzFdIDogZmFsc2U7XG4gICAgICBpZiAoIXRoaXMuZGlzYWJsZWQgJiYgdXJsKSB7XG4gICAgICAgIGlmICh0aGlzLmlzSW1nKSB7XG4gICAgICAgICAgdGhpcy5lbGVtZW50LnNyYyA9IHVybDtcbiAgICAgICAgfVxuICAgICAgICBpZiAoaGFzU2FtZVNpemUpIHtcbiAgICAgICAgICB0aGlzLnVybCA9IHVybDtcbiAgICAgICAgICB0aGlzLmltYWdlLnNyYyA9IHVybDtcbiAgICAgICAgICBpZiAodGhpcy5yZWFkeSkge1xuICAgICAgICAgICAgdGhpcy52aWV3Qm94SW1hZ2Uuc3JjID0gdXJsO1xuICAgICAgICAgICAgZm9yRWFjaCh0aGlzLnByZXZpZXdzLCBmdW5jdGlvbiAoZWxlbWVudCkge1xuICAgICAgICAgICAgICBlbGVtZW50LmdldEVsZW1lbnRzQnlUYWdOYW1lKCdpbWcnKVswXS5zcmMgPSB1cmw7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICB9XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgaWYgKHRoaXMuaXNJbWcpIHtcbiAgICAgICAgICAgIHRoaXMucmVwbGFjZWQgPSB0cnVlO1xuICAgICAgICAgIH1cbiAgICAgICAgICB0aGlzLm9wdGlvbnMuZGF0YSA9IG51bGw7XG4gICAgICAgICAgdGhpcy51bmNyZWF0ZSgpO1xuICAgICAgICAgIHRoaXMubG9hZCh1cmwpO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxuICAgIC8vIEVuYWJsZSAodW5mcmVlemUpIHRoZSBjcm9wcGVyXG4gICAgZW5hYmxlOiBmdW5jdGlvbiBlbmFibGUoKSB7XG4gICAgICBpZiAodGhpcy5yZWFkeSAmJiB0aGlzLmRpc2FibGVkKSB7XG4gICAgICAgIHRoaXMuZGlzYWJsZWQgPSBmYWxzZTtcbiAgICAgICAgcmVtb3ZlQ2xhc3ModGhpcy5jcm9wcGVyLCBDTEFTU19ESVNBQkxFRCk7XG4gICAgICB9XG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxuICAgIC8vIERpc2FibGUgKGZyZWV6ZSkgdGhlIGNyb3BwZXJcbiAgICBkaXNhYmxlOiBmdW5jdGlvbiBkaXNhYmxlKCkge1xuICAgICAgaWYgKHRoaXMucmVhZHkgJiYgIXRoaXMuZGlzYWJsZWQpIHtcbiAgICAgICAgdGhpcy5kaXNhYmxlZCA9IHRydWU7XG4gICAgICAgIGFkZENsYXNzKHRoaXMuY3JvcHBlciwgQ0xBU1NfRElTQUJMRUQpO1xuICAgICAgfVxuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcbiAgICAvKipcbiAgICAgKiBEZXN0cm95IHRoZSBjcm9wcGVyIGFuZCByZW1vdmUgdGhlIGluc3RhbmNlIGZyb20gdGhlIGltYWdlXG4gICAgICogQHJldHVybnMge0Nyb3BwZXJ9IHRoaXNcbiAgICAgKi9cbiAgICBkZXN0cm95OiBmdW5jdGlvbiBkZXN0cm95KCkge1xuICAgICAgdmFyIGVsZW1lbnQgPSB0aGlzLmVsZW1lbnQ7XG4gICAgICBpZiAoIWVsZW1lbnRbTkFNRVNQQUNFXSkge1xuICAgICAgICByZXR1cm4gdGhpcztcbiAgICAgIH1cbiAgICAgIGVsZW1lbnRbTkFNRVNQQUNFXSA9IHVuZGVmaW5lZDtcbiAgICAgIGlmICh0aGlzLmlzSW1nICYmIHRoaXMucmVwbGFjZWQpIHtcbiAgICAgICAgZWxlbWVudC5zcmMgPSB0aGlzLm9yaWdpbmFsVXJsO1xuICAgICAgfVxuICAgICAgdGhpcy51bmNyZWF0ZSgpO1xuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcbiAgICAvKipcbiAgICAgKiBNb3ZlIHRoZSBjYW52YXMgd2l0aCByZWxhdGl2ZSBvZmZzZXRzXG4gICAgICogQHBhcmFtIHtudW1iZXJ9IG9mZnNldFggLSBUaGUgcmVsYXRpdmUgb2Zmc2V0IGRpc3RhbmNlIG9uIHRoZSB4LWF4aXMuXG4gICAgICogQHBhcmFtIHtudW1iZXJ9IFtvZmZzZXRZPW9mZnNldFhdIC0gVGhlIHJlbGF0aXZlIG9mZnNldCBkaXN0YW5jZSBvbiB0aGUgeS1heGlzLlxuICAgICAqIEByZXR1cm5zIHtDcm9wcGVyfSB0aGlzXG4gICAgICovXG4gICAgbW92ZTogZnVuY3Rpb24gbW92ZShvZmZzZXRYKSB7XG4gICAgICB2YXIgb2Zmc2V0WSA9IGFyZ3VtZW50cy5sZW5ndGggPiAxICYmIGFyZ3VtZW50c1sxXSAhPT0gdW5kZWZpbmVkID8gYXJndW1lbnRzWzFdIDogb2Zmc2V0WDtcbiAgICAgIHZhciBfdGhpcyRjYW52YXNEYXRhID0gdGhpcy5jYW52YXNEYXRhLFxuICAgICAgICBsZWZ0ID0gX3RoaXMkY2FudmFzRGF0YS5sZWZ0LFxuICAgICAgICB0b3AgPSBfdGhpcyRjYW52YXNEYXRhLnRvcDtcbiAgICAgIHJldHVybiB0aGlzLm1vdmVUbyhpc1VuZGVmaW5lZChvZmZzZXRYKSA/IG9mZnNldFggOiBsZWZ0ICsgTnVtYmVyKG9mZnNldFgpLCBpc1VuZGVmaW5lZChvZmZzZXRZKSA/IG9mZnNldFkgOiB0b3AgKyBOdW1iZXIob2Zmc2V0WSkpO1xuICAgIH0sXG4gICAgLyoqXG4gICAgICogTW92ZSB0aGUgY2FudmFzIHRvIGFuIGFic29sdXRlIHBvaW50XG4gICAgICogQHBhcmFtIHtudW1iZXJ9IHggLSBUaGUgeC1heGlzIGNvb3JkaW5hdGUuXG4gICAgICogQHBhcmFtIHtudW1iZXJ9IFt5PXhdIC0gVGhlIHktYXhpcyBjb29yZGluYXRlLlxuICAgICAqIEByZXR1cm5zIHtDcm9wcGVyfSB0aGlzXG4gICAgICovXG4gICAgbW92ZVRvOiBmdW5jdGlvbiBtb3ZlVG8oeCkge1xuICAgICAgdmFyIHkgPSBhcmd1bWVudHMubGVuZ3RoID4gMSAmJiBhcmd1bWVudHNbMV0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1sxXSA6IHg7XG4gICAgICB2YXIgY2FudmFzRGF0YSA9IHRoaXMuY2FudmFzRGF0YTtcbiAgICAgIHZhciBjaGFuZ2VkID0gZmFsc2U7XG4gICAgICB4ID0gTnVtYmVyKHgpO1xuICAgICAgeSA9IE51bWJlcih5KTtcbiAgICAgIGlmICh0aGlzLnJlYWR5ICYmICF0aGlzLmRpc2FibGVkICYmIHRoaXMub3B0aW9ucy5tb3ZhYmxlKSB7XG4gICAgICAgIGlmIChpc051bWJlcih4KSkge1xuICAgICAgICAgIGNhbnZhc0RhdGEubGVmdCA9IHg7XG4gICAgICAgICAgY2hhbmdlZCA9IHRydWU7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKGlzTnVtYmVyKHkpKSB7XG4gICAgICAgICAgY2FudmFzRGF0YS50b3AgPSB5O1xuICAgICAgICAgIGNoYW5nZWQgPSB0cnVlO1xuICAgICAgICB9XG4gICAgICAgIGlmIChjaGFuZ2VkKSB7XG4gICAgICAgICAgdGhpcy5yZW5kZXJDYW52YXModHJ1ZSk7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG4gICAgLyoqXG4gICAgICogWm9vbSB0aGUgY2FudmFzIHdpdGggYSByZWxhdGl2ZSByYXRpb1xuICAgICAqIEBwYXJhbSB7bnVtYmVyfSByYXRpbyAtIFRoZSB0YXJnZXQgcmF0aW8uXG4gICAgICogQHBhcmFtIHtFdmVudH0gX29yaWdpbmFsRXZlbnQgLSBUaGUgb3JpZ2luYWwgZXZlbnQgaWYgYW55LlxuICAgICAqIEByZXR1cm5zIHtDcm9wcGVyfSB0aGlzXG4gICAgICovXG4gICAgem9vbTogZnVuY3Rpb24gem9vbShyYXRpbywgX29yaWdpbmFsRXZlbnQpIHtcbiAgICAgIHZhciBjYW52YXNEYXRhID0gdGhpcy5jYW52YXNEYXRhO1xuICAgICAgcmF0aW8gPSBOdW1iZXIocmF0aW8pO1xuICAgICAgaWYgKHJhdGlvIDwgMCkge1xuICAgICAgICByYXRpbyA9IDEgLyAoMSAtIHJhdGlvKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIHJhdGlvID0gMSArIHJhdGlvO1xuICAgICAgfVxuICAgICAgcmV0dXJuIHRoaXMuem9vbVRvKGNhbnZhc0RhdGEud2lkdGggKiByYXRpbyAvIGNhbnZhc0RhdGEubmF0dXJhbFdpZHRoLCBudWxsLCBfb3JpZ2luYWxFdmVudCk7XG4gICAgfSxcbiAgICAvKipcbiAgICAgKiBab29tIHRoZSBjYW52YXMgdG8gYW4gYWJzb2x1dGUgcmF0aW9cbiAgICAgKiBAcGFyYW0ge251bWJlcn0gcmF0aW8gLSBUaGUgdGFyZ2V0IHJhdGlvLlxuICAgICAqIEBwYXJhbSB7T2JqZWN0fSBwaXZvdCAtIFRoZSB6b29tIHBpdm90IHBvaW50IGNvb3JkaW5hdGUuXG4gICAgICogQHBhcmFtIHtFdmVudH0gX29yaWdpbmFsRXZlbnQgLSBUaGUgb3JpZ2luYWwgZXZlbnQgaWYgYW55LlxuICAgICAqIEByZXR1cm5zIHtDcm9wcGVyfSB0aGlzXG4gICAgICovXG4gICAgem9vbVRvOiBmdW5jdGlvbiB6b29tVG8ocmF0aW8sIHBpdm90LCBfb3JpZ2luYWxFdmVudCkge1xuICAgICAgdmFyIG9wdGlvbnMgPSB0aGlzLm9wdGlvbnMsXG4gICAgICAgIGNhbnZhc0RhdGEgPSB0aGlzLmNhbnZhc0RhdGE7XG4gICAgICB2YXIgd2lkdGggPSBjYW52YXNEYXRhLndpZHRoLFxuICAgICAgICBoZWlnaHQgPSBjYW52YXNEYXRhLmhlaWdodCxcbiAgICAgICAgbmF0dXJhbFdpZHRoID0gY2FudmFzRGF0YS5uYXR1cmFsV2lkdGgsXG4gICAgICAgIG5hdHVyYWxIZWlnaHQgPSBjYW52YXNEYXRhLm5hdHVyYWxIZWlnaHQ7XG4gICAgICByYXRpbyA9IE51bWJlcihyYXRpbyk7XG4gICAgICBpZiAocmF0aW8gPj0gMCAmJiB0aGlzLnJlYWR5ICYmICF0aGlzLmRpc2FibGVkICYmIG9wdGlvbnMuem9vbWFibGUpIHtcbiAgICAgICAgdmFyIG5ld1dpZHRoID0gbmF0dXJhbFdpZHRoICogcmF0aW87XG4gICAgICAgIHZhciBuZXdIZWlnaHQgPSBuYXR1cmFsSGVpZ2h0ICogcmF0aW87XG4gICAgICAgIGlmIChkaXNwYXRjaEV2ZW50KHRoaXMuZWxlbWVudCwgRVZFTlRfWk9PTSwge1xuICAgICAgICAgIHJhdGlvOiByYXRpbyxcbiAgICAgICAgICBvbGRSYXRpbzogd2lkdGggLyBuYXR1cmFsV2lkdGgsXG4gICAgICAgICAgb3JpZ2luYWxFdmVudDogX29yaWdpbmFsRXZlbnRcbiAgICAgICAgfSkgPT09IGZhbHNlKSB7XG4gICAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKF9vcmlnaW5hbEV2ZW50KSB7XG4gICAgICAgICAgdmFyIHBvaW50ZXJzID0gdGhpcy5wb2ludGVycztcbiAgICAgICAgICB2YXIgb2Zmc2V0ID0gZ2V0T2Zmc2V0KHRoaXMuY3JvcHBlcik7XG4gICAgICAgICAgdmFyIGNlbnRlciA9IHBvaW50ZXJzICYmIE9iamVjdC5rZXlzKHBvaW50ZXJzKS5sZW5ndGggPyBnZXRQb2ludGVyc0NlbnRlcihwb2ludGVycykgOiB7XG4gICAgICAgICAgICBwYWdlWDogX29yaWdpbmFsRXZlbnQucGFnZVgsXG4gICAgICAgICAgICBwYWdlWTogX29yaWdpbmFsRXZlbnQucGFnZVlcbiAgICAgICAgICB9O1xuXG4gICAgICAgICAgLy8gWm9vbSBmcm9tIHRoZSB0cmlnZ2VyaW5nIHBvaW50IG9mIHRoZSBldmVudFxuICAgICAgICAgIGNhbnZhc0RhdGEubGVmdCAtPSAobmV3V2lkdGggLSB3aWR0aCkgKiAoKGNlbnRlci5wYWdlWCAtIG9mZnNldC5sZWZ0IC0gY2FudmFzRGF0YS5sZWZ0KSAvIHdpZHRoKTtcbiAgICAgICAgICBjYW52YXNEYXRhLnRvcCAtPSAobmV3SGVpZ2h0IC0gaGVpZ2h0KSAqICgoY2VudGVyLnBhZ2VZIC0gb2Zmc2V0LnRvcCAtIGNhbnZhc0RhdGEudG9wKSAvIGhlaWdodCk7XG4gICAgICAgIH0gZWxzZSBpZiAoaXNQbGFpbk9iamVjdChwaXZvdCkgJiYgaXNOdW1iZXIocGl2b3QueCkgJiYgaXNOdW1iZXIocGl2b3QueSkpIHtcbiAgICAgICAgICBjYW52YXNEYXRhLmxlZnQgLT0gKG5ld1dpZHRoIC0gd2lkdGgpICogKChwaXZvdC54IC0gY2FudmFzRGF0YS5sZWZ0KSAvIHdpZHRoKTtcbiAgICAgICAgICBjYW52YXNEYXRhLnRvcCAtPSAobmV3SGVpZ2h0IC0gaGVpZ2h0KSAqICgocGl2b3QueSAtIGNhbnZhc0RhdGEudG9wKSAvIGhlaWdodCk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgLy8gWm9vbSBmcm9tIHRoZSBjZW50ZXIgb2YgdGhlIGNhbnZhc1xuICAgICAgICAgIGNhbnZhc0RhdGEubGVmdCAtPSAobmV3V2lkdGggLSB3aWR0aCkgLyAyO1xuICAgICAgICAgIGNhbnZhc0RhdGEudG9wIC09IChuZXdIZWlnaHQgLSBoZWlnaHQpIC8gMjtcbiAgICAgICAgfVxuICAgICAgICBjYW52YXNEYXRhLndpZHRoID0gbmV3V2lkdGg7XG4gICAgICAgIGNhbnZhc0RhdGEuaGVpZ2h0ID0gbmV3SGVpZ2h0O1xuICAgICAgICB0aGlzLnJlbmRlckNhbnZhcyh0cnVlKTtcbiAgICAgIH1cbiAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG4gICAgLyoqXG4gICAgICogUm90YXRlIHRoZSBjYW52YXMgd2l0aCBhIHJlbGF0aXZlIGRlZ3JlZVxuICAgICAqIEBwYXJhbSB7bnVtYmVyfSBkZWdyZWUgLSBUaGUgcm90YXRlIGRlZ3JlZS5cbiAgICAgKiBAcmV0dXJucyB7Q3JvcHBlcn0gdGhpc1xuICAgICAqL1xuICAgIHJvdGF0ZTogZnVuY3Rpb24gcm90YXRlKGRlZ3JlZSkge1xuICAgICAgcmV0dXJuIHRoaXMucm90YXRlVG8oKHRoaXMuaW1hZ2VEYXRhLnJvdGF0ZSB8fCAwKSArIE51bWJlcihkZWdyZWUpKTtcbiAgICB9LFxuICAgIC8qKlxuICAgICAqIFJvdGF0ZSB0aGUgY2FudmFzIHRvIGFuIGFic29sdXRlIGRlZ3JlZVxuICAgICAqIEBwYXJhbSB7bnVtYmVyfSBkZWdyZWUgLSBUaGUgcm90YXRlIGRlZ3JlZS5cbiAgICAgKiBAcmV0dXJucyB7Q3JvcHBlcn0gdGhpc1xuICAgICAqL1xuICAgIHJvdGF0ZVRvOiBmdW5jdGlvbiByb3RhdGVUbyhkZWdyZWUpIHtcbiAgICAgIGRlZ3JlZSA9IE51bWJlcihkZWdyZWUpO1xuICAgICAgaWYgKGlzTnVtYmVyKGRlZ3JlZSkgJiYgdGhpcy5yZWFkeSAmJiAhdGhpcy5kaXNhYmxlZCAmJiB0aGlzLm9wdGlvbnMucm90YXRhYmxlKSB7XG4gICAgICAgIHRoaXMuaW1hZ2VEYXRhLnJvdGF0ZSA9IGRlZ3JlZSAlIDM2MDtcbiAgICAgICAgdGhpcy5yZW5kZXJDYW52YXModHJ1ZSwgdHJ1ZSk7XG4gICAgICB9XG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxuICAgIC8qKlxuICAgICAqIFNjYWxlIHRoZSBpbWFnZSBvbiB0aGUgeC1heGlzLlxuICAgICAqIEBwYXJhbSB7bnVtYmVyfSBzY2FsZVggLSBUaGUgc2NhbGUgcmF0aW8gb24gdGhlIHgtYXhpcy5cbiAgICAgKiBAcmV0dXJucyB7Q3JvcHBlcn0gdGhpc1xuICAgICAqL1xuICAgIHNjYWxlWDogZnVuY3Rpb24gc2NhbGVYKF9zY2FsZVgpIHtcbiAgICAgIHZhciBzY2FsZVkgPSB0aGlzLmltYWdlRGF0YS5zY2FsZVk7XG4gICAgICByZXR1cm4gdGhpcy5zY2FsZShfc2NhbGVYLCBpc051bWJlcihzY2FsZVkpID8gc2NhbGVZIDogMSk7XG4gICAgfSxcbiAgICAvKipcbiAgICAgKiBTY2FsZSB0aGUgaW1hZ2Ugb24gdGhlIHktYXhpcy5cbiAgICAgKiBAcGFyYW0ge251bWJlcn0gc2NhbGVZIC0gVGhlIHNjYWxlIHJhdGlvIG9uIHRoZSB5LWF4aXMuXG4gICAgICogQHJldHVybnMge0Nyb3BwZXJ9IHRoaXNcbiAgICAgKi9cbiAgICBzY2FsZVk6IGZ1bmN0aW9uIHNjYWxlWShfc2NhbGVZKSB7XG4gICAgICB2YXIgc2NhbGVYID0gdGhpcy5pbWFnZURhdGEuc2NhbGVYO1xuICAgICAgcmV0dXJuIHRoaXMuc2NhbGUoaXNOdW1iZXIoc2NhbGVYKSA/IHNjYWxlWCA6IDEsIF9zY2FsZVkpO1xuICAgIH0sXG4gICAgLyoqXG4gICAgICogU2NhbGUgdGhlIGltYWdlXG4gICAgICogQHBhcmFtIHtudW1iZXJ9IHNjYWxlWCAtIFRoZSBzY2FsZSByYXRpbyBvbiB0aGUgeC1heGlzLlxuICAgICAqIEBwYXJhbSB7bnVtYmVyfSBbc2NhbGVZPXNjYWxlWF0gLSBUaGUgc2NhbGUgcmF0aW8gb24gdGhlIHktYXhpcy5cbiAgICAgKiBAcmV0dXJucyB7Q3JvcHBlcn0gdGhpc1xuICAgICAqL1xuICAgIHNjYWxlOiBmdW5jdGlvbiBzY2FsZShzY2FsZVgpIHtcbiAgICAgIHZhciBzY2FsZVkgPSBhcmd1bWVudHMubGVuZ3RoID4gMSAmJiBhcmd1bWVudHNbMV0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1sxXSA6IHNjYWxlWDtcbiAgICAgIHZhciBpbWFnZURhdGEgPSB0aGlzLmltYWdlRGF0YTtcbiAgICAgIHZhciB0cmFuc2Zvcm1lZCA9IGZhbHNlO1xuICAgICAgc2NhbGVYID0gTnVtYmVyKHNjYWxlWCk7XG4gICAgICBzY2FsZVkgPSBOdW1iZXIoc2NhbGVZKTtcbiAgICAgIGlmICh0aGlzLnJlYWR5ICYmICF0aGlzLmRpc2FibGVkICYmIHRoaXMub3B0aW9ucy5zY2FsYWJsZSkge1xuICAgICAgICBpZiAoaXNOdW1iZXIoc2NhbGVYKSkge1xuICAgICAgICAgIGltYWdlRGF0YS5zY2FsZVggPSBzY2FsZVg7XG4gICAgICAgICAgdHJhbnNmb3JtZWQgPSB0cnVlO1xuICAgICAgICB9XG4gICAgICAgIGlmIChpc051bWJlcihzY2FsZVkpKSB7XG4gICAgICAgICAgaW1hZ2VEYXRhLnNjYWxlWSA9IHNjYWxlWTtcbiAgICAgICAgICB0cmFuc2Zvcm1lZCA9IHRydWU7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKHRyYW5zZm9ybWVkKSB7XG4gICAgICAgICAgdGhpcy5yZW5kZXJDYW52YXModHJ1ZSwgdHJ1ZSk7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG4gICAgLyoqXG4gICAgICogR2V0IHRoZSBjcm9wcGVkIGFyZWEgcG9zaXRpb24gYW5kIHNpemUgZGF0YSAoYmFzZSBvbiB0aGUgb3JpZ2luYWwgaW1hZ2UpXG4gICAgICogQHBhcmFtIHtib29sZWFufSBbcm91bmRlZD1mYWxzZV0gLSBJbmRpY2F0ZSBpZiByb3VuZCB0aGUgZGF0YSB2YWx1ZXMgb3Igbm90LlxuICAgICAqIEByZXR1cm5zIHtPYmplY3R9IFRoZSByZXN1bHQgY3JvcHBlZCBkYXRhLlxuICAgICAqL1xuICAgIGdldERhdGE6IGZ1bmN0aW9uIGdldERhdGEoKSB7XG4gICAgICB2YXIgcm91bmRlZCA9IGFyZ3VtZW50cy5sZW5ndGggPiAwICYmIGFyZ3VtZW50c1swXSAhPT0gdW5kZWZpbmVkID8gYXJndW1lbnRzWzBdIDogZmFsc2U7XG4gICAgICB2YXIgb3B0aW9ucyA9IHRoaXMub3B0aW9ucyxcbiAgICAgICAgaW1hZ2VEYXRhID0gdGhpcy5pbWFnZURhdGEsXG4gICAgICAgIGNhbnZhc0RhdGEgPSB0aGlzLmNhbnZhc0RhdGEsXG4gICAgICAgIGNyb3BCb3hEYXRhID0gdGhpcy5jcm9wQm94RGF0YTtcbiAgICAgIHZhciBkYXRhO1xuICAgICAgaWYgKHRoaXMucmVhZHkgJiYgdGhpcy5jcm9wcGVkKSB7XG4gICAgICAgIGRhdGEgPSB7XG4gICAgICAgICAgeDogY3JvcEJveERhdGEubGVmdCAtIGNhbnZhc0RhdGEubGVmdCxcbiAgICAgICAgICB5OiBjcm9wQm94RGF0YS50b3AgLSBjYW52YXNEYXRhLnRvcCxcbiAgICAgICAgICB3aWR0aDogY3JvcEJveERhdGEud2lkdGgsXG4gICAgICAgICAgaGVpZ2h0OiBjcm9wQm94RGF0YS5oZWlnaHRcbiAgICAgICAgfTtcbiAgICAgICAgdmFyIHJhdGlvID0gaW1hZ2VEYXRhLndpZHRoIC8gaW1hZ2VEYXRhLm5hdHVyYWxXaWR0aDtcbiAgICAgICAgZm9yRWFjaChkYXRhLCBmdW5jdGlvbiAobiwgaSkge1xuICAgICAgICAgIGRhdGFbaV0gPSBuIC8gcmF0aW87XG4gICAgICAgIH0pO1xuICAgICAgICBpZiAocm91bmRlZCkge1xuICAgICAgICAgIC8vIEluIGNhc2Ugcm91bmRpbmcgb2ZmIGxlYWRzIHRvIGV4dHJhIDFweCBpbiByaWdodCBvciBib3R0b20gYm9yZGVyXG4gICAgICAgICAgLy8gd2Ugc2hvdWxkIHJvdW5kIHRoZSB0b3AtbGVmdCBjb3JuZXIgYW5kIHRoZSBkaW1lbnNpb24gKCMzNDMpLlxuICAgICAgICAgIHZhciBib3R0b20gPSBNYXRoLnJvdW5kKGRhdGEueSArIGRhdGEuaGVpZ2h0KTtcbiAgICAgICAgICB2YXIgcmlnaHQgPSBNYXRoLnJvdW5kKGRhdGEueCArIGRhdGEud2lkdGgpO1xuICAgICAgICAgIGRhdGEueCA9IE1hdGgucm91bmQoZGF0YS54KTtcbiAgICAgICAgICBkYXRhLnkgPSBNYXRoLnJvdW5kKGRhdGEueSk7XG4gICAgICAgICAgZGF0YS53aWR0aCA9IHJpZ2h0IC0gZGF0YS54O1xuICAgICAgICAgIGRhdGEuaGVpZ2h0ID0gYm90dG9tIC0gZGF0YS55O1xuICAgICAgICB9XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBkYXRhID0ge1xuICAgICAgICAgIHg6IDAsXG4gICAgICAgICAgeTogMCxcbiAgICAgICAgICB3aWR0aDogMCxcbiAgICAgICAgICBoZWlnaHQ6IDBcbiAgICAgICAgfTtcbiAgICAgIH1cbiAgICAgIGlmIChvcHRpb25zLnJvdGF0YWJsZSkge1xuICAgICAgICBkYXRhLnJvdGF0ZSA9IGltYWdlRGF0YS5yb3RhdGUgfHwgMDtcbiAgICAgIH1cbiAgICAgIGlmIChvcHRpb25zLnNjYWxhYmxlKSB7XG4gICAgICAgIGRhdGEuc2NhbGVYID0gaW1hZ2VEYXRhLnNjYWxlWCB8fCAxO1xuICAgICAgICBkYXRhLnNjYWxlWSA9IGltYWdlRGF0YS5zY2FsZVkgfHwgMTtcbiAgICAgIH1cbiAgICAgIHJldHVybiBkYXRhO1xuICAgIH0sXG4gICAgLyoqXG4gICAgICogU2V0IHRoZSBjcm9wcGVkIGFyZWEgcG9zaXRpb24gYW5kIHNpemUgd2l0aCBuZXcgZGF0YVxuICAgICAqIEBwYXJhbSB7T2JqZWN0fSBkYXRhIC0gVGhlIG5ldyBkYXRhLlxuICAgICAqIEByZXR1cm5zIHtDcm9wcGVyfSB0aGlzXG4gICAgICovXG4gICAgc2V0RGF0YTogZnVuY3Rpb24gc2V0RGF0YShkYXRhKSB7XG4gICAgICB2YXIgb3B0aW9ucyA9IHRoaXMub3B0aW9ucyxcbiAgICAgICAgaW1hZ2VEYXRhID0gdGhpcy5pbWFnZURhdGEsXG4gICAgICAgIGNhbnZhc0RhdGEgPSB0aGlzLmNhbnZhc0RhdGE7XG4gICAgICB2YXIgY3JvcEJveERhdGEgPSB7fTtcbiAgICAgIGlmICh0aGlzLnJlYWR5ICYmICF0aGlzLmRpc2FibGVkICYmIGlzUGxhaW5PYmplY3QoZGF0YSkpIHtcbiAgICAgICAgdmFyIHRyYW5zZm9ybWVkID0gZmFsc2U7XG4gICAgICAgIGlmIChvcHRpb25zLnJvdGF0YWJsZSkge1xuICAgICAgICAgIGlmIChpc051bWJlcihkYXRhLnJvdGF0ZSkgJiYgZGF0YS5yb3RhdGUgIT09IGltYWdlRGF0YS5yb3RhdGUpIHtcbiAgICAgICAgICAgIGltYWdlRGF0YS5yb3RhdGUgPSBkYXRhLnJvdGF0ZTtcbiAgICAgICAgICAgIHRyYW5zZm9ybWVkID0gdHJ1ZTtcbiAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgICAgaWYgKG9wdGlvbnMuc2NhbGFibGUpIHtcbiAgICAgICAgICBpZiAoaXNOdW1iZXIoZGF0YS5zY2FsZVgpICYmIGRhdGEuc2NhbGVYICE9PSBpbWFnZURhdGEuc2NhbGVYKSB7XG4gICAgICAgICAgICBpbWFnZURhdGEuc2NhbGVYID0gZGF0YS5zY2FsZVg7XG4gICAgICAgICAgICB0cmFuc2Zvcm1lZCA9IHRydWU7XG4gICAgICAgICAgfVxuICAgICAgICAgIGlmIChpc051bWJlcihkYXRhLnNjYWxlWSkgJiYgZGF0YS5zY2FsZVkgIT09IGltYWdlRGF0YS5zY2FsZVkpIHtcbiAgICAgICAgICAgIGltYWdlRGF0YS5zY2FsZVkgPSBkYXRhLnNjYWxlWTtcbiAgICAgICAgICAgIHRyYW5zZm9ybWVkID0gdHJ1ZTtcbiAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgICAgaWYgKHRyYW5zZm9ybWVkKSB7XG4gICAgICAgICAgdGhpcy5yZW5kZXJDYW52YXModHJ1ZSwgdHJ1ZSk7XG4gICAgICAgIH1cbiAgICAgICAgdmFyIHJhdGlvID0gaW1hZ2VEYXRhLndpZHRoIC8gaW1hZ2VEYXRhLm5hdHVyYWxXaWR0aDtcbiAgICAgICAgaWYgKGlzTnVtYmVyKGRhdGEueCkpIHtcbiAgICAgICAgICBjcm9wQm94RGF0YS5sZWZ0ID0gZGF0YS54ICogcmF0aW8gKyBjYW52YXNEYXRhLmxlZnQ7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKGlzTnVtYmVyKGRhdGEueSkpIHtcbiAgICAgICAgICBjcm9wQm94RGF0YS50b3AgPSBkYXRhLnkgKiByYXRpbyArIGNhbnZhc0RhdGEudG9wO1xuICAgICAgICB9XG4gICAgICAgIGlmIChpc051bWJlcihkYXRhLndpZHRoKSkge1xuICAgICAgICAgIGNyb3BCb3hEYXRhLndpZHRoID0gZGF0YS53aWR0aCAqIHJhdGlvO1xuICAgICAgICB9XG4gICAgICAgIGlmIChpc051bWJlcihkYXRhLmhlaWdodCkpIHtcbiAgICAgICAgICBjcm9wQm94RGF0YS5oZWlnaHQgPSBkYXRhLmhlaWdodCAqIHJhdGlvO1xuICAgICAgICB9XG4gICAgICAgIHRoaXMuc2V0Q3JvcEJveERhdGEoY3JvcEJveERhdGEpO1xuICAgICAgfVxuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcbiAgICAvKipcbiAgICAgKiBHZXQgdGhlIGNvbnRhaW5lciBzaXplIGRhdGEuXG4gICAgICogQHJldHVybnMge09iamVjdH0gVGhlIHJlc3VsdCBjb250YWluZXIgZGF0YS5cbiAgICAgKi9cbiAgICBnZXRDb250YWluZXJEYXRhOiBmdW5jdGlvbiBnZXRDb250YWluZXJEYXRhKCkge1xuICAgICAgcmV0dXJuIHRoaXMucmVhZHkgPyBhc3NpZ24oe30sIHRoaXMuY29udGFpbmVyRGF0YSkgOiB7fTtcbiAgICB9LFxuICAgIC8qKlxuICAgICAqIEdldCB0aGUgaW1hZ2UgcG9zaXRpb24gYW5kIHNpemUgZGF0YS5cbiAgICAgKiBAcmV0dXJucyB7T2JqZWN0fSBUaGUgcmVzdWx0IGltYWdlIGRhdGEuXG4gICAgICovXG4gICAgZ2V0SW1hZ2VEYXRhOiBmdW5jdGlvbiBnZXRJbWFnZURhdGEoKSB7XG4gICAgICByZXR1cm4gdGhpcy5zaXplZCA/IGFzc2lnbih7fSwgdGhpcy5pbWFnZURhdGEpIDoge307XG4gICAgfSxcbiAgICAvKipcbiAgICAgKiBHZXQgdGhlIGNhbnZhcyBwb3NpdGlvbiBhbmQgc2l6ZSBkYXRhLlxuICAgICAqIEByZXR1cm5zIHtPYmplY3R9IFRoZSByZXN1bHQgY2FudmFzIGRhdGEuXG4gICAgICovXG4gICAgZ2V0Q2FudmFzRGF0YTogZnVuY3Rpb24gZ2V0Q2FudmFzRGF0YSgpIHtcbiAgICAgIHZhciBjYW52YXNEYXRhID0gdGhpcy5jYW52YXNEYXRhO1xuICAgICAgdmFyIGRhdGEgPSB7fTtcbiAgICAgIGlmICh0aGlzLnJlYWR5KSB7XG4gICAgICAgIGZvckVhY2goWydsZWZ0JywgJ3RvcCcsICd3aWR0aCcsICdoZWlnaHQnLCAnbmF0dXJhbFdpZHRoJywgJ25hdHVyYWxIZWlnaHQnXSwgZnVuY3Rpb24gKG4pIHtcbiAgICAgICAgICBkYXRhW25dID0gY2FudmFzRGF0YVtuXTtcbiAgICAgICAgfSk7XG4gICAgICB9XG4gICAgICByZXR1cm4gZGF0YTtcbiAgICB9LFxuICAgIC8qKlxuICAgICAqIFNldCB0aGUgY2FudmFzIHBvc2l0aW9uIGFuZCBzaXplIHdpdGggbmV3IGRhdGEuXG4gICAgICogQHBhcmFtIHtPYmplY3R9IGRhdGEgLSBUaGUgbmV3IGNhbnZhcyBkYXRhLlxuICAgICAqIEByZXR1cm5zIHtDcm9wcGVyfSB0aGlzXG4gICAgICovXG4gICAgc2V0Q2FudmFzRGF0YTogZnVuY3Rpb24gc2V0Q2FudmFzRGF0YShkYXRhKSB7XG4gICAgICB2YXIgY2FudmFzRGF0YSA9IHRoaXMuY2FudmFzRGF0YTtcbiAgICAgIHZhciBhc3BlY3RSYXRpbyA9IGNhbnZhc0RhdGEuYXNwZWN0UmF0aW87XG4gICAgICBpZiAodGhpcy5yZWFkeSAmJiAhdGhpcy5kaXNhYmxlZCAmJiBpc1BsYWluT2JqZWN0KGRhdGEpKSB7XG4gICAgICAgIGlmIChpc051bWJlcihkYXRhLmxlZnQpKSB7XG4gICAgICAgICAgY2FudmFzRGF0YS5sZWZ0ID0gZGF0YS5sZWZ0O1xuICAgICAgICB9XG4gICAgICAgIGlmIChpc051bWJlcihkYXRhLnRvcCkpIHtcbiAgICAgICAgICBjYW52YXNEYXRhLnRvcCA9IGRhdGEudG9wO1xuICAgICAgICB9XG4gICAgICAgIGlmIChpc051bWJlcihkYXRhLndpZHRoKSkge1xuICAgICAgICAgIGNhbnZhc0RhdGEud2lkdGggPSBkYXRhLndpZHRoO1xuICAgICAgICAgIGNhbnZhc0RhdGEuaGVpZ2h0ID0gZGF0YS53aWR0aCAvIGFzcGVjdFJhdGlvO1xuICAgICAgICB9IGVsc2UgaWYgKGlzTnVtYmVyKGRhdGEuaGVpZ2h0KSkge1xuICAgICAgICAgIGNhbnZhc0RhdGEuaGVpZ2h0ID0gZGF0YS5oZWlnaHQ7XG4gICAgICAgICAgY2FudmFzRGF0YS53aWR0aCA9IGRhdGEuaGVpZ2h0ICogYXNwZWN0UmF0aW87XG4gICAgICAgIH1cbiAgICAgICAgdGhpcy5yZW5kZXJDYW52YXModHJ1ZSk7XG4gICAgICB9XG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxuICAgIC8qKlxuICAgICAqIEdldCB0aGUgY3JvcCBib3ggcG9zaXRpb24gYW5kIHNpemUgZGF0YS5cbiAgICAgKiBAcmV0dXJucyB7T2JqZWN0fSBUaGUgcmVzdWx0IGNyb3AgYm94IGRhdGEuXG4gICAgICovXG4gICAgZ2V0Q3JvcEJveERhdGE6IGZ1bmN0aW9uIGdldENyb3BCb3hEYXRhKCkge1xuICAgICAgdmFyIGNyb3BCb3hEYXRhID0gdGhpcy5jcm9wQm94RGF0YTtcbiAgICAgIHZhciBkYXRhO1xuICAgICAgaWYgKHRoaXMucmVhZHkgJiYgdGhpcy5jcm9wcGVkKSB7XG4gICAgICAgIGRhdGEgPSB7XG4gICAgICAgICAgbGVmdDogY3JvcEJveERhdGEubGVmdCxcbiAgICAgICAgICB0b3A6IGNyb3BCb3hEYXRhLnRvcCxcbiAgICAgICAgICB3aWR0aDogY3JvcEJveERhdGEud2lkdGgsXG4gICAgICAgICAgaGVpZ2h0OiBjcm9wQm94RGF0YS5oZWlnaHRcbiAgICAgICAgfTtcbiAgICAgIH1cbiAgICAgIHJldHVybiBkYXRhIHx8IHt9O1xuICAgIH0sXG4gICAgLyoqXG4gICAgICogU2V0IHRoZSBjcm9wIGJveCBwb3NpdGlvbiBhbmQgc2l6ZSB3aXRoIG5ldyBkYXRhLlxuICAgICAqIEBwYXJhbSB7T2JqZWN0fSBkYXRhIC0gVGhlIG5ldyBjcm9wIGJveCBkYXRhLlxuICAgICAqIEByZXR1cm5zIHtDcm9wcGVyfSB0aGlzXG4gICAgICovXG4gICAgc2V0Q3JvcEJveERhdGE6IGZ1bmN0aW9uIHNldENyb3BCb3hEYXRhKGRhdGEpIHtcbiAgICAgIHZhciBjcm9wQm94RGF0YSA9IHRoaXMuY3JvcEJveERhdGE7XG4gICAgICB2YXIgYXNwZWN0UmF0aW8gPSB0aGlzLm9wdGlvbnMuYXNwZWN0UmF0aW87XG4gICAgICB2YXIgd2lkdGhDaGFuZ2VkO1xuICAgICAgdmFyIGhlaWdodENoYW5nZWQ7XG4gICAgICBpZiAodGhpcy5yZWFkeSAmJiB0aGlzLmNyb3BwZWQgJiYgIXRoaXMuZGlzYWJsZWQgJiYgaXNQbGFpbk9iamVjdChkYXRhKSkge1xuICAgICAgICBpZiAoaXNOdW1iZXIoZGF0YS5sZWZ0KSkge1xuICAgICAgICAgIGNyb3BCb3hEYXRhLmxlZnQgPSBkYXRhLmxlZnQ7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKGlzTnVtYmVyKGRhdGEudG9wKSkge1xuICAgICAgICAgIGNyb3BCb3hEYXRhLnRvcCA9IGRhdGEudG9wO1xuICAgICAgICB9XG4gICAgICAgIGlmIChpc051bWJlcihkYXRhLndpZHRoKSAmJiBkYXRhLndpZHRoICE9PSBjcm9wQm94RGF0YS53aWR0aCkge1xuICAgICAgICAgIHdpZHRoQ2hhbmdlZCA9IHRydWU7XG4gICAgICAgICAgY3JvcEJveERhdGEud2lkdGggPSBkYXRhLndpZHRoO1xuICAgICAgICB9XG4gICAgICAgIGlmIChpc051bWJlcihkYXRhLmhlaWdodCkgJiYgZGF0YS5oZWlnaHQgIT09IGNyb3BCb3hEYXRhLmhlaWdodCkge1xuICAgICAgICAgIGhlaWdodENoYW5nZWQgPSB0cnVlO1xuICAgICAgICAgIGNyb3BCb3hEYXRhLmhlaWdodCA9IGRhdGEuaGVpZ2h0O1xuICAgICAgICB9XG4gICAgICAgIGlmIChhc3BlY3RSYXRpbykge1xuICAgICAgICAgIGlmICh3aWR0aENoYW5nZWQpIHtcbiAgICAgICAgICAgIGNyb3BCb3hEYXRhLmhlaWdodCA9IGNyb3BCb3hEYXRhLndpZHRoIC8gYXNwZWN0UmF0aW87XG4gICAgICAgICAgfSBlbHNlIGlmIChoZWlnaHRDaGFuZ2VkKSB7XG4gICAgICAgICAgICBjcm9wQm94RGF0YS53aWR0aCA9IGNyb3BCb3hEYXRhLmhlaWdodCAqIGFzcGVjdFJhdGlvO1xuICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgICAgICB0aGlzLnJlbmRlckNyb3BCb3goKTtcbiAgICAgIH1cbiAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG4gICAgLyoqXG4gICAgICogR2V0IGEgY2FudmFzIGRyYXduIHRoZSBjcm9wcGVkIGltYWdlLlxuICAgICAqIEBwYXJhbSB7T2JqZWN0fSBbb3B0aW9ucz17fV0gLSBUaGUgY29uZmlnIG9wdGlvbnMuXG4gICAgICogQHJldHVybnMge0hUTUxDYW52YXNFbGVtZW50fSAtIFRoZSByZXN1bHQgY2FudmFzLlxuICAgICAqL1xuICAgIGdldENyb3BwZWRDYW52YXM6IGZ1bmN0aW9uIGdldENyb3BwZWRDYW52YXMoKSB7XG4gICAgICB2YXIgb3B0aW9ucyA9IGFyZ3VtZW50cy5sZW5ndGggPiAwICYmIGFyZ3VtZW50c1swXSAhPT0gdW5kZWZpbmVkID8gYXJndW1lbnRzWzBdIDoge307XG4gICAgICBpZiAoIXRoaXMucmVhZHkgfHwgIXdpbmRvdy5IVE1MQ2FudmFzRWxlbWVudCkge1xuICAgICAgICByZXR1cm4gbnVsbDtcbiAgICAgIH1cbiAgICAgIHZhciBjYW52YXNEYXRhID0gdGhpcy5jYW52YXNEYXRhO1xuICAgICAgdmFyIHNvdXJjZSA9IGdldFNvdXJjZUNhbnZhcyh0aGlzLmltYWdlLCB0aGlzLmltYWdlRGF0YSwgY2FudmFzRGF0YSwgb3B0aW9ucyk7XG5cbiAgICAgIC8vIFJldHVybnMgdGhlIHNvdXJjZSBjYW52YXMgaWYgaXQgaXMgbm90IGNyb3BwZWQuXG4gICAgICBpZiAoIXRoaXMuY3JvcHBlZCkge1xuICAgICAgICByZXR1cm4gc291cmNlO1xuICAgICAgfVxuICAgICAgdmFyIF90aGlzJGdldERhdGEgPSB0aGlzLmdldERhdGEoKSxcbiAgICAgICAgaW5pdGlhbFggPSBfdGhpcyRnZXREYXRhLngsXG4gICAgICAgIGluaXRpYWxZID0gX3RoaXMkZ2V0RGF0YS55LFxuICAgICAgICBpbml0aWFsV2lkdGggPSBfdGhpcyRnZXREYXRhLndpZHRoLFxuICAgICAgICBpbml0aWFsSGVpZ2h0ID0gX3RoaXMkZ2V0RGF0YS5oZWlnaHQ7XG4gICAgICB2YXIgcmF0aW8gPSBzb3VyY2Uud2lkdGggLyBNYXRoLmZsb29yKGNhbnZhc0RhdGEubmF0dXJhbFdpZHRoKTtcbiAgICAgIGlmIChyYXRpbyAhPT0gMSkge1xuICAgICAgICBpbml0aWFsWCAqPSByYXRpbztcbiAgICAgICAgaW5pdGlhbFkgKj0gcmF0aW87XG4gICAgICAgIGluaXRpYWxXaWR0aCAqPSByYXRpbztcbiAgICAgICAgaW5pdGlhbEhlaWdodCAqPSByYXRpbztcbiAgICAgIH1cbiAgICAgIHZhciBhc3BlY3RSYXRpbyA9IGluaXRpYWxXaWR0aCAvIGluaXRpYWxIZWlnaHQ7XG4gICAgICB2YXIgbWF4U2l6ZXMgPSBnZXRBZGp1c3RlZFNpemVzKHtcbiAgICAgICAgYXNwZWN0UmF0aW86IGFzcGVjdFJhdGlvLFxuICAgICAgICB3aWR0aDogb3B0aW9ucy5tYXhXaWR0aCB8fCBJbmZpbml0eSxcbiAgICAgICAgaGVpZ2h0OiBvcHRpb25zLm1heEhlaWdodCB8fCBJbmZpbml0eVxuICAgICAgfSk7XG4gICAgICB2YXIgbWluU2l6ZXMgPSBnZXRBZGp1c3RlZFNpemVzKHtcbiAgICAgICAgYXNwZWN0UmF0aW86IGFzcGVjdFJhdGlvLFxuICAgICAgICB3aWR0aDogb3B0aW9ucy5taW5XaWR0aCB8fCAwLFxuICAgICAgICBoZWlnaHQ6IG9wdGlvbnMubWluSGVpZ2h0IHx8IDBcbiAgICAgIH0sICdjb3ZlcicpO1xuICAgICAgdmFyIF9nZXRBZGp1c3RlZFNpemVzID0gZ2V0QWRqdXN0ZWRTaXplcyh7XG4gICAgICAgICAgYXNwZWN0UmF0aW86IGFzcGVjdFJhdGlvLFxuICAgICAgICAgIHdpZHRoOiBvcHRpb25zLndpZHRoIHx8IChyYXRpbyAhPT0gMSA/IHNvdXJjZS53aWR0aCA6IGluaXRpYWxXaWR0aCksXG4gICAgICAgICAgaGVpZ2h0OiBvcHRpb25zLmhlaWdodCB8fCAocmF0aW8gIT09IDEgPyBzb3VyY2UuaGVpZ2h0IDogaW5pdGlhbEhlaWdodClcbiAgICAgICAgfSksXG4gICAgICAgIHdpZHRoID0gX2dldEFkanVzdGVkU2l6ZXMud2lkdGgsXG4gICAgICAgIGhlaWdodCA9IF9nZXRBZGp1c3RlZFNpemVzLmhlaWdodDtcbiAgICAgIHdpZHRoID0gTWF0aC5taW4obWF4U2l6ZXMud2lkdGgsIE1hdGgubWF4KG1pblNpemVzLndpZHRoLCB3aWR0aCkpO1xuICAgICAgaGVpZ2h0ID0gTWF0aC5taW4obWF4U2l6ZXMuaGVpZ2h0LCBNYXRoLm1heChtaW5TaXplcy5oZWlnaHQsIGhlaWdodCkpO1xuICAgICAgdmFyIGNhbnZhcyA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2NhbnZhcycpO1xuICAgICAgdmFyIGNvbnRleHQgPSBjYW52YXMuZ2V0Q29udGV4dCgnMmQnKTtcbiAgICAgIGNhbnZhcy53aWR0aCA9IG5vcm1hbGl6ZURlY2ltYWxOdW1iZXIod2lkdGgpO1xuICAgICAgY2FudmFzLmhlaWdodCA9IG5vcm1hbGl6ZURlY2ltYWxOdW1iZXIoaGVpZ2h0KTtcbiAgICAgIGNvbnRleHQuZmlsbFN0eWxlID0gb3B0aW9ucy5maWxsQ29sb3IgfHwgJ3RyYW5zcGFyZW50JztcbiAgICAgIGNvbnRleHQuZmlsbFJlY3QoMCwgMCwgd2lkdGgsIGhlaWdodCk7XG4gICAgICB2YXIgX29wdGlvbnMkaW1hZ2VTbW9vdGhpID0gb3B0aW9ucy5pbWFnZVNtb290aGluZ0VuYWJsZWQsXG4gICAgICAgIGltYWdlU21vb3RoaW5nRW5hYmxlZCA9IF9vcHRpb25zJGltYWdlU21vb3RoaSA9PT0gdm9pZCAwID8gdHJ1ZSA6IF9vcHRpb25zJGltYWdlU21vb3RoaSxcbiAgICAgICAgaW1hZ2VTbW9vdGhpbmdRdWFsaXR5ID0gb3B0aW9ucy5pbWFnZVNtb290aGluZ1F1YWxpdHk7XG4gICAgICBjb250ZXh0LmltYWdlU21vb3RoaW5nRW5hYmxlZCA9IGltYWdlU21vb3RoaW5nRW5hYmxlZDtcbiAgICAgIGlmIChpbWFnZVNtb290aGluZ1F1YWxpdHkpIHtcbiAgICAgICAgY29udGV4dC5pbWFnZVNtb290aGluZ1F1YWxpdHkgPSBpbWFnZVNtb290aGluZ1F1YWxpdHk7XG4gICAgICB9XG5cbiAgICAgIC8vIGh0dHBzOi8vZGV2ZWxvcGVyLm1vemlsbGEub3JnL2VuLVVTL2RvY3MvV2ViL0FQSS9DYW52YXNSZW5kZXJpbmdDb250ZXh0MkQuZHJhd0ltYWdlXG4gICAgICB2YXIgc291cmNlV2lkdGggPSBzb3VyY2Uud2lkdGg7XG4gICAgICB2YXIgc291cmNlSGVpZ2h0ID0gc291cmNlLmhlaWdodDtcblxuICAgICAgLy8gU291cmNlIGNhbnZhcyBwYXJhbWV0ZXJzXG4gICAgICB2YXIgc3JjWCA9IGluaXRpYWxYO1xuICAgICAgdmFyIHNyY1kgPSBpbml0aWFsWTtcbiAgICAgIHZhciBzcmNXaWR0aDtcbiAgICAgIHZhciBzcmNIZWlnaHQ7XG5cbiAgICAgIC8vIERlc3RpbmF0aW9uIGNhbnZhcyBwYXJhbWV0ZXJzXG4gICAgICB2YXIgZHN0WDtcbiAgICAgIHZhciBkc3RZO1xuICAgICAgdmFyIGRzdFdpZHRoO1xuICAgICAgdmFyIGRzdEhlaWdodDtcbiAgICAgIGlmIChzcmNYIDw9IC1pbml0aWFsV2lkdGggfHwgc3JjWCA+IHNvdXJjZVdpZHRoKSB7XG4gICAgICAgIHNyY1ggPSAwO1xuICAgICAgICBzcmNXaWR0aCA9IDA7XG4gICAgICAgIGRzdFggPSAwO1xuICAgICAgICBkc3RXaWR0aCA9IDA7XG4gICAgICB9IGVsc2UgaWYgKHNyY1ggPD0gMCkge1xuICAgICAgICBkc3RYID0gLXNyY1g7XG4gICAgICAgIHNyY1ggPSAwO1xuICAgICAgICBzcmNXaWR0aCA9IE1hdGgubWluKHNvdXJjZVdpZHRoLCBpbml0aWFsV2lkdGggKyBzcmNYKTtcbiAgICAgICAgZHN0V2lkdGggPSBzcmNXaWR0aDtcbiAgICAgIH0gZWxzZSBpZiAoc3JjWCA8PSBzb3VyY2VXaWR0aCkge1xuICAgICAgICBkc3RYID0gMDtcbiAgICAgICAgc3JjV2lkdGggPSBNYXRoLm1pbihpbml0aWFsV2lkdGgsIHNvdXJjZVdpZHRoIC0gc3JjWCk7XG4gICAgICAgIGRzdFdpZHRoID0gc3JjV2lkdGg7XG4gICAgICB9XG4gICAgICBpZiAoc3JjV2lkdGggPD0gMCB8fCBzcmNZIDw9IC1pbml0aWFsSGVpZ2h0IHx8IHNyY1kgPiBzb3VyY2VIZWlnaHQpIHtcbiAgICAgICAgc3JjWSA9IDA7XG4gICAgICAgIHNyY0hlaWdodCA9IDA7XG4gICAgICAgIGRzdFkgPSAwO1xuICAgICAgICBkc3RIZWlnaHQgPSAwO1xuICAgICAgfSBlbHNlIGlmIChzcmNZIDw9IDApIHtcbiAgICAgICAgZHN0WSA9IC1zcmNZO1xuICAgICAgICBzcmNZID0gMDtcbiAgICAgICAgc3JjSGVpZ2h0ID0gTWF0aC5taW4oc291cmNlSGVpZ2h0LCBpbml0aWFsSGVpZ2h0ICsgc3JjWSk7XG4gICAgICAgIGRzdEhlaWdodCA9IHNyY0hlaWdodDtcbiAgICAgIH0gZWxzZSBpZiAoc3JjWSA8PSBzb3VyY2VIZWlnaHQpIHtcbiAgICAgICAgZHN0WSA9IDA7XG4gICAgICAgIHNyY0hlaWdodCA9IE1hdGgubWluKGluaXRpYWxIZWlnaHQsIHNvdXJjZUhlaWdodCAtIHNyY1kpO1xuICAgICAgICBkc3RIZWlnaHQgPSBzcmNIZWlnaHQ7XG4gICAgICB9XG4gICAgICB2YXIgcGFyYW1zID0gW3NyY1gsIHNyY1ksIHNyY1dpZHRoLCBzcmNIZWlnaHRdO1xuXG4gICAgICAvLyBBdm9pZCBcIkluZGV4U2l6ZUVycm9yXCJcbiAgICAgIGlmIChkc3RXaWR0aCA+IDAgJiYgZHN0SGVpZ2h0ID4gMCkge1xuICAgICAgICB2YXIgc2NhbGUgPSB3aWR0aCAvIGluaXRpYWxXaWR0aDtcbiAgICAgICAgcGFyYW1zLnB1c2goZHN0WCAqIHNjYWxlLCBkc3RZICogc2NhbGUsIGRzdFdpZHRoICogc2NhbGUsIGRzdEhlaWdodCAqIHNjYWxlKTtcbiAgICAgIH1cblxuICAgICAgLy8gQWxsIHRoZSBudW1lcmljYWwgcGFyYW1ldGVycyBzaG91bGQgYmUgaW50ZWdlciBmb3IgYGRyYXdJbWFnZWBcbiAgICAgIC8vIGh0dHBzOi8vZ2l0aHViLmNvbS9mZW5neXVhbmNoZW4vY3JvcHBlci9pc3N1ZXMvNDc2XG4gICAgICBjb250ZXh0LmRyYXdJbWFnZS5hcHBseShjb250ZXh0LCBbc291cmNlXS5jb25jYXQoX3RvQ29uc3VtYWJsZUFycmF5KHBhcmFtcy5tYXAoZnVuY3Rpb24gKHBhcmFtKSB7XG4gICAgICAgIHJldHVybiBNYXRoLmZsb29yKG5vcm1hbGl6ZURlY2ltYWxOdW1iZXIocGFyYW0pKTtcbiAgICAgIH0pKSkpO1xuICAgICAgcmV0dXJuIGNhbnZhcztcbiAgICB9LFxuICAgIC8qKlxuICAgICAqIENoYW5nZSB0aGUgYXNwZWN0IHJhdGlvIG9mIHRoZSBjcm9wIGJveC5cbiAgICAgKiBAcGFyYW0ge251bWJlcn0gYXNwZWN0UmF0aW8gLSBUaGUgbmV3IGFzcGVjdCByYXRpby5cbiAgICAgKiBAcmV0dXJucyB7Q3JvcHBlcn0gdGhpc1xuICAgICAqL1xuICAgIHNldEFzcGVjdFJhdGlvOiBmdW5jdGlvbiBzZXRBc3BlY3RSYXRpbyhhc3BlY3RSYXRpbykge1xuICAgICAgdmFyIG9wdGlvbnMgPSB0aGlzLm9wdGlvbnM7XG4gICAgICBpZiAoIXRoaXMuZGlzYWJsZWQgJiYgIWlzVW5kZWZpbmVkKGFzcGVjdFJhdGlvKSkge1xuICAgICAgICAvLyAwIC0+IE5hTlxuICAgICAgICBvcHRpb25zLmFzcGVjdFJhdGlvID0gTWF0aC5tYXgoMCwgYXNwZWN0UmF0aW8pIHx8IE5hTjtcbiAgICAgICAgaWYgKHRoaXMucmVhZHkpIHtcbiAgICAgICAgICB0aGlzLmluaXRDcm9wQm94KCk7XG4gICAgICAgICAgaWYgKHRoaXMuY3JvcHBlZCkge1xuICAgICAgICAgICAgdGhpcy5yZW5kZXJDcm9wQm94KCk7XG4gICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICB9XG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxuICAgIC8qKlxuICAgICAqIENoYW5nZSB0aGUgZHJhZyBtb2RlLlxuICAgICAqIEBwYXJhbSB7c3RyaW5nfSBtb2RlIC0gVGhlIG5ldyBkcmFnIG1vZGUuXG4gICAgICogQHJldHVybnMge0Nyb3BwZXJ9IHRoaXNcbiAgICAgKi9cbiAgICBzZXREcmFnTW9kZTogZnVuY3Rpb24gc2V0RHJhZ01vZGUobW9kZSkge1xuICAgICAgdmFyIG9wdGlvbnMgPSB0aGlzLm9wdGlvbnMsXG4gICAgICAgIGRyYWdCb3ggPSB0aGlzLmRyYWdCb3gsXG4gICAgICAgIGZhY2UgPSB0aGlzLmZhY2U7XG4gICAgICBpZiAodGhpcy5yZWFkeSAmJiAhdGhpcy5kaXNhYmxlZCkge1xuICAgICAgICB2YXIgY3JvcHBhYmxlID0gbW9kZSA9PT0gRFJBR19NT0RFX0NST1A7XG4gICAgICAgIHZhciBtb3ZhYmxlID0gb3B0aW9ucy5tb3ZhYmxlICYmIG1vZGUgPT09IERSQUdfTU9ERV9NT1ZFO1xuICAgICAgICBtb2RlID0gY3JvcHBhYmxlIHx8IG1vdmFibGUgPyBtb2RlIDogRFJBR19NT0RFX05PTkU7XG4gICAgICAgIG9wdGlvbnMuZHJhZ01vZGUgPSBtb2RlO1xuICAgICAgICBzZXREYXRhKGRyYWdCb3gsIERBVEFfQUNUSU9OLCBtb2RlKTtcbiAgICAgICAgdG9nZ2xlQ2xhc3MoZHJhZ0JveCwgQ0xBU1NfQ1JPUCwgY3JvcHBhYmxlKTtcbiAgICAgICAgdG9nZ2xlQ2xhc3MoZHJhZ0JveCwgQ0xBU1NfTU9WRSwgbW92YWJsZSk7XG4gICAgICAgIGlmICghb3B0aW9ucy5jcm9wQm94TW92YWJsZSkge1xuICAgICAgICAgIC8vIFN5bmMgZHJhZyBtb2RlIHRvIGNyb3AgYm94IHdoZW4gaXQgaXMgbm90IG1vdmFibGVcbiAgICAgICAgICBzZXREYXRhKGZhY2UsIERBVEFfQUNUSU9OLCBtb2RlKTtcbiAgICAgICAgICB0b2dnbGVDbGFzcyhmYWNlLCBDTEFTU19DUk9QLCBjcm9wcGFibGUpO1xuICAgICAgICAgIHRvZ2dsZUNsYXNzKGZhY2UsIENMQVNTX01PVkUsIG1vdmFibGUpO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9XG4gIH07XG5cbiAgdmFyIEFub3RoZXJDcm9wcGVyID0gV0lORE9XLkNyb3BwZXI7XG4gIHZhciBDcm9wcGVyID0gLyojX19QVVJFX18qL2Z1bmN0aW9uICgpIHtcbiAgICAvKipcbiAgICAgKiBDcmVhdGUgYSBuZXcgQ3JvcHBlci5cbiAgICAgKiBAcGFyYW0ge0VsZW1lbnR9IGVsZW1lbnQgLSBUaGUgdGFyZ2V0IGVsZW1lbnQgZm9yIGNyb3BwaW5nLlxuICAgICAqIEBwYXJhbSB7T2JqZWN0fSBbb3B0aW9ucz17fV0gLSBUaGUgY29uZmlndXJhdGlvbiBvcHRpb25zLlxuICAgICAqL1xuICAgIGZ1bmN0aW9uIENyb3BwZXIoZWxlbWVudCkge1xuICAgICAgdmFyIG9wdGlvbnMgPSBhcmd1bWVudHMubGVuZ3RoID4gMSAmJiBhcmd1bWVudHNbMV0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1sxXSA6IHt9O1xuICAgICAgX2NsYXNzQ2FsbENoZWNrKHRoaXMsIENyb3BwZXIpO1xuICAgICAgaWYgKCFlbGVtZW50IHx8ICFSRUdFWFBfVEFHX05BTUUudGVzdChlbGVtZW50LnRhZ05hbWUpKSB7XG4gICAgICAgIHRocm93IG5ldyBFcnJvcignVGhlIGZpcnN0IGFyZ3VtZW50IGlzIHJlcXVpcmVkIGFuZCBtdXN0IGJlIGFuIDxpbWc+IG9yIDxjYW52YXM+IGVsZW1lbnQuJyk7XG4gICAgICB9XG4gICAgICB0aGlzLmVsZW1lbnQgPSBlbGVtZW50O1xuICAgICAgdGhpcy5vcHRpb25zID0gYXNzaWduKHt9LCBERUZBVUxUUywgaXNQbGFpbk9iamVjdChvcHRpb25zKSAmJiBvcHRpb25zKTtcbiAgICAgIHRoaXMuY3JvcHBlZCA9IGZhbHNlO1xuICAgICAgdGhpcy5kaXNhYmxlZCA9IGZhbHNlO1xuICAgICAgdGhpcy5wb2ludGVycyA9IHt9O1xuICAgICAgdGhpcy5yZWFkeSA9IGZhbHNlO1xuICAgICAgdGhpcy5yZWxvYWRpbmcgPSBmYWxzZTtcbiAgICAgIHRoaXMucmVwbGFjZWQgPSBmYWxzZTtcbiAgICAgIHRoaXMuc2l6ZWQgPSBmYWxzZTtcbiAgICAgIHRoaXMuc2l6aW5nID0gZmFsc2U7XG4gICAgICB0aGlzLmluaXQoKTtcbiAgICB9XG4gICAgX2NyZWF0ZUNsYXNzKENyb3BwZXIsIFt7XG4gICAgICBrZXk6IFwiaW5pdFwiLFxuICAgICAgdmFsdWU6IGZ1bmN0aW9uIGluaXQoKSB7XG4gICAgICAgIHZhciBlbGVtZW50ID0gdGhpcy5lbGVtZW50O1xuICAgICAgICB2YXIgdGFnTmFtZSA9IGVsZW1lbnQudGFnTmFtZS50b0xvd2VyQ2FzZSgpO1xuICAgICAgICB2YXIgdXJsO1xuICAgICAgICBpZiAoZWxlbWVudFtOQU1FU1BBQ0VdKSB7XG4gICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG4gICAgICAgIGVsZW1lbnRbTkFNRVNQQUNFXSA9IHRoaXM7XG4gICAgICAgIGlmICh0YWdOYW1lID09PSAnaW1nJykge1xuICAgICAgICAgIHRoaXMuaXNJbWcgPSB0cnVlO1xuXG4gICAgICAgICAgLy8gZS5nLjogXCJpbWcvcGljdHVyZS5qcGdcIlxuICAgICAgICAgIHVybCA9IGVsZW1lbnQuZ2V0QXR0cmlidXRlKCdzcmMnKSB8fCAnJztcbiAgICAgICAgICB0aGlzLm9yaWdpbmFsVXJsID0gdXJsO1xuXG4gICAgICAgICAgLy8gU3RvcCB3aGVuIGl0J3MgYSBibGFuayBpbWFnZVxuICAgICAgICAgIGlmICghdXJsKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgfVxuXG4gICAgICAgICAgLy8gZS5nLjogXCJodHRwczovL2V4YW1wbGUuY29tL2ltZy9waWN0dXJlLmpwZ1wiXG4gICAgICAgICAgdXJsID0gZWxlbWVudC5zcmM7XG4gICAgICAgIH0gZWxzZSBpZiAodGFnTmFtZSA9PT0gJ2NhbnZhcycgJiYgd2luZG93LkhUTUxDYW52YXNFbGVtZW50KSB7XG4gICAgICAgICAgdXJsID0gZWxlbWVudC50b0RhdGFVUkwoKTtcbiAgICAgICAgfVxuICAgICAgICB0aGlzLmxvYWQodXJsKTtcbiAgICAgIH1cbiAgICB9LCB7XG4gICAgICBrZXk6IFwibG9hZFwiLFxuICAgICAgdmFsdWU6IGZ1bmN0aW9uIGxvYWQodXJsKSB7XG4gICAgICAgIHZhciBfdGhpcyA9IHRoaXM7XG4gICAgICAgIGlmICghdXJsKSB7XG4gICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG4gICAgICAgIHRoaXMudXJsID0gdXJsO1xuICAgICAgICB0aGlzLmltYWdlRGF0YSA9IHt9O1xuICAgICAgICB2YXIgZWxlbWVudCA9IHRoaXMuZWxlbWVudCxcbiAgICAgICAgICBvcHRpb25zID0gdGhpcy5vcHRpb25zO1xuICAgICAgICBpZiAoIW9wdGlvbnMucm90YXRhYmxlICYmICFvcHRpb25zLnNjYWxhYmxlKSB7XG4gICAgICAgICAgb3B0aW9ucy5jaGVja09yaWVudGF0aW9uID0gZmFsc2U7XG4gICAgICAgIH1cblxuICAgICAgICAvLyBPbmx5IElFMTArIHN1cHBvcnRzIFR5cGVkIEFycmF5c1xuICAgICAgICBpZiAoIW9wdGlvbnMuY2hlY2tPcmllbnRhdGlvbiB8fCAhd2luZG93LkFycmF5QnVmZmVyKSB7XG4gICAgICAgICAgdGhpcy5jbG9uZSgpO1xuICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIERldGVjdCB0aGUgbWltZSB0eXBlIG9mIHRoZSBpbWFnZSBkaXJlY3RseSBpZiBpdCBpcyBhIERhdGEgVVJMXG4gICAgICAgIGlmIChSRUdFWFBfREFUQV9VUkwudGVzdCh1cmwpKSB7XG4gICAgICAgICAgLy8gUmVhZCBBcnJheUJ1ZmZlciBmcm9tIERhdGEgVVJMIG9mIEpQRUcgaW1hZ2VzIGRpcmVjdGx5IGZvciBiZXR0ZXIgcGVyZm9ybWFuY2VcbiAgICAgICAgICBpZiAoUkVHRVhQX0RBVEFfVVJMX0pQRUcudGVzdCh1cmwpKSB7XG4gICAgICAgICAgICB0aGlzLnJlYWQoZGF0YVVSTFRvQXJyYXlCdWZmZXIodXJsKSk7XG4gICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIC8vIE9ubHkgYSBKUEVHIGltYWdlIG1heSBjb250YWlucyBFeGlmIE9yaWVudGF0aW9uIGluZm9ybWF0aW9uLFxuICAgICAgICAgICAgLy8gdGhlIHJlc3QgdHlwZXMgb2YgRGF0YSBVUkxzIGFyZSBub3QgbmVjZXNzYXJ5IHRvIGNoZWNrIG9yaWVudGF0aW9uIGF0IGFsbC5cbiAgICAgICAgICAgIHRoaXMuY2xvbmUoKTtcbiAgICAgICAgICB9XG4gICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gMS4gRGV0ZWN0IHRoZSBtaW1lIHR5cGUgb2YgdGhlIGltYWdlIGJ5IGEgWE1MSHR0cFJlcXVlc3QuXG4gICAgICAgIC8vIDIuIExvYWQgdGhlIGltYWdlIGFzIEFycmF5QnVmZmVyIGZvciByZWFkaW5nIG9yaWVudGF0aW9uIGlmIGl0cyBhIEpQRUcgaW1hZ2UuXG4gICAgICAgIHZhciB4aHIgPSBuZXcgWE1MSHR0cFJlcXVlc3QoKTtcbiAgICAgICAgdmFyIGNsb25lID0gdGhpcy5jbG9uZS5iaW5kKHRoaXMpO1xuICAgICAgICB0aGlzLnJlbG9hZGluZyA9IHRydWU7XG4gICAgICAgIHRoaXMueGhyID0geGhyO1xuXG4gICAgICAgIC8vIDEuIENyb3NzIG9yaWdpbiByZXF1ZXN0cyBhcmUgb25seSBzdXBwb3J0ZWQgZm9yIHByb3RvY29sIHNjaGVtZXM6XG4gICAgICAgIC8vIGh0dHAsIGh0dHBzLCBkYXRhLCBjaHJvbWUsIGNocm9tZS1leHRlbnNpb24uXG4gICAgICAgIC8vIDIuIEFjY2VzcyB0byBYTUxIdHRwUmVxdWVzdCBmcm9tIGEgRGF0YSBVUkwgd2lsbCBiZSBibG9ja2VkIGJ5IENPUlMgcG9saWN5XG4gICAgICAgIC8vIGluIHNvbWUgYnJvd3NlcnMgYXMgSUUxMSBhbmQgU2FmYXJpLlxuICAgICAgICB4aHIub25hYm9ydCA9IGNsb25lO1xuICAgICAgICB4aHIub25lcnJvciA9IGNsb25lO1xuICAgICAgICB4aHIub250aW1lb3V0ID0gY2xvbmU7XG4gICAgICAgIHhoci5vbnByb2dyZXNzID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgIC8vIEFib3J0IHRoZSByZXF1ZXN0IGRpcmVjdGx5IGlmIGl0IG5vdCBhIEpQRUcgaW1hZ2UgZm9yIGJldHRlciBwZXJmb3JtYW5jZVxuICAgICAgICAgIGlmICh4aHIuZ2V0UmVzcG9uc2VIZWFkZXIoJ2NvbnRlbnQtdHlwZScpICE9PSBNSU1FX1RZUEVfSlBFRykge1xuICAgICAgICAgICAgeGhyLmFib3J0KCk7XG4gICAgICAgICAgfVxuICAgICAgICB9O1xuICAgICAgICB4aHIub25sb2FkID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgIF90aGlzLnJlYWQoeGhyLnJlc3BvbnNlKTtcbiAgICAgICAgfTtcbiAgICAgICAgeGhyLm9ubG9hZGVuZCA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICBfdGhpcy5yZWxvYWRpbmcgPSBmYWxzZTtcbiAgICAgICAgICBfdGhpcy54aHIgPSBudWxsO1xuICAgICAgICB9O1xuXG4gICAgICAgIC8vIEJ1c3QgY2FjaGUgd2hlbiB0aGVyZSBpcyBhIFwiY3Jvc3NPcmlnaW5cIiBwcm9wZXJ0eSB0byBhdm9pZCBicm93c2VyIGNhY2hlIGVycm9yXG4gICAgICAgIGlmIChvcHRpb25zLmNoZWNrQ3Jvc3NPcmlnaW4gJiYgaXNDcm9zc09yaWdpblVSTCh1cmwpICYmIGVsZW1lbnQuY3Jvc3NPcmlnaW4pIHtcbiAgICAgICAgICB1cmwgPSBhZGRUaW1lc3RhbXAodXJsKTtcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIFRoZSB0aGlyZCBwYXJhbWV0ZXIgaXMgcmVxdWlyZWQgZm9yIGF2b2lkaW5nIHNpZGUtZWZmZWN0ICgjNjgyKVxuICAgICAgICB4aHIub3BlbignR0VUJywgdXJsLCB0cnVlKTtcbiAgICAgICAgeGhyLnJlc3BvbnNlVHlwZSA9ICdhcnJheWJ1ZmZlcic7XG4gICAgICAgIHhoci53aXRoQ3JlZGVudGlhbHMgPSBlbGVtZW50LmNyb3NzT3JpZ2luID09PSAndXNlLWNyZWRlbnRpYWxzJztcbiAgICAgICAgeGhyLnNlbmQoKTtcbiAgICAgIH1cbiAgICB9LCB7XG4gICAgICBrZXk6IFwicmVhZFwiLFxuICAgICAgdmFsdWU6IGZ1bmN0aW9uIHJlYWQoYXJyYXlCdWZmZXIpIHtcbiAgICAgICAgdmFyIG9wdGlvbnMgPSB0aGlzLm9wdGlvbnMsXG4gICAgICAgICAgaW1hZ2VEYXRhID0gdGhpcy5pbWFnZURhdGE7XG5cbiAgICAgICAgLy8gUmVzZXQgdGhlIG9yaWVudGF0aW9uIHZhbHVlIHRvIGl0cyBkZWZhdWx0IHZhbHVlIDFcbiAgICAgICAgLy8gYXMgc29tZSBpT1MgYnJvd3NlcnMgd2lsbCByZW5kZXIgaW1hZ2Ugd2l0aCBpdHMgb3JpZW50YXRpb25cbiAgICAgICAgdmFyIG9yaWVudGF0aW9uID0gcmVzZXRBbmRHZXRPcmllbnRhdGlvbihhcnJheUJ1ZmZlcik7XG4gICAgICAgIHZhciByb3RhdGUgPSAwO1xuICAgICAgICB2YXIgc2NhbGVYID0gMTtcbiAgICAgICAgdmFyIHNjYWxlWSA9IDE7XG4gICAgICAgIGlmIChvcmllbnRhdGlvbiA+IDEpIHtcbiAgICAgICAgICAvLyBHZW5lcmF0ZSBhIG5ldyBVUkwgd2hpY2ggaGFzIHRoZSBkZWZhdWx0IG9yaWVudGF0aW9uIHZhbHVlXG4gICAgICAgICAgdGhpcy51cmwgPSBhcnJheUJ1ZmZlclRvRGF0YVVSTChhcnJheUJ1ZmZlciwgTUlNRV9UWVBFX0pQRUcpO1xuICAgICAgICAgIHZhciBfcGFyc2VPcmllbnRhdGlvbiA9IHBhcnNlT3JpZW50YXRpb24ob3JpZW50YXRpb24pO1xuICAgICAgICAgIHJvdGF0ZSA9IF9wYXJzZU9yaWVudGF0aW9uLnJvdGF0ZTtcbiAgICAgICAgICBzY2FsZVggPSBfcGFyc2VPcmllbnRhdGlvbi5zY2FsZVg7XG4gICAgICAgICAgc2NhbGVZID0gX3BhcnNlT3JpZW50YXRpb24uc2NhbGVZO1xuICAgICAgICB9XG4gICAgICAgIGlmIChvcHRpb25zLnJvdGF0YWJsZSkge1xuICAgICAgICAgIGltYWdlRGF0YS5yb3RhdGUgPSByb3RhdGU7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKG9wdGlvbnMuc2NhbGFibGUpIHtcbiAgICAgICAgICBpbWFnZURhdGEuc2NhbGVYID0gc2NhbGVYO1xuICAgICAgICAgIGltYWdlRGF0YS5zY2FsZVkgPSBzY2FsZVk7XG4gICAgICAgIH1cbiAgICAgICAgdGhpcy5jbG9uZSgpO1xuICAgICAgfVxuICAgIH0sIHtcbiAgICAgIGtleTogXCJjbG9uZVwiLFxuICAgICAgdmFsdWU6IGZ1bmN0aW9uIGNsb25lKCkge1xuICAgICAgICB2YXIgZWxlbWVudCA9IHRoaXMuZWxlbWVudCxcbiAgICAgICAgICB1cmwgPSB0aGlzLnVybDtcbiAgICAgICAgdmFyIGNyb3NzT3JpZ2luID0gZWxlbWVudC5jcm9zc09yaWdpbjtcbiAgICAgICAgdmFyIGNyb3NzT3JpZ2luVXJsID0gdXJsO1xuICAgICAgICBpZiAodGhpcy5vcHRpb25zLmNoZWNrQ3Jvc3NPcmlnaW4gJiYgaXNDcm9zc09yaWdpblVSTCh1cmwpKSB7XG4gICAgICAgICAgaWYgKCFjcm9zc09yaWdpbikge1xuICAgICAgICAgICAgY3Jvc3NPcmlnaW4gPSAnYW5vbnltb3VzJztcbiAgICAgICAgICB9XG5cbiAgICAgICAgICAvLyBCdXN0IGNhY2hlIHdoZW4gdGhlcmUgaXMgbm90IGEgXCJjcm9zc09yaWdpblwiIHByb3BlcnR5ICgjNTE5KVxuICAgICAgICAgIGNyb3NzT3JpZ2luVXJsID0gYWRkVGltZXN0YW1wKHVybCk7XG4gICAgICAgIH1cbiAgICAgICAgdGhpcy5jcm9zc09yaWdpbiA9IGNyb3NzT3JpZ2luO1xuICAgICAgICB0aGlzLmNyb3NzT3JpZ2luVXJsID0gY3Jvc3NPcmlnaW5Vcmw7XG4gICAgICAgIHZhciBpbWFnZSA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2ltZycpO1xuICAgICAgICBpZiAoY3Jvc3NPcmlnaW4pIHtcbiAgICAgICAgICBpbWFnZS5jcm9zc09yaWdpbiA9IGNyb3NzT3JpZ2luO1xuICAgICAgICB9XG4gICAgICAgIGltYWdlLnNyYyA9IGNyb3NzT3JpZ2luVXJsIHx8IHVybDtcbiAgICAgICAgaW1hZ2UuYWx0ID0gZWxlbWVudC5hbHQgfHwgJ1RoZSBpbWFnZSB0byBjcm9wJztcbiAgICAgICAgdGhpcy5pbWFnZSA9IGltYWdlO1xuICAgICAgICBpbWFnZS5vbmxvYWQgPSB0aGlzLnN0YXJ0LmJpbmQodGhpcyk7XG4gICAgICAgIGltYWdlLm9uZXJyb3IgPSB0aGlzLnN0b3AuYmluZCh0aGlzKTtcbiAgICAgICAgYWRkQ2xhc3MoaW1hZ2UsIENMQVNTX0hJREUpO1xuICAgICAgICBlbGVtZW50LnBhcmVudE5vZGUuaW5zZXJ0QmVmb3JlKGltYWdlLCBlbGVtZW50Lm5leHRTaWJsaW5nKTtcbiAgICAgIH1cbiAgICB9LCB7XG4gICAgICBrZXk6IFwic3RhcnRcIixcbiAgICAgIHZhbHVlOiBmdW5jdGlvbiBzdGFydCgpIHtcbiAgICAgICAgdmFyIF90aGlzMiA9IHRoaXM7XG4gICAgICAgIHZhciBpbWFnZSA9IHRoaXMuaW1hZ2U7XG4gICAgICAgIGltYWdlLm9ubG9hZCA9IG51bGw7XG4gICAgICAgIGltYWdlLm9uZXJyb3IgPSBudWxsO1xuICAgICAgICB0aGlzLnNpemluZyA9IHRydWU7XG5cbiAgICAgICAgLy8gTWF0Y2ggYWxsIGJyb3dzZXJzIHRoYXQgdXNlIFdlYktpdCBhcyB0aGUgbGF5b3V0IGVuZ2luZSBpbiBpT1MgZGV2aWNlcyxcbiAgICAgICAgLy8gc3VjaCBhcyBTYWZhcmkgZm9yIGlPUywgQ2hyb21lIGZvciBpT1MsIGFuZCBpbi1hcHAgYnJvd3NlcnMuXG4gICAgICAgIHZhciBpc0lPU1dlYktpdCA9IFdJTkRPVy5uYXZpZ2F0b3IgJiYgLyg/OmlQYWR8aVBob25lfGlQb2QpLio/QXBwbGVXZWJLaXQvaS50ZXN0KFdJTkRPVy5uYXZpZ2F0b3IudXNlckFnZW50KTtcbiAgICAgICAgdmFyIGRvbmUgPSBmdW5jdGlvbiBkb25lKG5hdHVyYWxXaWR0aCwgbmF0dXJhbEhlaWdodCkge1xuICAgICAgICAgIGFzc2lnbihfdGhpczIuaW1hZ2VEYXRhLCB7XG4gICAgICAgICAgICBuYXR1cmFsV2lkdGg6IG5hdHVyYWxXaWR0aCxcbiAgICAgICAgICAgIG5hdHVyYWxIZWlnaHQ6IG5hdHVyYWxIZWlnaHQsXG4gICAgICAgICAgICBhc3BlY3RSYXRpbzogbmF0dXJhbFdpZHRoIC8gbmF0dXJhbEhlaWdodFxuICAgICAgICAgIH0pO1xuICAgICAgICAgIF90aGlzMi5pbml0aWFsSW1hZ2VEYXRhID0gYXNzaWduKHt9LCBfdGhpczIuaW1hZ2VEYXRhKTtcbiAgICAgICAgICBfdGhpczIuc2l6aW5nID0gZmFsc2U7XG4gICAgICAgICAgX3RoaXMyLnNpemVkID0gdHJ1ZTtcbiAgICAgICAgICBfdGhpczIuYnVpbGQoKTtcbiAgICAgICAgfTtcblxuICAgICAgICAvLyBNb3N0IG1vZGVybiBicm93c2VycyAoZXhjZXB0cyBpT1MgV2ViS2l0KVxuICAgICAgICBpZiAoaW1hZ2UubmF0dXJhbFdpZHRoICYmICFpc0lPU1dlYktpdCkge1xuICAgICAgICAgIGRvbmUoaW1hZ2UubmF0dXJhbFdpZHRoLCBpbWFnZS5uYXR1cmFsSGVpZ2h0KTtcbiAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cbiAgICAgICAgdmFyIHNpemluZ0ltYWdlID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnaW1nJyk7XG4gICAgICAgIHZhciBib2R5ID0gZG9jdW1lbnQuYm9keSB8fCBkb2N1bWVudC5kb2N1bWVudEVsZW1lbnQ7XG4gICAgICAgIHRoaXMuc2l6aW5nSW1hZ2UgPSBzaXppbmdJbWFnZTtcbiAgICAgICAgc2l6aW5nSW1hZ2Uub25sb2FkID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgIGRvbmUoc2l6aW5nSW1hZ2Uud2lkdGgsIHNpemluZ0ltYWdlLmhlaWdodCk7XG4gICAgICAgICAgaWYgKCFpc0lPU1dlYktpdCkge1xuICAgICAgICAgICAgYm9keS5yZW1vdmVDaGlsZChzaXppbmdJbWFnZSk7XG4gICAgICAgICAgfVxuICAgICAgICB9O1xuICAgICAgICBzaXppbmdJbWFnZS5zcmMgPSBpbWFnZS5zcmM7XG5cbiAgICAgICAgLy8gaU9TIFdlYktpdCB3aWxsIGNvbnZlcnQgdGhlIGltYWdlIGF1dG9tYXRpY2FsbHlcbiAgICAgICAgLy8gd2l0aCBpdHMgb3JpZW50YXRpb24gb25jZSBhcHBlbmQgaXQgaW50byBET00gKCMyNzkpXG4gICAgICAgIGlmICghaXNJT1NXZWJLaXQpIHtcbiAgICAgICAgICBzaXppbmdJbWFnZS5zdHlsZS5jc3NUZXh0ID0gJ2xlZnQ6MDsnICsgJ21heC1oZWlnaHQ6bm9uZSFpbXBvcnRhbnQ7JyArICdtYXgtd2lkdGg6bm9uZSFpbXBvcnRhbnQ7JyArICdtaW4taGVpZ2h0OjAhaW1wb3J0YW50OycgKyAnbWluLXdpZHRoOjAhaW1wb3J0YW50OycgKyAnb3BhY2l0eTowOycgKyAncG9zaXRpb246YWJzb2x1dGU7JyArICd0b3A6MDsnICsgJ3otaW5kZXg6LTE7JztcbiAgICAgICAgICBib2R5LmFwcGVuZENoaWxkKHNpemluZ0ltYWdlKTtcbiAgICAgICAgfVxuICAgICAgfVxuICAgIH0sIHtcbiAgICAgIGtleTogXCJzdG9wXCIsXG4gICAgICB2YWx1ZTogZnVuY3Rpb24gc3RvcCgpIHtcbiAgICAgICAgdmFyIGltYWdlID0gdGhpcy5pbWFnZTtcbiAgICAgICAgaW1hZ2Uub25sb2FkID0gbnVsbDtcbiAgICAgICAgaW1hZ2Uub25lcnJvciA9IG51bGw7XG4gICAgICAgIGltYWdlLnBhcmVudE5vZGUucmVtb3ZlQ2hpbGQoaW1hZ2UpO1xuICAgICAgICB0aGlzLmltYWdlID0gbnVsbDtcbiAgICAgIH1cbiAgICB9LCB7XG4gICAgICBrZXk6IFwiYnVpbGRcIixcbiAgICAgIHZhbHVlOiBmdW5jdGlvbiBidWlsZCgpIHtcbiAgICAgICAgaWYgKCF0aGlzLnNpemVkIHx8IHRoaXMucmVhZHkpIHtcbiAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cbiAgICAgICAgdmFyIGVsZW1lbnQgPSB0aGlzLmVsZW1lbnQsXG4gICAgICAgICAgb3B0aW9ucyA9IHRoaXMub3B0aW9ucyxcbiAgICAgICAgICBpbWFnZSA9IHRoaXMuaW1hZ2U7XG5cbiAgICAgICAgLy8gQ3JlYXRlIGNyb3BwZXIgZWxlbWVudHNcbiAgICAgICAgdmFyIGNvbnRhaW5lciA9IGVsZW1lbnQucGFyZW50Tm9kZTtcbiAgICAgICAgdmFyIHRlbXBsYXRlID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2Jyk7XG4gICAgICAgIHRlbXBsYXRlLmlubmVySFRNTCA9IFRFTVBMQVRFO1xuICAgICAgICB2YXIgY3JvcHBlciA9IHRlbXBsYXRlLnF1ZXJ5U2VsZWN0b3IoXCIuXCIuY29uY2F0KE5BTUVTUEFDRSwgXCItY29udGFpbmVyXCIpKTtcbiAgICAgICAgdmFyIGNhbnZhcyA9IGNyb3BwZXIucXVlcnlTZWxlY3RvcihcIi5cIi5jb25jYXQoTkFNRVNQQUNFLCBcIi1jYW52YXNcIikpO1xuICAgICAgICB2YXIgZHJhZ0JveCA9IGNyb3BwZXIucXVlcnlTZWxlY3RvcihcIi5cIi5jb25jYXQoTkFNRVNQQUNFLCBcIi1kcmFnLWJveFwiKSk7XG4gICAgICAgIHZhciBjcm9wQm94ID0gY3JvcHBlci5xdWVyeVNlbGVjdG9yKFwiLlwiLmNvbmNhdChOQU1FU1BBQ0UsIFwiLWNyb3AtYm94XCIpKTtcbiAgICAgICAgdmFyIGZhY2UgPSBjcm9wQm94LnF1ZXJ5U2VsZWN0b3IoXCIuXCIuY29uY2F0KE5BTUVTUEFDRSwgXCItZmFjZVwiKSk7XG4gICAgICAgIHRoaXMuY29udGFpbmVyID0gY29udGFpbmVyO1xuICAgICAgICB0aGlzLmNyb3BwZXIgPSBjcm9wcGVyO1xuICAgICAgICB0aGlzLmNhbnZhcyA9IGNhbnZhcztcbiAgICAgICAgdGhpcy5kcmFnQm94ID0gZHJhZ0JveDtcbiAgICAgICAgdGhpcy5jcm9wQm94ID0gY3JvcEJveDtcbiAgICAgICAgdGhpcy52aWV3Qm94ID0gY3JvcHBlci5xdWVyeVNlbGVjdG9yKFwiLlwiLmNvbmNhdChOQU1FU1BBQ0UsIFwiLXZpZXctYm94XCIpKTtcbiAgICAgICAgdGhpcy5mYWNlID0gZmFjZTtcbiAgICAgICAgY2FudmFzLmFwcGVuZENoaWxkKGltYWdlKTtcblxuICAgICAgICAvLyBIaWRlIHRoZSBvcmlnaW5hbCBpbWFnZVxuICAgICAgICBhZGRDbGFzcyhlbGVtZW50LCBDTEFTU19ISURERU4pO1xuXG4gICAgICAgIC8vIEluc2VydHMgdGhlIGNyb3BwZXIgYWZ0ZXIgdG8gdGhlIGN1cnJlbnQgaW1hZ2VcbiAgICAgICAgY29udGFpbmVyLmluc2VydEJlZm9yZShjcm9wcGVyLCBlbGVtZW50Lm5leHRTaWJsaW5nKTtcblxuICAgICAgICAvLyBTaG93IHRoZSBoaWRkZW4gaW1hZ2VcbiAgICAgICAgcmVtb3ZlQ2xhc3MoaW1hZ2UsIENMQVNTX0hJREUpO1xuICAgICAgICB0aGlzLmluaXRQcmV2aWV3KCk7XG4gICAgICAgIHRoaXMuYmluZCgpO1xuICAgICAgICBvcHRpb25zLmluaXRpYWxBc3BlY3RSYXRpbyA9IE1hdGgubWF4KDAsIG9wdGlvbnMuaW5pdGlhbEFzcGVjdFJhdGlvKSB8fCBOYU47XG4gICAgICAgIG9wdGlvbnMuYXNwZWN0UmF0aW8gPSBNYXRoLm1heCgwLCBvcHRpb25zLmFzcGVjdFJhdGlvKSB8fCBOYU47XG4gICAgICAgIG9wdGlvbnMudmlld01vZGUgPSBNYXRoLm1heCgwLCBNYXRoLm1pbigzLCBNYXRoLnJvdW5kKG9wdGlvbnMudmlld01vZGUpKSkgfHwgMDtcbiAgICAgICAgYWRkQ2xhc3MoY3JvcEJveCwgQ0xBU1NfSElEREVOKTtcbiAgICAgICAgaWYgKCFvcHRpb25zLmd1aWRlcykge1xuICAgICAgICAgIGFkZENsYXNzKGNyb3BCb3guZ2V0RWxlbWVudHNCeUNsYXNzTmFtZShcIlwiLmNvbmNhdChOQU1FU1BBQ0UsIFwiLWRhc2hlZFwiKSksIENMQVNTX0hJRERFTik7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKCFvcHRpb25zLmNlbnRlcikge1xuICAgICAgICAgIGFkZENsYXNzKGNyb3BCb3guZ2V0RWxlbWVudHNCeUNsYXNzTmFtZShcIlwiLmNvbmNhdChOQU1FU1BBQ0UsIFwiLWNlbnRlclwiKSksIENMQVNTX0hJRERFTik7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKG9wdGlvbnMuYmFja2dyb3VuZCkge1xuICAgICAgICAgIGFkZENsYXNzKGNyb3BwZXIsIFwiXCIuY29uY2F0KE5BTUVTUEFDRSwgXCItYmdcIikpO1xuICAgICAgICB9XG4gICAgICAgIGlmICghb3B0aW9ucy5oaWdobGlnaHQpIHtcbiAgICAgICAgICBhZGRDbGFzcyhmYWNlLCBDTEFTU19JTlZJU0lCTEUpO1xuICAgICAgICB9XG4gICAgICAgIGlmIChvcHRpb25zLmNyb3BCb3hNb3ZhYmxlKSB7XG4gICAgICAgICAgYWRkQ2xhc3MoZmFjZSwgQ0xBU1NfTU9WRSk7XG4gICAgICAgICAgc2V0RGF0YShmYWNlLCBEQVRBX0FDVElPTiwgQUNUSU9OX0FMTCk7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKCFvcHRpb25zLmNyb3BCb3hSZXNpemFibGUpIHtcbiAgICAgICAgICBhZGRDbGFzcyhjcm9wQm94LmdldEVsZW1lbnRzQnlDbGFzc05hbWUoXCJcIi5jb25jYXQoTkFNRVNQQUNFLCBcIi1saW5lXCIpKSwgQ0xBU1NfSElEREVOKTtcbiAgICAgICAgICBhZGRDbGFzcyhjcm9wQm94LmdldEVsZW1lbnRzQnlDbGFzc05hbWUoXCJcIi5jb25jYXQoTkFNRVNQQUNFLCBcIi1wb2ludFwiKSksIENMQVNTX0hJRERFTik7XG4gICAgICAgIH1cbiAgICAgICAgdGhpcy5yZW5kZXIoKTtcbiAgICAgICAgdGhpcy5yZWFkeSA9IHRydWU7XG4gICAgICAgIHRoaXMuc2V0RHJhZ01vZGUob3B0aW9ucy5kcmFnTW9kZSk7XG4gICAgICAgIGlmIChvcHRpb25zLmF1dG9Dcm9wKSB7XG4gICAgICAgICAgdGhpcy5jcm9wKCk7XG4gICAgICAgIH1cbiAgICAgICAgdGhpcy5zZXREYXRhKG9wdGlvbnMuZGF0YSk7XG4gICAgICAgIGlmIChpc0Z1bmN0aW9uKG9wdGlvbnMucmVhZHkpKSB7XG4gICAgICAgICAgYWRkTGlzdGVuZXIoZWxlbWVudCwgRVZFTlRfUkVBRFksIG9wdGlvbnMucmVhZHksIHtcbiAgICAgICAgICAgIG9uY2U6IHRydWVcbiAgICAgICAgICB9KTtcbiAgICAgICAgfVxuICAgICAgICBkaXNwYXRjaEV2ZW50KGVsZW1lbnQsIEVWRU5UX1JFQURZKTtcbiAgICAgIH1cbiAgICB9LCB7XG4gICAgICBrZXk6IFwidW5idWlsZFwiLFxuICAgICAgdmFsdWU6IGZ1bmN0aW9uIHVuYnVpbGQoKSB7XG4gICAgICAgIGlmICghdGhpcy5yZWFkeSkge1xuICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuICAgICAgICB0aGlzLnJlYWR5ID0gZmFsc2U7XG4gICAgICAgIHRoaXMudW5iaW5kKCk7XG4gICAgICAgIHRoaXMucmVzZXRQcmV2aWV3KCk7XG4gICAgICAgIHZhciBwYXJlbnROb2RlID0gdGhpcy5jcm9wcGVyLnBhcmVudE5vZGU7XG4gICAgICAgIGlmIChwYXJlbnROb2RlKSB7XG4gICAgICAgICAgcGFyZW50Tm9kZS5yZW1vdmVDaGlsZCh0aGlzLmNyb3BwZXIpO1xuICAgICAgICB9XG4gICAgICAgIHJlbW92ZUNsYXNzKHRoaXMuZWxlbWVudCwgQ0xBU1NfSElEREVOKTtcbiAgICAgIH1cbiAgICB9LCB7XG4gICAgICBrZXk6IFwidW5jcmVhdGVcIixcbiAgICAgIHZhbHVlOiBmdW5jdGlvbiB1bmNyZWF0ZSgpIHtcbiAgICAgICAgaWYgKHRoaXMucmVhZHkpIHtcbiAgICAgICAgICB0aGlzLnVuYnVpbGQoKTtcbiAgICAgICAgICB0aGlzLnJlYWR5ID0gZmFsc2U7XG4gICAgICAgICAgdGhpcy5jcm9wcGVkID0gZmFsc2U7XG4gICAgICAgIH0gZWxzZSBpZiAodGhpcy5zaXppbmcpIHtcbiAgICAgICAgICB0aGlzLnNpemluZ0ltYWdlLm9ubG9hZCA9IG51bGw7XG4gICAgICAgICAgdGhpcy5zaXppbmcgPSBmYWxzZTtcbiAgICAgICAgICB0aGlzLnNpemVkID0gZmFsc2U7XG4gICAgICAgIH0gZWxzZSBpZiAodGhpcy5yZWxvYWRpbmcpIHtcbiAgICAgICAgICB0aGlzLnhoci5vbmFib3J0ID0gbnVsbDtcbiAgICAgICAgICB0aGlzLnhoci5hYm9ydCgpO1xuICAgICAgICB9IGVsc2UgaWYgKHRoaXMuaW1hZ2UpIHtcbiAgICAgICAgICB0aGlzLnN0b3AoKTtcbiAgICAgICAgfVxuICAgICAgfVxuXG4gICAgICAvKipcbiAgICAgICAqIEdldCB0aGUgbm8gY29uZmxpY3QgY3JvcHBlciBjbGFzcy5cbiAgICAgICAqIEByZXR1cm5zIHtDcm9wcGVyfSBUaGUgY3JvcHBlciBjbGFzcy5cbiAgICAgICAqL1xuICAgIH1dLCBbe1xuICAgICAga2V5OiBcIm5vQ29uZmxpY3RcIixcbiAgICAgIHZhbHVlOiBmdW5jdGlvbiBub0NvbmZsaWN0KCkge1xuICAgICAgICB3aW5kb3cuQ3JvcHBlciA9IEFub3RoZXJDcm9wcGVyO1xuICAgICAgICByZXR1cm4gQ3JvcHBlcjtcbiAgICAgIH1cblxuICAgICAgLyoqXG4gICAgICAgKiBDaGFuZ2UgdGhlIGRlZmF1bHQgb3B0aW9ucy5cbiAgICAgICAqIEBwYXJhbSB7T2JqZWN0fSBvcHRpb25zIC0gVGhlIG5ldyBkZWZhdWx0IG9wdGlvbnMuXG4gICAgICAgKi9cbiAgICB9LCB7XG4gICAgICBrZXk6IFwic2V0RGVmYXVsdHNcIixcbiAgICAgIHZhbHVlOiBmdW5jdGlvbiBzZXREZWZhdWx0cyhvcHRpb25zKSB7XG4gICAgICAgIGFzc2lnbihERUZBVUxUUywgaXNQbGFpbk9iamVjdChvcHRpb25zKSAmJiBvcHRpb25zKTtcbiAgICAgIH1cbiAgICB9XSk7XG4gICAgcmV0dXJuIENyb3BwZXI7XG4gIH0oKTtcbiAgYXNzaWduKENyb3BwZXIucHJvdG90eXBlLCByZW5kZXIsIHByZXZpZXcsIGV2ZW50cywgaGFuZGxlcnMsIGNoYW5nZSwgbWV0aG9kcyk7XG5cbiAgcmV0dXJuIENyb3BwZXI7XG5cbn0pKTtcbiIsICJpbXBvcnQgQ3JvcHBlciBmcm9tICdjcm9wcGVyanMnXG5cbmRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoXCJhbHBpbmU6aW5pdFwiLCAoKSA9PiB7XG4gICAgQWxwaW5lLmRhdGEoJ2N1cmF0b3InLCAoe1xuICAgICAgICBzdGF0ZVBhdGgsXG4gICAgICAgIHR5cGVzLFxuICAgICAgICBpbml0aWFsU2VsZWN0aW9uID0gbnVsbFxuICAgIH0pID0+ICh7XG4gICAgICAgIHN0YXRlUGF0aCxcbiAgICAgICAgdHlwZXMsXG4gICAgICAgIHNlbGVjdGVkOiBbXSxcbiAgICAgICAgZmlsZXM6IFtdLFxuICAgICAgICBuZXh0UGFnZVVybDogbnVsbCxcbiAgICAgICAgaXNGZXRjaGluZzogZmFsc2UsXG4gICAgICAgIHNob3dFZGl0Rm9ybTogZmFsc2UsXG4gICAgICAgIHNob3dVcGxvYWRGb3JtOiB0cnVlLFxuICAgICAgICBhc3luYyBpbml0KCkge1xuICAgICAgICAgICAgYXdhaXQgdGhpcy5nZXRGaWxlcygnL2N1cmF0b3IvbWVkaWEnLCBpbml0aWFsU2VsZWN0aW9uKTtcbiAgICAgICAgICAgIGNvbnN0IG9ic2VydmVyID0gbmV3IEludGVyc2VjdGlvbk9ic2VydmVyKFxuICAgICAgICAgICAgICAgIChbZV0pID0+IHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKGUuaXNJbnRlcnNlY3RpbmcpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMubG9hZE1vcmVGaWxlcygpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIHJvb3RNYXJnaW46ICcwcHgnLFxuICAgICAgICAgICAgICAgICAgICB0aHJlc2hvbGQ6IFswXSxcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICApO1xuICAgICAgICAgICAgb2JzZXJ2ZXIub2JzZXJ2ZSh0aGlzLiRyZWZzLmxvYWRNb3JlKTtcblxuICAgICAgICAgICAgdGhpcy4kd2F0Y2goJ3NlbGVjdGVkJywgKHZhbHVlKSA9PiB7XG4gICAgICAgICAgICAgICAgaWYgKHZhbHVlLmxlbmd0aCA9PT0gMSkge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLiR3aXJlLnNldFNlbGVjdGlvbih2YWx1ZSk7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuc2hvd0VkaXRGb3JtID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5zaG93VXBsb2FkRm9ybSA9IGZhbHNlO1xuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAodmFsdWUubGVuZ3RoID4gMSkge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnNob3dFZGl0Rm9ybSA9IGZhbHNlO1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnNob3dVcGxvYWRGb3JtID0gZmFsc2U7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5zaG93RWRpdEZvcm0gPSBmYWxzZTtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5zaG93VXBsb2FkRm9ybSA9IHRydWU7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIGlmIChpbml0aWFsU2VsZWN0aW9uPy5sZW5ndGggPiAwKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5zZWxlY3RlZCA9IGluaXRpYWxTZWxlY3Rpb247XG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG4gICAgICAgIGdldEZpbGVzOiBhc3luYyBmdW5jdGlvbih1cmwgPSAnL2N1cmF0b3IvbWVkaWEnLCBzZWxlY3RlZCA9IFtdKSB7XG4gICAgICAgICAgICBpZiAoc2VsZWN0ZWQubGVuZ3RoID4gMCkge1xuICAgICAgICAgICAgICAgIGxldCBpbmRpY2F0b3IgPSB1cmwuaW5jbHVkZXMoJz8nKSA/ICcmJyA6ICc/JztcbiAgICAgICAgICAgICAgICB1cmwgPSB1cmwgKyBpbmRpY2F0b3IgKyAnbWVkaWE9JyArIHNlbGVjdGVkLm1hcChvYmogPT4gb2JqLmlkKS5qb2luKCcsJyk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICB0aGlzLmlzRmV0Y2hpbmcgPSB0cnVlO1xuICAgICAgICAgICAgY29uc3QgcmVzcG9uc2UgPSBhd2FpdCBmZXRjaCh1cmwpO1xuICAgICAgICAgICAgY29uc3QgcmVzdWx0ID0gYXdhaXQgcmVzcG9uc2UuanNvbigpO1xuICAgICAgICAgICAgdGhpcy5maWxlcyA9IHRoaXMuZmlsZXMgPyB0aGlzLmZpbGVzLmNvbmNhdChyZXN1bHQuZGF0YSkgOiByZXN1bHQuZGF0YTtcbiAgICAgICAgICAgIHRoaXMubmV4dFBhZ2VVcmwgPSByZXN1bHQubmV4dF9wYWdlX3VybDtcbiAgICAgICAgICAgIHRoaXMuaXNGZXRjaGluZyA9IGZhbHNlO1xuICAgICAgICB9LFxuICAgICAgICBsb2FkTW9yZUZpbGVzOiBhc3luYyBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIGlmICh0aGlzLm5leHRQYWdlVXJsKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5pc0ZldGNoaW5nID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICBhd2FpdCB0aGlzLmdldEZpbGVzKHRoaXMubmV4dFBhZ2VVcmwsIHRoaXMuc2VsZWN0ZWQ/LmlkKTtcbiAgICAgICAgICAgICAgICB0aGlzLmlzRmV0Y2hpbmcgPSBmYWxzZTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgc2VhcmNoRmlsZXM6IGFzeW5jIGZ1bmN0aW9uKGV2ZW50KSB7XG4gICAgICAgICAgICB0aGlzLmlzRmV0Y2hpbmcgPSB0cnVlO1xuICAgICAgICAgICAgY29uc3QgcmVzcG9uc2UgPSBhd2FpdCBmZXRjaCgnL2N1cmF0b3IvbWVkaWEvc2VhcmNoP3E9JyArIGV2ZW50LnRhcmdldC52YWx1ZSk7XG4gICAgICAgICAgICBjb25zdCByZXN1bHQgPSBhd2FpdCByZXNwb25zZS5qc29uKCk7XG4gICAgICAgICAgICB0aGlzLmZpbGVzID0gcmVzdWx0LmRhdGE7XG4gICAgICAgICAgICB0aGlzLmlzRmV0Y2hpbmcgPSBmYWxzZTtcbiAgICAgICAgfSxcbiAgICAgICAgYWRkTmV3RmlsZTogZnVuY3Rpb24obWVkaWEgPSBudWxsKSB7XG4gICAgICAgICAgICBpZiAobWVkaWEpIHtcbiAgICAgICAgICAgICAgICB0aGlzLmZpbGVzID0gWy4uLm1lZGlhLCAuLi50aGlzLmZpbGVzXTtcbiAgICAgICAgICAgICAgICB0aGlzLiRuZXh0VGljaygoKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuYWRkVG9TZWxlY3Rpb24obWVkaWFbMF0uaWQpO1xuICAgICAgICAgICAgICAgIH0pXG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG4gICAgICAgIHJlbW92ZUZpbGU6IGZ1bmN0aW9uKG1lZGlhID0gbnVsbCkge1xuICAgICAgICAgICAgaWYgKG1lZGlhKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5maWxlcyA9IHRoaXMuZmlsZXMuZmlsdGVyKChvYmopID0+IG9iai5pZCAhPT0gbWVkaWEuaWQpO1xuICAgICAgICAgICAgICAgIHRoaXMucmVtb3ZlRnJvbVNlbGVjdGlvbihtZWRpYS5pZCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG4gICAgICAgIGFkZFRvU2VsZWN0aW9uOiBmdW5jdGlvbihtZWRpYUlkID0gbnVsbCkge1xuICAgICAgICAgICAgdGhpcy5zZWxlY3RlZC5wdXNoKHRoaXMuZmlsZXMuZmluZChvYmogPT4gb2JqLmlkID09PSBtZWRpYUlkKSk7XG4gICAgICAgIH0sXG4gICAgICAgIHJlbW92ZUZyb21TZWxlY3Rpb246IGZ1bmN0aW9uKG1lZGlhSWQgPSBudWxsKSB7XG4gICAgICAgICAgICB0aGlzLnNlbGVjdGVkID0gdGhpcy5zZWxlY3RlZC5maWx0ZXIoKG9iaikgPT4gb2JqLmlkICE9PSBtZWRpYUlkKTtcbiAgICAgICAgfSxcbiAgICAgICAgaXNTZWxlY3RlZDogZnVuY3Rpb24obWVkaWFJZCA9IG51bGwpIHtcbiAgICAgICAgICAgIGlmICh0aGlzLnNlbGVjdGVkLmxlbmd0aCA9PT0gMCkgcmV0dXJuIGZhbHNlO1xuXG4gICAgICAgICAgICByZXR1cm4gdGhpcy5zZWxlY3RlZC5maW5kKChvYmopID0+IG9iai5pZCA9PT0gbWVkaWFJZCkgIT09IHVuZGVmaW5lZDtcbiAgICAgICAgfSxcbiAgICAgICAgaW5zZXJ0TWVkaWE6IGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgdGhpcy4kZGlzcGF0Y2goJ2luc2VydC1tZWRpYScsIHtcbiAgICAgICAgICAgICAgICBzdGF0ZVBhdGg6IHRoaXMuc3RhdGVQYXRoLFxuICAgICAgICAgICAgICAgIG1lZGlhOiB0aGlzLnNlbGVjdGVkLFxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICB9KSk7XG5cbiAgICBBbHBpbmUuZGF0YSgnY3VyYXRpb24nLCAoeyBzdGF0ZVBhdGgsIGZpbGVOYW1lLCBmaWxlVHlwZSwgcHJlc2V0cyA9IHt9fSkgPT4gKHtcbiAgICAgICAgc3RhdGVQYXRoOiBzdGF0ZVBhdGgsXG4gICAgICAgIGZpbGVuYW1lOiBmaWxlTmFtZSxcbiAgICAgICAgZmlsZXR5cGU6IGZpbGVUeXBlLFxuICAgICAgICBjcm9wcGVyOiBudWxsLFxuICAgICAgICBwcmVzZXRzOiBwcmVzZXRzLFxuICAgICAgICBwcmVzZXQ6ICdjdXN0b20nLFxuICAgICAgICBmbGlwcGVkSG9yaXpvbnRhbGx5OiBmYWxzZSxcbiAgICAgICAgZmxpcHBlZFZlcnRpY2FsbHk6IGZhbHNlLFxuICAgICAgICBmb3JtYXQ6ICdqcGcnLFxuICAgICAgICBxdWFsaXR5OiAgNjAsXG4gICAgICAgIGtleTogbnVsbCxcbiAgICAgICAgZmluYWxXaWR0aDogMCxcbiAgICAgICAgZmluYWxIZWlnaHQ6IDAsXG4gICAgICAgIGNyb3BCb3hEYXRhOiB7XG4gICAgICAgICAgICBsZWZ0OiAwLFxuICAgICAgICAgICAgdG9wOiAwLFxuICAgICAgICAgICAgd2lkdGg6IDAsXG4gICAgICAgICAgICBoZWlnaHQ6IDAsXG4gICAgICAgIH0sXG4gICAgICAgIGRhdGE6IHtcbiAgICAgICAgICAgIGxlZnQ6IDAsXG4gICAgICAgICAgICB0b3A6IDAsXG4gICAgICAgICAgICB3aWR0aDogMCxcbiAgICAgICAgICAgIGhlaWdodDogMCxcbiAgICAgICAgICAgIHJvdGF0ZTogMCxcbiAgICAgICAgICAgIHNjYWxlWDogMSxcbiAgICAgICAgICAgIHNjYWxlWTogMSxcbiAgICAgICAgfSxcbiAgICAgICAgaW5pdCgpIHtcbiAgICAgICAgICAgIHRoaXMuZGVzdHJveSgpO1xuXG4gICAgICAgICAgICBzZXRUaW1lb3V0KCgpID0+IHtcbiAgICAgICAgICAgICAgICB0aGlzLmNyb3BwZXIgPSBuZXcgQ3JvcHBlcih0aGlzLiRyZWZzLmltYWdlLCB7XG4gICAgICAgICAgICAgICAgICAgIGJhY2tncm91bmQ6IGZhbHNlLFxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfSwgMTAwKTtcbiAgICAgICAgICAgIFxuICAgICAgICAgICAgdGhpcy4kd2F0Y2goJ3ByZXNldCcsICgkdmFsdWUpID0+IHtcbiAgICAgICAgICAgICAgICBpZiAoJHZhbHVlID09PSAnY3VzdG9tJykge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLmNyb3BwZXIucmVzZXQoKVxuICAgICAgICAgICAgICAgICAgICB0aGlzLmtleSA9IG51bGw7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuZm9ybWF0ID0gJ2pwZyc7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMucXVhbGl0eSA9IDYwO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIGxldCBjb250YWluZXJEYXRhID0gdGhpcy5jcm9wcGVyLmdldENvbnRhaW5lckRhdGEoKTtcbiAgICAgICAgICAgICAgICAgICAgbGV0IGNyb3BCb3hEYXRhID0gdGhpcy5jcm9wcGVyLmdldENyb3BCb3hEYXRhKCk7XG4gICAgICAgICAgICAgICAgICAgIGxldCBwcmVzZXQgPSB0aGlzLnByZXNldHMuZmluZCgocCkgPT4gcC5rZXkgPT09ICR2YWx1ZSlcbiAgICAgICAgICAgICAgICAgICAgbGV0IHdpZHRoID0gcHJlc2V0LndpZHRoO1xuICAgICAgICAgICAgICAgICAgICBsZXQgaGVpZ2h0ID0gcHJlc2V0LmhlaWdodDtcbiAgICAgICAgICAgICAgICAgICAgbGV0IGxlZnQgPSBNYXRoLnJvdW5kKChjb250YWluZXJEYXRhLndpZHRoIC0gd2lkdGgpIC8gMik7XG4gICAgICAgICAgICAgICAgICAgIGxldCB0b3AgPSBNYXRoLnJvdW5kKChjb250YWluZXJEYXRhLmhlaWdodCAtIGhlaWdodCkgLyAyKTtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5jcm9wcGVyLnNldENyb3BCb3hEYXRhKHsuLi5jcm9wQm94RGF0YSwgbGVmdCwgdG9wLCB3aWR0aCwgaGVpZ2h0fSk7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMua2V5ID0gcHJlc2V0LmtleTtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5mb3JtYXQgPSBwcmVzZXQuZm9ybWF0O1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnF1YWxpdHkgPSBwcmVzZXQucXVhbGl0eTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KVxuICAgICAgICB9LFxuICAgICAgICBkZXN0cm95KCkge1xuICAgICAgICAgICAgaWYgKHRoaXMuY3JvcHBlciA9PSBudWxsKSByZXR1cm47XG4gICAgICAgICAgICB0aGlzLmNyb3BwZXIuZGVzdHJveSgpO1xuICAgICAgICAgICAgdGhpcy5jcm9wcGVyID0gbnVsbDtcbiAgICAgICAgfSxcbiAgICAgICAgc2V0RGF0YSgpIHtcbiAgICAgICAgICAgIHRoaXMuZmluYWxXaWR0aCA9IHRoaXMuZGF0YS53aWR0aDtcbiAgICAgICAgICAgIHRoaXMuZmluYWxIZWlnaHQgPSB0aGlzLmRhdGEuaGVpZ2h0O1xuICAgICAgICAgICAgdGhpcy5kYXRhID0gdGhpcy5jcm9wcGVyLmdldERhdGEodHJ1ZSk7XG4gICAgICAgICAgICB0aGlzLmNyb3BCb3hEYXRhID0gdGhpcy5jcm9wcGVyLmdldENyb3BCb3hEYXRhKCk7XG4gICAgICAgIH0sXG4gICAgICAgIHVwZGF0ZURhdGEoKSB7XG4gICAgICAgICAgICB0aGlzLmZpbmFsV2lkdGggPSB0aGlzLmRhdGEud2lkdGg7XG4gICAgICAgICAgICB0aGlzLmZpbmFsSGVpZ2h0ID0gdGhpcy5kYXRhLmhlaWdodDtcbiAgICAgICAgICAgIHRoaXMuZGF0YSA9IHRoaXMuY3JvcHBlci5nZXREYXRhKHRydWUpO1xuICAgICAgICAgICAgdGhpcy5jcm9wQm94RGF0YSA9IHRoaXMuY3JvcHBlci5nZXRDcm9wQm94RGF0YSgpO1xuICAgICAgICB9LFxuICAgICAgICBzZXRDcm9wQm94WCgkZXZlbnQpIHtcbiAgICAgICAgICAgIGxldCBjdXJyZW50Q3JvcEJveCA9IHRoaXMuY3JvcHBlci5nZXRDcm9wQm94RGF0YSgpO1xuICAgICAgICAgICAgdGhpcy5jcm9wcGVyLnNldENyb3BCb3hEYXRhKHsuLi5jdXJyZW50Q3JvcEJveCwgbGVmdDogcGFyc2VJbnQoJGV2ZW50LnRhcmdldC52YWx1ZSl9KVxuICAgICAgICB9LFxuICAgICAgICBzZXRDcm9wQm94WSgkZXZlbnQpIHtcbiAgICAgICAgICAgIGxldCBjdXJyZW50Q3JvcEJveCA9IHRoaXMuY3JvcHBlci5nZXRDcm9wQm94RGF0YSgpO1xuICAgICAgICAgICAgdGhpcy5jcm9wcGVyLnNldENyb3BCb3hEYXRhKHsuLi5jdXJyZW50Q3JvcEJveCwgdG9wOiBwYXJzZUludCgkZXZlbnQudGFyZ2V0LnZhbHVlKX0pXG4gICAgICAgIH0sXG4gICAgICAgIHNldENyb3BCb3hXaWR0aCgkZXZlbnQpIHtcbiAgICAgICAgICAgIGxldCBjdXJyZW50Q3JvcEJveCA9IHRoaXMuY3JvcHBlci5nZXRDcm9wQm94RGF0YSgpO1xuICAgICAgICAgICAgdGhpcy5jcm9wcGVyLnNldENyb3BCb3hEYXRhKHsuLi5jdXJyZW50Q3JvcEJveCwgd2lkdGg6IHBhcnNlSW50KCRldmVudC50YXJnZXQudmFsdWUpfSlcbiAgICAgICAgfSxcbiAgICAgICAgc2V0Q3JvcEJveEhlaWdodCgkZXZlbnQpIHtcbiAgICAgICAgICAgIGxldCBjdXJyZW50Q3JvcEJveCA9IHRoaXMuY3JvcHBlci5nZXRDcm9wQm94RGF0YSgpO1xuICAgICAgICAgICAgdGhpcy5jcm9wcGVyLnNldENyb3BCb3hEYXRhKHsuLi5jdXJyZW50Q3JvcEJveCwgaGVpZ2h0OiBwYXJzZUludCgkZXZlbnQudGFyZ2V0LnZhbHVlKX0pXG4gICAgICAgIH0sXG4gICAgICAgIGZsaXBIb3Jpem9udGFsbHkoKVxuICAgICAgICB7XG4gICAgICAgICAgICB0aGlzLmNyb3BwZXIuc2NhbGVYKHRoaXMuZmxpcHBlZEhvcml6b250YWxseSA/IDEgOiAtMSk7XG4gICAgICAgICAgICB0aGlzLmZsaXBwZWRIb3Jpem9udGFsbHkgPSAhIHRoaXMuZmxpcHBlZEhvcml6b250YWxseVxuICAgICAgICB9LFxuICAgICAgICBmbGlwVmVydGljYWxseSgpXG4gICAgICAgIHtcbiAgICAgICAgICAgIHRoaXMuY3JvcHBlci5zY2FsZVkodGhpcy5mbGlwcGVkVmVydGljYWxseSA/IDEgOiAtMSk7XG4gICAgICAgICAgICB0aGlzLmZsaXBwZWRWZXJ0aWNhbGx5ID0gISB0aGlzLmZsaXBwZWRWZXJ0aWNhbGx5XG4gICAgICAgIH0sXG4gICAgICAgIHNhdmVDdXJhdGlvbigpIHtcbiAgICAgICAgICAgIGxldCBkYXRhID0gdGhpcy5jcm9wcGVyLmdldERhdGEodHJ1ZSk7XG4gICAgICAgICAgICBkYXRhID0ge1xuICAgICAgICAgICAgICAgIC4uLmRhdGEsXG4gICAgICAgICAgICAgICAgY29udGFpbmVyRGF0YTogdGhpcy5jcm9wcGVyLmdldENvbnRhaW5lckRhdGEoKSxcbiAgICAgICAgICAgICAgICBpbWFnZURhdGE6IHRoaXMuY3JvcHBlci5nZXRJbWFnZURhdGEoKSxcbiAgICAgICAgICAgICAgICBjYW52YXNEYXRhOiB0aGlzLmNyb3BwZXIuZ2V0Q2FudmFzRGF0YSgpLFxuICAgICAgICAgICAgICAgIGNyb3BwZWRDYW52YXNEYXRhOiB0aGlzLmNyb3BwZXIuZ2V0Q3JvcHBlZENhbnZhcygpLFxuICAgICAgICAgICAgICAgIGZvcm1hdDogdGhpcy5mb3JtYXQsXG4gICAgICAgICAgICAgICAgcXVhbGl0eTogdGhpcy5xdWFsaXR5LFxuICAgICAgICAgICAgICAgIHByZXNldDogdGhpcy5wcmVzZXQsXG4gICAgICAgICAgICAgICAga2V5OiB0aGlzLmtleSA/PyB0aGlzLnByZXNldCxcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHRoaXMuJHdpcmUuc2F2ZUN1cmF0aW9uKGRhdGEpO1xuICAgICAgICB9LFxuICAgIH0pKTtcbn0pXG4iXSwKICAibWFwcGluZ3MiOiAiOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFVQSxJQUFDLFVBQVUsUUFBUSxTQUFTO0FBQzFCLGFBQU8sWUFBWSxZQUFZLE9BQU8sV0FBVyxjQUFjLE9BQU8sVUFBVSxZQUNoRixPQUFPLFdBQVcsY0FBYyxPQUFPLE1BQU0sT0FBTyxXQUNuRCxVQUFTLE9BQU8sZUFBZSxjQUFjLGFBQWEsVUFBVSxNQUFNLE9BQU8sVUFBVTtBQUFBLE9BQzNGLFNBQU8sV0FBWTtBQUFFO0FBRXRCLHVCQUFp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
