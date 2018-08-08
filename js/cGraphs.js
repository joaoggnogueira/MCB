
//anti daltonismo

//class graph-item
function cGraph(elem, cod) {

    var elem = cUI.catchElement(elem);
    var tipoVisual = false;
    if (elem.classList.contains("graph-bars")) {
        tipoVisual = "barras";
    } else if (elem.classList.contains("graph-sector")) {
        tipoVisual = "setor";
    }
    var table = elem.getAttribute("name");
    var title = elem.getAttribute("categoria");

    cData.getTotais(cod, cUI.filterCtrl.getFilters(), cUI.mapCtrl.markerType, table, function (data) {
        var pallete = [
            "#4573a7", "#aa4744", "#89a54e", "#71588f", "#4298af", "#dc833f", "#95a8d0", "#444444", "#d29291", "#bbcc96", "#aa9abe", "#99ff99", "#000066", "#ffc299", "#006666"
        ];
        var width = 200;
        var height = 200;
        var svg = d3.select("#" + elem.id).append("svg").attr("width", width).attr("height", height);
        var legenda = $("<div/>").addClass("legenda").appendTo(elem);
        cUI.filterCtrl.filterCheckboxes[table];
        var data_chart = [];

        var sum = 0;
        for (var i = 0; i < data.length; i++) {
            sum += parseInt(data[i].total);
        }

        for (var i = 0; i < data.length; i++) {
            if (table === "enade") {
                switch (data[i].nome) {
                    case "0":
                        data[i].nome = "INDEFINIDO";
                        pallete[i] = "#AAA";
                        break;
                    case "1":
                        data[i].nome = "Nota 1 (0.0 até 1.0)";
                        break;
                    case "2":
                        data[i].nome = "Nota 2 (1.0 até 2.0)";
                        break;
                    case "3":
                        data[i].nome = "Nota 3 (2.0 até 3.0)";
                        break;
                    case "4":
                        data[i].nome = "Nota 4 (3.0 até 4.0)";
                        break;
                    case "5":
                        data[i].nome = "Nota 5 (4.0 até 5.0)";
                        break;
                }
            }
            var percent = "" + ((parseInt(data[i].total) / sum) * 100);

            data_chart.push({label: data[i].nome, percent: parseFloat(percent), value: data[i].total, color: pallete[i]});

            var cor = $("<span/>").addClass("cor").css("background", pallete[i]);


            var text = $("<span/>").addClass("text").html(percent.substr(0, 4) + "% - " + data[i].nome);

            var html = "<b>" + title + "</b><hr/>" + data[i].nome + "<br/>";
            html += data[i].total + " dos " + sum + " cursos (~" + percent.substr(0, 5) + "%)";
            html = $("<div/>").html(html)[0];
            $("<div/>").addClass("cor_legenda").append(cor).append(text).appendTo(legenda).tipsy({
                html: true,
                fallback: html,
                gravity: "nw"
            });
        }
        if (tipoVisual === "setor") {
            svg.append("g").attr("id", "quotesDonut_" + table);
            Donut3D.draw("quotesDonut_" + table, data_chart, 100, 100, 70, 60, 15, 0);
        } else if (tipoVisual === "barras") {

            var chart = svg.append("g");
            var x = d3.scale.ordinal()
                    .domain(data_chart.map(function (d) {
                        return d.label;
                    }))
                    .rangeRoundBands([0, width], .1);

            var y = d3.scale.linear()
                    .domain([0, d3.max(data_chart, function (d) {
                            return d.percent;
                        })])
                    .range([height, 0]);

            var xAxis = d3.svg.axis().scale(x).orient("bottom");
            var yAxis = d3.svg.axis().scale(y).orient("left");

            chart.append("g").attr("class", "x axis").attr("transform", "translate(0," + height + ")").call(xAxis);
            chart.append("g").attr("class", "y axis").call(yAxis);

            var bar = chart.selectAll(".bar").data(data_chart)
                    .enter().append("rect")
                    .attr("class", "bar")
                    .attr("x", function (d) {
                        return x(d.label);
                    })
                    .attr("y", height)
                    .attr("width", x.rangeBand())
                    .attr("height", 0);

            bar.transition()
                    .duration(1500)
                    .ease("elastic")
                    .attr("y", function (d) {
                        return y(d.percent);
                    })
                    .attr("fill", function(d){
                        return d.color;
                    })
                    .attr("height", function (d) {
                        return height - y(d.percent);
                    })
        }

    });

}