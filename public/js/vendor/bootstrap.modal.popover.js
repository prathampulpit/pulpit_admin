
/*!
 * @copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2018
 * @version 1.4.7
 *
 * Bootstrap Popover Extended - Popover with modal behavior, styling enhancements and more.
 *
 * For more JQuery/Bootstrap plugins and demos visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */
!function(t){"use strict";"function"==typeof define&&define.amd?define(["jquery"],t):"object"==typeof module&&module.exports?module.exports=t(require("jquery")):t(window.jQuery)}(function(t){"use strict";var o,e,i;o={NAMESPACE:".popoverX",kvLog:function(t){t="bootstrap-popover-x: "+t,window.console&&window.console.log?window.console.log(t):window.alert(t)},addCss:function(t,o){t.removeClass(o).addClass(o)},handler:function(t,e,i){var r=e+o.NAMESPACE;return t.off(r).on(r,i)},raise:function(t,e,i){var r=e+(void 0===i?".target":i)+o.NAMESPACE;return t.trigger(r)}},i=function(o,e){this.options=e,this.$element=t(o),this.$dialog=this.$element,this.init()},(e=function(o,e){this.options=e,this.$element=t(o),this.init()}).prototype={constructor:e,init:function(){var e,i,r,n=this,s=n.$element,a=n.options||{},p=s.attr("href");if(r=function(t){o.kvLog("PopoverX initialization skipped! "+t)},n.href=p,s&&s.length)if(i=a.target?t(a.target):t(s.attr("data-target")||p&&p.replace(/.*(?=#[^\s]+$)/,"")),o.addCss(i,"popover-x"),n.$dialog=i,i.length){if(!i.data("popover-x")){var l=t.extend(!0,{remote:p&&!/#/.test(p)},i.data(),s.data(),a);l.$target=s,i.popoverX(l)}"string"==typeof(e=a.trigger)?(e=e.split(" "),t.each(e,function(t,o){n.listen(o)})):r("Invalid or improper configuration for PopoverX trigger.")}else r("PopoverX dialog element could not be found.");else r("PopoverX triggering button element could not be found.")},listen:function(t){var e,i,r=this.$element,n=this.$dialog,s=!1,a=this.href;"manual"!==t&&("click"!==t&&"keyup"!==t&&(s=!0),s?(e="hover"===t?"mouseenter":"focusin",i="hover"===t?"mouseleave":"focusout",o.handler(r,e,function(){o.raise(n,e).popoverX("show")}),o.handler(r,i,function(){o.raise(n,i).popoverX("hide")})):o.handler(r,t,function(e){"keyup"!==t?(a&&"click"===t&&e.preventDefault(),o.raise(n,t).popoverX("toggle"),o.handler(n,"hide",function(){r.focus()})):n&&27===e.which&&o.raise(n,t).popoverX("hide")}))},destroy:function(){this.$element.off(o.NAMESPACE),this.$dialog.off(o.NAMESPACE)}},i.prototype=t.extend({},t.fn.modal.Constructor.prototype,{constructor:i,init:function(){var e=this,i=e.$element,r=e.options.$container;r&&r.length&&(e.$body=r),e.$body&&e.$body.length||(e.$body=t(document.body)),e.bodyPadding=e.$body.css("padding"),e.$target=e.options.$target,e.$marker=t(document.createElement("div")).addClass("popover-x-marker").insertAfter(i).hide(),i.find(".popover-footer").length&&o.addCss(i,"has-footer"),e.options.remote&&i.find(".popover-content").load(e.options.remote,function(){i.trigger("load.complete.popoverX")}),i.on("click.dismiss"+o.NAMESPACE,'[data-dismiss="popover-x"]',t.proxy(e.hide,e)),t(window).resize(function(){i.hasClass("kv-popover-active")&&(e.hide(),setTimeout(function(){e.show(!0)},50))})},getPlacement:function(){var t=this.getPosition(),o=this.options.placement,e=document.documentElement,i=document.body,r=e.clientWidth,n=e.clientHeight,s=Math.max(i.scrollTop||0,e.scrollTop),a="horizontal"===o,p=Math.max(i.scrollLeft||0,e.scrollLeft),l="vertical"===o,f=Math.max(0,t.left-p),h=Math.max(0,t.top-s),d="auto"===o||a||l,c=window.innerWidth||e||document.body.clientWidth;if(this.options.autoPlaceSmallScreen&&c<this.options.smallScreenWidth&&(d=!0),d)return f<r/3?h<n/3?a?"right right-top":"bottom bottom-left":h<2*n/3?l?h<=n/2?"bottom bottom-left":"top top-left":"right":a?"right right-bottom":"top top-left":f<2*r/3?h<n/3?a?f<=r/2?"right right-top":"left left-top":"bottom":h<2*n/3?a?f<=r/2?"right":"left":h<=n/2?"bottom":"top":a?f<=r/2?"right right-bottom":"left left-bottom":"top":h<n/3?a?"left left-top":"bottom bottom-left":h<2*n/3?l?h<=n/2?"bottom-right":"top-right":"left":a?"left left-bottom":"top top-left";switch(o){case"auto-top":return f<r/3?"top top-left":f<2*r/3?"top":"top top-right";case"auto-bottom":return f<r/3?"bottom bottom-left":f<2*r/3?"bottom":"bottom bottom-right";case"auto-left":return h<n/3?"left left-top":h<2*n/3?"left":"left left-bottom";case"auto-right":return h<n/3?"right right-top":h<2*n/3?"right":"right right-bottom";default:return o}},getPosition:function(){var o=this.$target,e=o[0].getBoundingClientRect(),i=this.$body,r=i.css("position");if(i.is(document.body)||"static"===r)return t.extend({},o.offset(),{width:o[0].offsetWidth||e.width,height:o[0].offsetHeight||e.height});if("relative"===r)return{top:o.offset().top-i.offset().top,left:o.offset().left-i.offset().left,width:o[0].offsetWidth||e.width,height:o[0].offsetHeight||e.height};var n=i[0].getBoundingClientRect();return{top:e.top-n.top+i.scrollTop(),left:e.left-n.left+i.scrollLeft(),width:e.width,height:e.height}},refreshPosition:function(){var t,e=this.$element,i=this.getPosition(),r=e[0].offsetWidth,n=e[0].offsetHeight,s=this.getPlacement();switch(s){case"bottom":t={top:i.top+i.height,left:i.left+i.width/2-r/2};break;case"bottom bottom-left":t={top:i.top+i.height,left:i.left};break;case"bottom bottom-right":t={top:i.top+i.height,left:i.left+i.width-r};break;case"top":t={top:i.top-n,left:i.left+i.width/2-r/2};break;case"top top-left":t={top:i.top-n,left:i.left};break;case"top top-right":t={top:i.top-n,left:i.left+i.width-r};break;case"left":t={top:i.top+i.height/2-n/2,left:i.left-r};break;case"left left-top":t={top:i.top,left:i.left-r};break;case"left left-bottom":t={top:i.top+i.height-n,left:i.left-r};break;case"right":t={top:i.top+i.height/2-n/2,left:i.left+i.width};break;case"right right-top":t={top:i.top,left:i.left+i.width};break;case"right right-bottom":t={top:i.top+i.height-n,left:i.left+i.width};break;default:o.kvLog("Invalid popover placement '"+s+"'.")}e.removeClass("bottom top left right bottom-left top-left bottom-right top-right left-bottom left-top right-bottom right-top").css(t).addClass(s+" in")},validateOpenPopovers:function(){var o=this.$element;this.options.closeOpenPopovers&&this.$body.find(".popover:visible").each(function(){var e=t(this);e.is(o)||e.popoverX("hide")})},hide:function(){var t=this.$element;this.$body.removeClass("popover-x-body"),t.removeClass("kv-popover-active"),t.modal("hide"),t.insertBefore(this.$marker)},show:function(t){var e=this.$element;e.addClass("kv-popover-active"),e.css(this.options.dialogCss).appendTo(this.$body),t||this.validateOpenPopovers(),o.addCss(this.$body,"popover-x-body"),e.modal("show"),this.$body.css({padding:this.bodyPadding}),e.css({padding:0}),this.refreshPosition()},destroy:function(){this.$element.off(o.NAMESPACE)}}),t.fn.popoverButton=function(o){return this.each(function(){var i=t(this),r=i.data("popover-button"),n=t.extend({},t.fn.popoverButton.defaults,i.data(),"object"==typeof o&&o);r||i.data("popover-button",r=new e(this,n)),"string"==typeof o&&r[o]()})},t.fn.popoverX=function(o){return this.each(function(){var e=t(this),r=e.data("popover-x"),n=t.extend({},t.fn.popoverX.defaults,e.data(),"object"==typeof o&&o);n.$target||(r&&r.$target?n.$target=r.$target:n.$target=o.$target||t(o.target)),r||e.data("popover-x",r=new i(this,n)),"string"==typeof o?r[o]():n.show&&r.show(!0)})},t.fn.popoverButton.defaults={trigger:"click keyup"},t.fn.popoverX.defaults=t.extend(!0,{},t.fn.modal.defaults,{placement:"auto",dialogCss:{top:0,left:0,display:"block","z-index":1050},keyboard:!0,autoPlaceSmallScreen:!0,smallScreenWidth:640,closeOpenPopovers:!0,backdrop:!1,show:!1}),t.fn.popoverButton.Constructor=e,t.fn.popoverX.Constructor=i,t(document).ready(function(){var o=t("[data-toggle='popover-x']");o.length&&o.popoverButton()})});