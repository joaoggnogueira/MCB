
//class graph-item
function cGraph(mapInfo, elem, cod) {
    const pallete = [
        "#4573a7", "#aa4744", "#89a54e", "#71588f", "#4298af", "#dc833f", "#95a8d0", "#444444", "#d29291", "#bbcc96", "#aa9abe", "#99ff99", "#000066", "#ffc299", "#006666"
    ];
    const palleteHover = [
        "#6593c7", "#ca6764", "#a9c56e", "#9178af", "#62b8cf", "#fca35f", "#b5c8f0", "#646464", "#f2b2b1", "#dbecb6", "#cabade", "#b9ffb9", "#5050a6", "#ffe2b9", "#208686"
    ];

    for (let i = 0; i < pallete.length; i++) {
        palleteHover[i] = d3.hsl(pallete[i]);
        palleteHover[i].s += 0.3;
        palleteHover[i].l += 0.1;
        palleteHover[i] = palleteHover[i].toString();
    }

    const elem_content = cUI.catchElement(elem);
    let tipoVisual = false;
    if (elem_content.classList.contains("graph-bars")) {
        tipoVisual = "barras";
    } else if (elem_content.classList.contains("graph-sector")) {
        tipoVisual = "setor";
    }
    const table = elem_content.getAttribute("name");

    cData.getTotais(cod, mapInfo.id, cUI.filterCtrl.getFilters(), cUI.mapCtrl.markerType, table, function (data) {

        let elem_item = elem_content.parentClass("graph-item");

        if (data.length === 0) {
            elem_item.hide();
        } else if (data.length === 1) {
            elem_item.removeAttribute("open");
            elem_content.html("<div class='text-one'>Todos os cursos apresentam resultado : <b>" + data[0].nome + "</b></div>");
        } else {
            elem_item.setAttribute("open", true);
            const width = 200;
            const height = 200;
            const svg = d3.select("#" + elem_content.id).append("svg").attr("width", width).attr("height", height);
            const legenda = $("<div/>").addClass("legenda").appendTo(elem_content);
            cUI.filterCtrl.filterCheckboxes[table];
            const data_chart = [];
            if (data.length <= 4) {
                tipoVisual = "setor";
            }
            let sum = 0;
            for (let i = 0; i < data.length; i++) {
                sum += parseInt(data[i].total);
            }
            const labels = [];
            for (let i = 0; i < data.length; i++) {
                if (table === "enade") {
                    switch (data[i].nome) {
                        case "0":
                            data[i].nome = "INDEFINIDO";
                            pallete[i] = "#AAA";
                            palleteHover[i] = "#777";
                            break;
                        case "1":
                            data[i].nome = "Nota 1 (0.0 at?? 1.0)";
                            break;
                        case "2":
                            data[i].nome = "Nota 2 (1.0 at?? 2.0)";
                            break;
                        case "3":
                            data[i].nome = "Nota 3 (2.0 at?? 3.0)";
                            break;
                        case "4":
                            data[i].nome = "Nota 4 (3.0 at?? 4.0)";
                            break;
                        case "5":
                            data[i].nome = "Nota 5 (4.0 at?? 5.0)";
                            break;
                    }
                }
                const percent = "" + ((parseInt(data[i].total) / sum) * 100);

                const cor = $("<span/>").addClass("cor").css("background", pallete[i]);

                const text = $("<span/>").addClass("text").html(percent.substr(0, 4) + "% - " + data[i].nome);

                let html = "<b>" + data[i].nome + "</b><br/>";
                html += data[i].total + " dos " + sum + " cursos (~" + percent.substr(0, 5) + "%)";
                html = $("<div/>").html(html)[0];
                labels[i] = $("<div/>").addClass("cor_legenda").append(cor).append(text).appendTo(legenda).tipsy({
                    html: true,
                    fallback: html,
                    gravity: "nw"
                });

                data_chart.push({labelobj: labels[i], label: data[i].nome, percent: parseFloat(percent), value: data[i].total, color: pallete[i], hover_color: palleteHover[i]});
            }
            if (tipoVisual === "setor") {
                svg.append("g").attr("id", "quotesDonut_" + table);
                Donut3D.draw("quotesDonut_" + table, data_chart, 100, 100, 70, 60, 15, 0);
            } else if (tipoVisual === "barras") {

                let chart = svg.append("g");
                const x = d3.scale.ordinal()
                        .domain(data_chart.map(function (d) {
                            return d.label;
                        }))
                        .rangeRoundBands([0, width], .1);

                const y = d3.scale.linear()
                        .domain([0, d3.max(data_chart, function (d) {
                                return d.percent;
                            })])
                        .range([height, 0]);

                const xAxis = d3.svg.axis().scale(x).orient("bottom");
                const yAxis = d3.svg.axis().scale(y).orient("left");

                chart.append("g").attr("class", "x axis").attr("transform", "translate(0," + height + ")").call(xAxis);
                chart.append("g").attr("class", "y axis").call(yAxis);

                const bar = chart.selectAll(".bar").data(data_chart)
                        .enter().append("rect")
                        .attr("class", "bar")
                        .attr("x", function (d) {
                            return x(d.label);
                        })
                        .attr("y", height)
                        .attr("width", x.rangeBand())
                        .attr("height", 0);

                const a = bar.transition()
                        .duration(1500)
                        .ease("elastic")
                        .attr("y", function (d) {
                            return y(d.percent);
                        })
                        .attr("fill", function (d) {
                            return d.color;
                        })
                        .attr("height", function (d) {
                            return height - y(d.percent);
                        });
                for (let i = 0; i < a[0].length; i++) {
                    const index = i;

                    $(a[0][i]).mouseenter(function () {
                        a[0][index].setAttribute("fill", palleteHover[index]);
                        $(labels[index]).find(".cor").css("background", palleteHover[index]);
                        $(labels[index]).tipsy(true).show();
                    }).mouseleave(function () {
                        a[0][index].setAttribute("fill", pallete[index]);
                        $(labels[index]).find(".cor").css("background", pallete[index]);
                        $(labels[index]).tipsy(true).hide();
                    });

                    $(labels[i]).mouseenter(function () {
                        a[0][index].setAttribute("fill", palleteHover[index]);
                        $(labels[index]).find(".cor").css("background", palleteHover[index]);
                    }).mouseleave(function () {
                        a[0][index].setAttribute("fill", pallete[index]);
                        $(labels[index]).find(".cor").css("background", pallete[index]);
                    });
                }
            }
        }
    });

}