
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

    cData.getTotais(cod, cUI.filterCtrl.getFilters(), cUI.mapCtrl.markerType, table, function (data) {
        var pallete = [
            "#aa4744", "#4573a7", "#89a54e", "#71588f", "#4298af", "#dc833f", "#95a8d0", "#444444", "#d29291", "#bbcc96", "#aa9abe", "#99ff99", "#000066", "#ffc299", "#006666"
        ];
        var svg = d3.select("#" + elem.id).append("svg").attr("width", 200).attr("height", 200);
        var legenda = $("<div/>").addClass("legenda").appendTo(elem);

        var data_pie = [];
        for (var i = 0; i < data.length; i++) {
            if (table == "enade") {
                switch(data[i].nome) {
                    case "0": data[i].nome = "INDEFINIDO"; pallete[i] = "#AAA"; break;
                    case "1": data[i].nome = "1 (0.0 até 1.0)"; break;
                    case "2": data[i].nome = "2 (1.0 até 2.0)"; break;
                    case "3": data[i].nome = "3 (2.0 até 3.0)"; break;
                    case "4": data[i].nome = "4 (3.0 até 4.0)"; break;
                    case "5": data[i].nome = "5 (4.0 até 5.0)"; break;
                }
            }
            data_pie.push({label: data[i].nome, value: data[i].total, color: pallete[i]});
            var cor = $("<span/>").addClass("cor").css("background", pallete[i]);
            var text = $("<span/>").addClass("text").html(data[i].nome);
            $("<div/>").addClass("cor_legenda").append(cor).append(text).appendTo(legenda);
        }
        //svg.append("g").attr("id", "salesDonut");
        svg.append("g").attr("id", "quotesDonut_" + table);

        //Donut3D.draw("salesDonut", randomData(), 150, 150, 130, 100, 30, 0.4);
        Donut3D.draw("quotesDonut_" + table, data_pie, 100, 100, 70, 60, 15, 0);

//
//        function changeData() {
//            //Donut3D.transition("salesDonut", randomData(), 130, 100, 30, 0.4);
//            Donut3D.transition("quotesDonut", randomData(), 130, 100, 30, 0);
//        }
//
//        function randomData() {
//            return salesData.map(function (d) {
//                return {label: d.label, value: 1000 * Math.random(), color: d.color};
//            });
//        }
    });

}