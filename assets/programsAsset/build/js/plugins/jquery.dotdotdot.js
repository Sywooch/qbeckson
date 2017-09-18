;(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        module.exports = factory(require('jquery'));
    } else {
        root.jquery_dotdotdot_js = factory(root.jQuery);
    }
}(this, function (jQuery) {
    /*
     *	jQuery dotdotdot 2.0.1
     *
     *	Copyright (c) Fred Heusschen
     *	www.frebsite.nl
     *
     *	Plugin website:
     *	dotdotdot.frebsite.nl
     *
     *	Licensed under the MIT license.
     *	http://en.wikipedia.org/wiki/MIT_License
     */
    !function (t, e) {
        "use strict";

        function n(t, e, n) {
            var r = t.children(), o = !1;
            t.empty();
            for (var i = 0, d = r.length; d > i; i++) {
                var l = r.eq(i);
                if (t.append(l), n && t.append(n), a(t, e)) {
                    l.remove(), o = !0;
                    break
                }
                n && n.detach()
            }
            return o
        }

        function r(e, n, i, d, l) {
            var s = !1,
                c = "a, table, thead, tbody, tfoot, tr, col, colgroup, object, embed, param, ol, ul, dl, blockquote, select, optgroup, option, textarea, script, style",
                u = "script, .dotdotdot-keep";
            return e.contents().detach().each(function () {
                var f = this, h = t(f);
                if ("undefined" == typeof f) return !0;
                if (h.is(u)) e.append(h); else {
                    if (s) return !0;
                    e.append(h), !l || h.is(d.after) || h.find(d.after).length || e[e.is(c) ? "after" : "append"](l), a(i, d) && (s = 3 == f.nodeType ? o(h, n, i, d, l) : r(h, n, i, d, l)), s || l && l.detach()
                }
            }), a(i, d) && r(i, n, i, d, l), n.addClass("is-truncated"), s
        }

        function o(e, n, r, o, d) {
            var c = e[0];
            if (!c) return !1;
            var f = s(c), h = -1 !== f.indexOf(" ") ? " " : "　", p = "letter" == o.wrap ? "" : h, g = f.split(p),
                v = -1, w = -1, m = 0, y = g.length - 1;
            if (o.fallbackToLetter && 0 === m && 0 === y && (p = "", g = f.split(p), y = g.length - 1), o.maxLength) f = i(f.trim().substr(0, o.maxLength), o), l(c, f); else {
                for (; y >= m && (0 !== m || 0 !== y);) {
                    var b = Math.floor((m + y) / 2);
                    if (b == w) break;
                    w = b, l(c, g.slice(0, w + 1).join(p) + o.ellipsis), r.children().each(function () {
                        t(this).toggle().toggle()
                    }), a(r, o) ? (y = w, o.fallbackToLetter && 0 === m && 0 === y && (p = "", g = g[0].split(p), v = -1, w = -1, m = 0, y = g.length - 1)) : (v = w, m = w)
                }
                if (-1 == v || 1 === g.length && 0 === g[0].length) {
                    var x = e.parent();
                    e.detach();
                    var T = d && d.closest(x).length ? d.length : 0;
                    if (x.contents().length > T ? c = u(x.contents().eq(-1 - T), n) : (c = u(x, n, !0), T || x.detach()), c && (f = i(s(c), o), l(c, f), T && d)) {
                        var C = d.parent();
                        t(c).parent().append(d), t.trim(C.html()) || C.remove()
                    }
                } else f = i(g.slice(0, v + 1).join(p), o), l(c, f)
            }
            return !0
        }

        function a(t, e) {
            return t.innerHeight() > e.maxHeight || e.maxLength && t.text().trim().length > e.maxLength
        }

        function i(e, n) {
            for (; t.inArray(e.slice(-1), n.lastCharacter.remove) > -1;) e = e.slice(0, -1);
            return t.inArray(e.slice(-1), n.lastCharacter.noEllipsis) < 0 && (e += n.ellipsis), e
        }

        function d(t) {
            return {width: t.innerWidth(), height: t.innerHeight()}
        }

        function l(t, e) {
            t.innerText ? t.innerText = e : t.nodeValue ? t.nodeValue = e : t.textContent && (t.textContent = e)
        }

        function s(t) {
            return t.innerText ? t.innerText : t.nodeValue ? t.nodeValue : t.textContent ? t.textContent : ""
        }

        function c(t) {
            do t = t.previousSibling; while (t && 1 !== t.nodeType && 3 !== t.nodeType);
            return t
        }

        function u(e, n, r) {
            var o, a = e && e[0];
            if (a) {
                if (!r) {
                    if (3 === a.nodeType) return a;
                    if (t.trim(e.text())) return u(e.contents().last(), n)
                }
                for (o = c(a); !o;) {
                    if (e = e.parent(), e.is(n) || !e.length) return !1;
                    o = c(e[0])
                }
                if (o) return u(t(o), n)
            }
            return !1
        }

        function f(e, n) {
            return e ? "string" == typeof e ? (e = t(e, n), e.length ? e : !1) : e.jquery ? e : !1 : !1
        }

        function h(t) {
            for (var e = t.innerHeight(), n = ["paddingTop", "paddingBottom"], r = 0, o = n.length; o > r; r++) {
                var a = parseInt(t.css(n[r]), 10);
                isNaN(a) && (a = 0), e -= a
            }
            return e
        }

        if (!t.fn.dotdotdot) {
            t.fn.dotdotdot = function (e) {
                if (0 === this.length) return t.fn.dotdotdot.debug('No element found for "' + this.selector + '".'), this;
                if (this.length > 1) return this.each(function () {
                    t(this).dotdotdot(e)
                });
                var o = t(window), i = this;
                i.data("dotdotdot") && i.trigger("destroy.dot");
                var l = i.contents();
                i.data("dotdotdot-style", i.attr("style") || ""), i.css("word-wrap", "break-word"), "nowrap" === i.css("white-space") && i.css("white-space", "normal"), i.bind_events = function () {
                    return i.on("update.dot", function (e, o) {
                        switch (i.removeClass("is-truncated"), e.preventDefault(), e.stopPropagation(), typeof s.height) {
                            case"number":
                                s.maxHeight = s.height;
                                break;
                            case"function":
                                s.maxHeight = s.height.call(i[0]);
                                break;
                            default:
                                s.maxHeight = h(i)
                        }
                        s.maxHeight += s.tolerance, "undefined" != typeof o && (("string" == typeof o || "nodeType" in o && 1 === o.nodeType) && (o = t("<div />").append(o).contents()), o instanceof t && (l = o)), v = i.wrapInner('<div class="dotdotdot" />').children(), v.contents().detach().end().append(l.clone(!0)).find("br").replaceWith("  <br />  ").end().css({
                            height: "auto",
                            width: "auto",
                            border: "none",
                            padding: 0,
                            margin: 0
                        });
                        var d = !1, u = !1;
                        return c.afterElement && (d = c.afterElement.clone(!0), d.show(), c.afterElement.detach()), a(v, s) && (u = "children" == s.wrap ? n(v, s, d) : r(v, i, v, s, d)), v.replaceWith(v.contents()), v = null, t.isFunction(s.callback) && s.callback.call(i[0], u, l), c.isTruncated = u, u
                    }).on("isTruncated.dot", function (t, e) {
                        return t.preventDefault(), t.stopPropagation(), "function" == typeof e && e.call(i[0], c.isTruncated), c.isTruncated
                    }).on("originalContent.dot", function (t, e) {
                        return t.preventDefault(), t.stopPropagation(), "function" == typeof e && e.call(i[0], l), l
                    }).on("destroy.dot", function (t) {
                        t.preventDefault(), t.stopPropagation(), i.unwatch().unbind_events().contents().detach().end().append(l).attr("style", i.data("dotdotdot-style") || "").removeClass("is-truncated").data("dotdotdot", !1)
                    }), i
                }, i.unbind_events = function () {
                    return i.off(".dot"), i
                }, i.watch = function () {
                    if (i.unwatch(), "window" == s.watch) {
                        var t = o.width(), e = o.height();
                        o.on("resize.dot" + c.dotId, function () {
                            var n = o.width(), r = o.height();
                            t == n && e == r && s.windowResizeFix || (t = n, e = r, g && clearInterval(g), g = setTimeout(function () {
                                i.trigger("update.dot")
                            }, 100))
                        })
                    } else u = d(i), g = setInterval(function () {
                        if (i.is(":visible")) {
                            var t = d(i);
                            u.width == t.width && u.height == t.height || (i.trigger("update.dot"), u = t)
                        }
                    }, 500);
                    return i
                }, i.unwatch = function () {
                    return t(window).off("resize.dot" + c.dotId), g && clearInterval(g), i
                };
                var s = t.extend(!0, {}, t.fn.dotdotdot.defaults, e), c = {}, u = {}, g = null, v = null;
                return s.lastCharacter.remove instanceof Array || (s.lastCharacter.remove = t.fn.dotdotdot.defaultArrays.lastCharacter.remove), s.lastCharacter.noEllipsis instanceof Array || (s.lastCharacter.noEllipsis = t.fn.dotdotdot.defaultArrays.lastCharacter.noEllipsis), c.afterElement = f(s.after, i), c.isTruncated = !1, c.dotId = p++, i.data("dotdotdot", !0).bind_events().trigger("update.dot"), s.watch && i.watch(), i
            }, t.fn.dotdotdot.defaults = {
                ellipsis: "… ",
                wrap: "word",
                fallbackToLetter: !0,
                lastCharacter: {},
                tolerance: 0,
                callback: null,
                after: null,
                height: null,
                watch: !1,
                windowResizeFix: !0,
                maxLength: null
            }, t.fn.dotdotdot.defaultArrays = {
                lastCharacter: {
                    remove: [" ", "　", ",", ";", ".", "!", "?"],
                    noEllipsis: []
                }
            }, t.fn.dotdotdot.debug = function (t) {
            };
            var p = 1
        }
    }(jQuery);
    return true;
}));
