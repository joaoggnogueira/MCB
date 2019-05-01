/**
 * http://bl.ocks.org/NPashaP/9994181
 **/

!function () {
    const Donut3D = {};

    function pieTop(d, rx, ry, ir) {
        if (d.endAngle - d.startAngle == 0)
            return "M 0 0";
        const sx = rx * Math.cos(d.startAngle),
                sy = ry * Math.sin(d.startAngle),
                ex = rx * Math.cos(d.endAngle),
                ey = ry * Math.sin(d.endAngle);

        const ret = [];
        ret.push("M", sx, sy, "A", rx, ry, "0", (d.endAngle - d.startAngle > Math.PI ? 1 : 0), "1", ex, ey, "L", ir * ex, ir * ey);
        ret.push("A", ir * rx, ir * ry, "0", (d.endAngle - d.startAngle > Math.PI ? 1 : 0), "0", ir * sx, ir * sy, "z");
        return ret.join(" ");
    }

    function pieOuter(d, rx, ry, h) {
        const startAngle = (d.startAngle > Math.PI ? Math.PI : d.startAngle);
        const endAngle = (d.endAngle > Math.PI ? Math.PI : d.endAngle);

        const sx = rx * Math.cos(startAngle),
                sy = ry * Math.sin(startAngle),
                ex = rx * Math.cos(endAngle),
                ey = ry * Math.sin(endAngle);

        const ret = [];
        ret.push("M", sx, h + sy, "A", rx, ry, "0 0 1", ex, h + ey, "L", ex, ey, "A", rx, ry, "0 0 0", sx, sy, "z");
        return ret.join(" ");
    }

    function pieInner(d, rx, ry, h, ir) {
        const startAngle = (d.startAngle < Math.PI ? Math.PI : d.startAngle);
        const endAngle = (d.endAngle < Math.PI ? Math.PI : d.endAngle);

        const sx = ir * rx * Math.cos(startAngle),
                sy = ir * ry * Math.sin(startAngle),
                ex = ir * rx * Math.cos(endAngle),
                ey = ir * ry * Math.sin(endAngle);

        const ret = [];
        ret.push("M", sx, sy, "A", ir * rx, ir * ry, "0 0 1", ex, ey, "L", ex, h + ey, "A", ir * rx, ir * ry, "0 0 0", sx, h + sy, "z");
        return ret.join(" ");
    }

    function getPercent(d) {
        return (d.endAngle - d.startAngle > 0.2 ?
                Math.round(1000 * (d.endAngle - d.startAngle) / (Math.PI * 2)) / 10 + '%' : '');
    }

    Donut3D.transition = function (id, data, rx, ry, h, ir) {
        function arcTweenInner(a) {
            const i = d3.interpolate(this._current, a);
            this._current = i(0);
            return function (t) {
                return pieInner(i(t), rx + 0.5, ry + 0.5, h, ir);
            };
        }
        function arcTweenTop(a) {
            const i = d3.interpolate(this._current, a);
            this._current = i(0);
            return function (t) {
                return pieTop(i(t), rx, ry, ir);
            };
        }
        function arcTweenOuter(a) {
            const i = d3.interpolate(this._current, a);
            this._current = i(0);
            return function (t) {
                return pieOuter(i(t), rx - .5, ry - .5, h);
            };
        }
        function textTweenX(a) {
            const i = d3.interpolate(this._current, a);
            this._current = i(0);
            return function (t) {
                return 0.6 * rx * Math.cos(0.5 * (i(t).startAngle + i(t).endAngle));
            };
        }
        function textTweenY(a) {
            const i = d3.interpolate(this._current, a);
            this._current = i(0);
            return function (t) {
                return 0.6 * rx * Math.sin(0.5 * (i(t).startAngle + i(t).endAngle));
            };
        }

        const _data = d3.layout.pie().sort(null).value(function (d) {
            return d.value;
        })(data);

        d3.select("#" + id).selectAll(".innerSlice").data(_data)
                .transition().duration(750).attrTween("d", arcTweenInner);

        d3.select("#" + id).selectAll(".topSlice").data(_data)
                .transition().duration(750).attrTween("d", arcTweenTop);

        d3.select("#" + id).selectAll(".outerSlice").data(_data)
                .transition().duration(750).attrTween("d", arcTweenOuter);

        d3.select("#" + id).selectAll(".percent").data(_data).transition().duration(750)
                .attrTween("x", textTweenX).attrTween("y", textTweenY).text(getPercent);
    }

    Donut3D.draw = function (id, data, x /*center x*/, y/*center y*/,
            rx/*radius x*/, ry/*radius y*/, h/*height*/, ir/*inner radius*/) {

        const _data = d3.layout.pie().sort(null).value(function (d) {
            return d.value;
        })(data);

        const slices = d3.select("#" + id).append("g").attr("transform", "translate(" + x + "," + y + ")")
                .attr("class", "slices");

        const slices_inner = slices.selectAll(".innerSlice").data(_data).enter().append("path").attr("class", "innerSlice")
                .style("fill", function (d) {
                    return d3.hsl(d.data.color).darker(0.7);
                })
                .attr("d", function (d) {
                    return pieInner(d, rx + 0.5, ry + 0.5, h, ir);
                })
                .each(function (d) {
                    this._current = d;
                });

        const slices_top = slices.selectAll(".topSlice").data(_data).enter().append("path").attr("class", "topSlice")
                .style("fill", function (d) {
                    return d.data.color;
                })
                .style("stroke", function (d) {
                    return d.data.color;
                })
                .attr("d", function (d) {
                    return pieTop(d, rx, ry, ir);
                })
                .each(function (d) {
                    this._current = d;
                });

        const slices_outer = slices.selectAll(".outerSlice").data(_data).enter().append("path").attr("class", "outerSlice")
                .style("fill", function (d) {
                    return d3.hsl(d.data.color).darker(0.7);
                })
                .attr("d", function (d) {
                    return pieOuter(d, rx - .5, ry - .5, h);
                })
                .each(function (d) {
                    this._current = d;
                });

        const percents = slices.selectAll(".percent").data(_data).enter().append("text").attr("class", "percent")
                .attr("x", function (d) {
                    return 0.6 * rx * Math.cos(0.5 * (d.startAngle + d.endAngle));
                })
                .attr("y", function (d) {
                    return 0.6 * ry * Math.sin(0.5 * (d.startAngle + d.endAngle));
                })
                .text(getPercent).each(function (d) {
            this._current = d;
        });

        for (let i = 0; i < slices_outer[0].length; i++) {
            const index = i;
            
            const enter = function(){
                $(slices_outer[0][index]).css("fill",d3.hsl(data[index].hover_color).darker(0.7));
                $(slices_top[0][index]).css("fill",data[index].hover_color);
                $(data[index].labelobj).tipsy(true).show();
                $(data[index].labelobj).find(".cor").css("background", data[index].hover_color);
            };
            
            const out = function(){
                $(slices_outer[0][index]).css("fill",d3.hsl(data[index].color).darker(0.7));
                $(slices_top[0][index]).css("fill",data[index].color);
                $(data[index].labelobj).tipsy(true).hide();
                $(data[index].labelobj).find(".cor").css("background", data[index].color);
            };
            
            $(slices_top[0][i]).mouseenter(enter).mouseleave(out);
            $(slices_outer[0][i]).mouseenter(enter).mouseleave(out);
            $(slices_inner[0][i]).mouseenter(enter).mouseleave(out);
            $(percents[0][i]).mouseenter(enter).mouseleave(out).css("cursor","default");
            
            const enterlabel = function(){
                $(slices_outer[0][index]).css("fill",d3.hsl(data[index].hover_color).darker(0.7));
                $(slices_top[0][index]).css("fill",data[index].hover_color);
                $(data[index].labelobj).find(".cor").css("background", data[index].hover_color);
            };
            
            const outlabel = function(){
                $(slices_outer[0][index]).css("fill",d3.hsl(data[index].color).darker(0.7));
                $(slices_top[0][index]).css("fill",data[index].color);
                $(data[index].labelobj).find(".cor").css("background", data[index].color);
            };
            
            $(data[index].labelobj).mouseenter(enterlabel).mouseleave(outlabel);
            
        }

    }

    this.Donut3D = Donut3D;
}();