var lastsel = 0;
var grid;

function caricaGriglia(parametrijs) {

    var titolo = (parametrijs["titolo"] || "Elenco " + parametrijs["tabella"]);
    var tabella = parametrijs["tabella"];
    var nomicolonne = parametrijs["nomicolonne"];
    var modellocolonne = parametrijs["modellocolonne"];
    var multisearch = (parametrijs["multisearch"] == 0) ? false : true;
    var multisel = (parametrijs["multisel"] == 1) ? true : false;
    var funzionedaeseguireloadcomplete = parametrijs["funzionedaeseguireloadcomplete"] || null;
    var showedit = (parametrijs["showedit"] == 0) ? false : (parametrijs["permessiedit"] == 0 && parametrijs["permessiread"] == 0 ? false : true);
    var captionedit = parametrijs["captionedit"] || "";
    var showadd = (parametrijs["showadd"] == 0) ? false : (parametrijs["permessicreate"] == 0 ? false : true);
    var captionadd = parametrijs["captionadd"] || "";
    var showdel = (parametrijs["showdel"] == 0) ? false : (parametrijs["permessidelete"] == 0 ? false : true);
    var captiondel = parametrijs["captiondel"] || "";
    var showprint = (parametrijs["showprint"] == 0) ? false : true;
    var captionprint = parametrijs["captionprint"] || "";
    var showconfig = (parametrijs["showconfig"] == 1) ? true : false;
    var captionconfig = parametrijs["captionconfig"] || "";
    var editinline = (parametrijs["editinline"] == 1) ? true : false;
    var altezzaform = parametrijs["altezzaform"] || 0;
    var larghezzaform = parametrijs["larghezzaform"] || 0;
    var sinistraform = parametrijs["sinistraform"] || -1;
    var altoform = parametrijs["altoform"] || -1;
    var altezzagriglia = parametrijs["altezzagriglia"] || 600;
    var larghezzagriglia = parametrijs["larghezzagriglia"] || null;
    var is_draggable = (parametrijs["is_draggable"] == 0) ? false : true;
    var numrighe = parametrijs["numrighe"] || 50;
    var link = parametrijs["link"] || "";
    var colonna_id_link = parametrijs["colonna_id_link"] || "";
    var sortname = parametrijs["sortname"] || "id";
    var sortorder = parametrijs["sortorder"] || "desc";
    var nomelist = parametrijs["nomelist"] || "#list1";
    var nomepager = parametrijs["nomepager"] || "#pager1";
    var tastonascondi = (parametrijs["tastonascondi"] == 1) ? true : false;
    var tastochiudi = (parametrijs["tastochiudi"] == 1) ? true : false;
    var percorsogriglia = parametrijs["percorsogriglia"] || "griglia";
    var div = parametrijs["div"] || "#dettaglio";
    var chiamante = parametrijs["chiamante"] || null;
    var parametritesta = parametrijs["parametritesta"] || 0;
    var parametrigriglia = parametrijs["parametrigriglia"] || 0;
    var richiamata = parametrijs["richiamata"] || null;
    var datipost = parametrijs["datipost"] || {};
    var selezionaprimo = (parametrijs["selezionaprimo"] == 1) ? true : false;
    var nascondicaption = (parametrijs["nascondicaption"] == 1) ? true : false;
    var nascondifilter = (parametrijs["nascondifilter"] == 1) ? true : false;
    var ridimensionabile = (parametrijs["ridimensionabile"] == 0) ? false : true;
    var overlayopen = (parametrijs["overlayopen"] == 1) ? true : false;
    var imgwaiturl = parametrijs['imgwaiturl'] || '/bundles/ficore/images/wait.gif';

    var filterToolbar_stringResult = parametrijs["filterToolbar_stringResult"] || true;
    var filterToolbar_searchOnEnter = parametrijs["filterToolbar_searchOnEnter"] || false;
    var filterToolbar_searchOperators = parametrijs["filterToolbar_searchOperators"] || false;
    var filterToolbar_clearSearch = parametrijs["filterToolbar_clearSearch"] || false;

    var parametristampa = {
        'tabella': tabella,
        'nomelist': nomelist,
        'parametritesta': parametritesta,
        'parametrigriglia': parametrigriglia
    };

    // i possibili operatori di ciascuna ricerca sono questi: 
    //['eq','ne','lt','le','gt','ge','bw','bn','in','ni','ew','en','cn','nc', 'nu', 'nn'] 
    //significano questo 
    //['equal','not equal', 'less', 'less or equal','greater','greater or equal', 'begins with','does not begin with','is in','is not in','ends with','does not end with','contains','does not contain', 'is null', 'is not null'] 

    //Si sistemano tutte le caratteristiche per le colonne della griglia
    for (var i = 0; i < modellocolonne.length; i++) {
        modellocolonne[i]["editable"] = (editinline && modellocolonne[i]["name"] != "id" ? true : false);
        var effettuaricercasucampo = modellocolonne[i]["search"];
        if ((typeof effettuaricercasucampo == 'undefined') || ((typeof effettuaricercasucampo == 'undefined') && (effettuaricercasucampo != false))) {
            modellocolonne[i]["search"] = true;
        }


        if (modellocolonne[i]["tipocampo"] == "boolean") {

            modellocolonne[i]["formatter"] = "checkbox";
            modellocolonne[i]["edittype"] = "checkbox";
            modellocolonne[i]["editoptions"] = {value: "1:0"};
            modellocolonne [i]["searchoptions"] = {
                sopt: ['eq'],
                value: "null:Tutti;true:SI;false:No",
                clearSearch: filterToolbar_clearSearch
            };
            modellocolonne[i]["stype"] = "select";
        }

        if ((modellocolonne[i]["tipocampo"] == "text") || (modellocolonne[i]["tipocampo"] == "string")) {
            modellocolonne[i]["searchoptions"] = {
                sopt: ['bw', 'eq', 'cn'],
                clearSearch: filterToolbar_clearSearch
            };
            modellocolonne[i]["align"] = "left";
        }
        if ((modellocolonne[i]["tipocampo"] == "float") || (modellocolonne[i]["tipocampo"] == "integer") || (modellocolonne[i]["tipocampo"] == "number")) {
            modellocolonne[i]["searchoptions"] = {
                sopt: ['eq', 'ne', 'lt', 'le', 'gt', 'ge', 'in', 'ni'],
                clearSearch: filterToolbar_clearSearch
            };
            modellocolonne[i]["align"] = "right";
        }
        if ((modellocolonne[i]["tipocampo"] == "datetime") || (modellocolonne[i]["tipocampo"] == "date")) {
            modellocolonne[i]["searchoptions"] = {
                sopt: ['eq', 'ne', 'lt', 'le', 'gt', 'ge', 'nu', 'nn'],
                clearSearch: filterToolbar_clearSearch
            };
            modellocolonne[i]["align"] = "right";
        }

        if ((modellocolonne[i]["tipocampo"] == "float") || (modellocolonne[i]["tipocampo"] == "number"))
            modellocolonne[i]["formatter"] = 'number';

        if (modellocolonne[i]["tipocampo"] == "select") {
            modellocolonne[i]["edittype"] = "checkbox";
            modellocolonne[i]["stype"] = "select";
            if (modellocolonne[i]["tipocampo"] == "select") {
                var elencovalori = modellocolonne[i]["editoptions"];
            }
            var defaulteditoptions = FindDefaultOption(elencovalori);
            var editoptions = SetEditOptions(elencovalori);
            var searchoptions = SetSearchOptions(elencovalori);
            modellocolonne[i]["editoptions"] = {
                value: editoptions,
                defaultoption: defaulteditoptions
            };
            modellocolonne[i]["searchoptions"] = {
                sopt: ['eq', 'ne'],
                value: searchoptions,
                clearSearch: filterToolbar_clearSearch
            };
        }
    }

    //Si crea la griglia
    jQuery(nomelist).jqGrid({
        url: baseUrl + '/' + tabella + '/' + percorsogriglia,
        postData: datipost,
        datatype: "json",
        colNames: nomicolonne,
        colModel: modellocolonne,
        rowNum: numrighe,
        rowList: ((numrighe !== 50) && (numrighe !== 100) && (numrighe !== 150)) ? [numrighe, 50, 100, 150] : [50, 100, 150],
        pager: nomepager,
        sortname: sortname, //'id', 
        viewrecords: true,
        sortorder: sortorder, //"desc", 
        caption: titolo,
        editurl: baseUrl + '/' + tabella + '/aggiorna',
        multiselect: multisel,
        hidegrid: tastonascondi,
        ondblClickRow: function (rowid, iRow, iCol, e) {
            if (showedit) {
                var data = jQuery(nomelist).jqGrid("getRowData", rowid);
                apriDettaglio({
                    'tabella': tabella,
                    'tipo': 'edit',
                    'id': rowid,
                    'altezza': altezzaform,
                    'larghezza': larghezzaform,
                    'sinistra': sinistraform,
                    'alto': altoform,
                    'overlayopen': overlayopen,
                    'imgwaiturl': imgwaiturl,
                    'list': nomelist,
                    'div': div
                });

            }
        },
        onSelectRow: function (rowid) {
            lastsel = rowid;
            //if (editinline)
            //  jQuery(nomelist).jqGrid('editRow', rowid, true);
            //else {
            if (link !== "") {
                if (colonna_id_link !== "") {
                    var MyCellData = jQuery(nomelist).jqGrid('getCell', rowid, colonna_id_link);
                    location.href = link + "/id/" + MyCellData;
                } else {
                    location.href = link;
                }
                jQuery(nomelist).jqGrid('resetSelection');
            }
            //}
        },
        //ContextMenu
        loadComplete: function () {
            if (selezionaprimo) {
                var rowid = jQuery(nomelist).setSelection(jQuery(nomelist).getDataIDs()[0], true);
            }
            jQuery(nomelist).focus();
            jQuery("tr.jqgrow", this).contextMenu('myMenu1', {
                bindings: {
                    'editjqgridrow': function (trigger) {
                        // trigger is the DOM element ("tr.jqgrow") which are triggered
                        //grid.editGridRow(trigger.id, editSettings);
                        if (jQuery('#editjqgridrow').hasClass('ui-state-disabled') === false) {
                            apriDettaglio({
                                'tabella': tabella,
                                'tipo': 'edit',
                                'id': trigger.id,
                                'altezza': altezzaform,
                                'larghezza': larghezzaform,
                                'sinistra': sinistraform,
                                'alto': altoform,
                                'overlayopen': overlayopen,
                                'imgwaiturl': imgwaiturl,
                                'list': nomelist,
                                'div': div
                            });
                        }
                    },
                    'addjqgridrow': function (/*trigger*/) {

                        if (jQuery('#addjqgridrow').hasClass('ui-state-disabled') === false) {
                            apriDettaglio({
                                'tabella': tabella,
                                'tipo': 'new',
                                'altezza': altezzaform,
                                'larghezza': larghezzaform,
                                'sinistra': sinistraform,
                                'alto': altoform,
                                'overlayopen': overlayopen,
                                'imgwaiturl': imgwaiturl,
                                'list': nomelist,
                                'div': div
                            });
                        }
                    },
                    'deljqgridrow': function (trigger) {

                        if (jQuery('#deljqgridrow').hasClass('ui-state-disabled') === false) {
                            // disabled item can do be choosed
                            eliminaDettaglio({
                                'tabella': tabella,
                                'tipo': 'del',
                                'id': trigger.id,
                                'multisel': multisel,
                                'overlayopen': overlayopen,
                                'list': nomelist,
                                'imgwaiturl': imgwaiturl
                            });

                        }
                    }

                },
                onContextMenu: function (event/*, menu*/) {

                    var rowId = jQuery(event.target).closest("tr.jqgrow").attr("id");
                    //grid.setSelection(rowId);
                    // disable menu for rows with even rowids
                    if (!showadd) {
                        jQuery('#addjqgridrow').attr("disabled", "disabled").addClass('ui-state-disabled');
                    } else {
                        jQuery('#addjqgridrow').removeAttr("disabled").removeClass('ui-state-disabled');
                    }
                    if (!showedit) {
                        jQuery('#editjqgridrow').attr("disabled", "disabled").addClass('ui-state-disabled');
                    } else {
                        jQuery('#editjqgridrow').removeAttr("disabled").removeClass('ui-state-disabled');
                    }
                    if (!showdel) {
                        jQuery('#deljqgridrow').attr("disabled", "disabled").addClass('ui-state-disabled');
                    } else {
                        jQuery('#deljqgridrow').removeAttr("disabled").removeClass('ui-state-disabled');
                    }
                    return true;
                }
            });
            if (funzionedaeseguireloadcomplete != null) {
                funzionedaeseguireloadcomplete();
            }
        }
    });

    if (nascondicaption) {
        jQuery(div + " .ui-jqgrid-titlebar").hide();
    }

    //Si imposta la possibilità di usare i tasti della tastiera per interagire con la griglia
    jQuery(nomelist).jqGrid('bindKeys');

    //Si imposta la larghezza della griglia
    if (jQuery(nomelist).jqGrid('getGridParam', 'width') >= 1000) {
        jQuery(nomelist).jqGrid("setGridWidth", "1000");
    }

    if (jQuery(nomelist).jqGrid('getGridParam', 'width') <= 600) {
        jQuery(nomelist).jqGrid("setGridWidth", "600");
    }

    if (larghezzagriglia) {
        jQuery(nomelist).jqGrid("setGridWidth", larghezzagriglia);
    }

    //Si imposta l'altezza della griglia
    jQuery(nomelist).jqGrid("setGridHeight", altezzagriglia);


    jQuery(nomelist).navGrid(nomepager, {
        search: multisearch,
        edit: false,
        add: false,
        del: false
    }, {}, {}, {}, {
        multipleSearch: multisearch,
        multipleGroup: false,
        closeAfterSearch: true,
        closeAfterReset: true,
        onSearch: function () {
            dopolaricerca(parametristampa);
        },
        onReset: function () {
            jQuery("#notecorpo").hide();
        }
    });

    //Si imposta la griglia resizable
    if (ridimensionabile) {
        jQuery(nomelist).gridResize();
    }

    //Si imposta la toolbar dei filtri (quella nella testata dei campi che permettono il filtro per campo)
    if (!nascondifilter) {
        jQuery(nomelist).jqGrid('filterToolbar', {
            stringResult: filterToolbar_stringResult,
            searchOnEnter: filterToolbar_searchOnEnter,
            searchOperators: filterToolbar_searchOperators

        });
    }

    //Se si hanno i diritti di aggiungere un record si imposta il pulsante e la funizonalità
    if (showadd) {
        jQuery(nomelist).jqGrid('navButtonAdd', nomepager, {
            caption: captionadd,
            buttonicon: ((captionadd == "") ? "ui-icon-plus" : "none"),
            onClickButton: function () {
                apriDettaglio({
                    'tabella': tabella,
                    'tipo': 'new',
                    'altezza': altezzaform,
                    'larghezza': larghezzaform,
                    'sinistra': sinistraform,
                    'alto': altoform,
                    'overlayopen': overlayopen,
                    'imgwaiturl': imgwaiturl,
                    'list': nomelist,
                    'div': div
                });
            },
            position: "last",
            title: "",
            cursor: "pointer"
        });

        if (editinline) {
            parametriinline = {add: false,
                editParams: {}};

            jQuery(nomelist).jqGrid('inlineNav', nomepager, parametriinline);
        }
    }

    //Se si hanno i diritti di modificare un record si imposta il pulsante e la funizonalità
    if (showedit) {
        var s;
        jQuery(nomelist).jqGrid('navButtonAdd', nomepager, {
            id: "buttonedit_" + nomelist.substr(1),
            caption: captionedit,
            buttonicon: ((captionedit === "") ? "ui-icon-pencil" : "none"),
            onClickButton: function () {
                // dialog se nessun record è selezionato 
                // devi dare un messaggio d'errore
                s = jQuery(nomelist).jqGrid('getGridParam', 'selarrrow');

                if ((!multisel && lastsel === 0) || (multisel && s.length !== 1)) {

                    jQuery("#dialog").dialog({
                        title: 'Attenzione',
                        buttons: {
                            "Ok": function () {
                                jQuery(this).dialog("close");
                            }
                        },
                        modal: true
                    });
                    jQuery("#testodialog").html("Righe selezionate " + s.length + "<br/>" + "Selezionare una riga");

                    jQuery("#dialog").show();
                    return;
                }

                apriDettaglio({
                    'tabella': tabella,
                    'tipo': 'edit',
                    'id': (multisel ? s[0] : lastsel),
                    'altezza': altezzaform,
                    'larghezza': larghezzaform,
                    'sinistra': sinistraform,
                    'alto': altoform,
                    'overlayopen': overlayopen,
                    'imgwaiturl': imgwaiturl,
                    'list': nomelist,
                    'div': div
                });
                lastsel = 0;
                jQuery(nomelist).jqGrid('resetSelection');
            },
            position: "last",
            title: "",
            cursor: "pointer"
        });
    }


    //Se si hanno i diritti di cancellare un record si imposta il pulsante e la funizonalità
    if (showdel) {

        var s;

        jQuery(nomelist).jqGrid('navButtonAdd', nomepager, {
            caption: captiondel,
            buttonicon: ((captiondel === "") ? "ui-icon-trash" : "none"),
            onClickButton: function () {
                // dialog se nessun record è selezionato parametristampa
                // devi dare un messaggio d'errore
                s = jQuery(nomelist).jqGrid('getGridParam', 'selarrrow');

                if ((!multisel && lastsel === 0) || (multisel && s.length === 0)) {

                    jQuery("#dialog").dialog({
                        title: 'Attenzione',
                        buttons: {
                            "Ok": function () {
                                jQuery(this).dialog("close");
                            }
                        },
                        modal: true
                    });
                    jQuery("#testodialog").html("Righe selezionate " + s.length + "<br/>" + "Selezionare una riga");

                    jQuery("#dialog").show();
                    return;
                }

                eliminaDettaglio({
                    'tabella': tabella,
                    'tipo': 'edit',
                    'id': (multisel ? s : lastsel),
                    'multisel': multisel,
                    'list': nomelist,
                    'imgwaiturl': imgwaiturl
                });

                lastsel = 0;
                s = jQuery(nomelist).jqGrid('getGridParam', 'selarrrow');

            },
            position: "last",
            title: "",
            cursor: "pointer"
        });
    }

    //Se si hanno i diritti per stampare si imposta il pulsante e la funizonalità
    if (showprint) {
        jQuery(nomelist).navGrid(nomepager).navButtonAdd(nomepager, {
            caption: captionprint,
            buttonicon: ((captionprint === "") ? "ui-icon-print" : "none"),
            onClickButton: function () {
                stampa(parametristampa);
            },
            position: "last",
            title: "",
            cursor: "pointer"
        });

    }

    //Se si hanno i diritti per modificare la configurazioen della griglia si imposta il pulsante e la funizonalità
    if (showconfig) {
        jQuery(nomelist).navGrid(nomepager).navButtonAdd(nomepager, {
            caption: captionconfig,
            buttonicon: ((captionconfig === "") ? "ui-icon-calculator" : "none"),
            onClickButton: function () {
                mostraConfigurazione(parametristampa);
            },
            position: "last",
            title: "",
            cursor: "pointer"
        });

    }

    //Se si può spostare la griglia
    if (is_draggable) {
        jQuery(div).draggable({
            handle: '.ui-jqgrid-titlebar, .ui-jqgrid-pager'
        });
    }

    //Se c'è il tasto chiudi
    if (tastochiudi) {
        temp = jQuery("<div id='tastochiudi' style='right: 16px;'/>")

                .addClass('ui-jqgrid-titlebar-close HeaderButton')

                .hover(
                        function () {
                            jQuery(this).addClass('ui-state-hover');
                        },
                        function () {
                            jQuery(this).removeClass('ui-state-hover');
                        }

                ).click(function () {
            jQuery(div).hide();
            if (richiamata && typeof richiamata === 'function')
                richiamata();

            if (chiamante)
                document.location.href = baseUrl + '/' + chiamante;

        }).append("<span class='ui-icon ui-icon-circle-close'></span>");


        //jQuery("#" + tabella + ' .ui-jqgrid-title').before(temp);
        jQuery(div + ' .ui-jqgrid-title').before(temp);
    }
    if (div !== "#dettaglio")
        jQuery(div).show();
}

/*
 * Costruisce la griglia con edit in line e Actions sull'ultima colonna
 *
 * Richiama la action griglia per popolare la griglia, e la action aggiorna per registrare
 * 
 * Se si desidera passare dei parametri aggiuntivi utilizzare
 * anche il secondo parametro "parametriaggiuntivi" i cui elementi devono essere così
 * composti {'nomeparametro' : valore}
 */
function caricaGriglia_inline(parametrijs, parametriaggiuntivi) {
    var lastsel = 0;

    var nomelist = (parametrijs['nomelist'] || "#list1");
    var nomepager = (parametrijs['nomepager'] || "#pager1");
    var titolo = (parametrijs["titolo"] || "Lista");
    var nomemodulo = parametrijs["nomemodulo"];
    var nomicolonne = parametrijs["nomicolonne"];
    var modellocolonne = parametrijs["modellocolonne"];
    var sortname = (parametrijs["sortname"] || modellocolonne[0]["name"]);
    var sortorder = (parametrijs["sortorder"] || "asc");
    var showedit = (parametrijs["showedit"] == 0) ? false : true;
    var showadd = (parametrijs["showadd"] == 0) ? false : true;
    var showdel = (parametrijs["showdel"] == 0) ? false : true;
    var tabella = parametrijs["tabella"];
    var percorsogriglia = parametrijs["percorsogriglia"];
    var datipost = parametrijs["datipost"] || {};
    var viewrownumbers = (parametrijs["viewrownumbers"] == 0) ? false : true;

    var stringapar = "";
    if (typeof parametriaggiuntivi != 'undefined') {
        for (var key in parametriaggiuntivi) {
            if (stringapar != "")
                stringapar += "&";
            stringapar += key + "=" + encodeURI(parametriaggiuntivi[key]);
        }
    }

    /*if (typeof datipost["precondizioni"] != 'undefined') {
     for (var key in datipost["precondizioni"]) {
     if (stringapar != "")
     stringapar += "&";
     stringapar += key + "=" + encodeURI(datipost["precondizioni"][key]);
     }
     }*/
    if (stringapar != "")
        stringapar = "?" + stringapar;
    // 
    // ESEMPIO DI COME DEVE ESSERE COMPOSTO L'ARRAY nomicolonne
    //nomicolonne = ["Domanda", "Gruppo domanda", "Azioni"];

    // ESEMPI DI COME DEVE ESSERE COMPOSTO L'ARRAY modellocolonne
    //
    /*modellocolonne = [
     {
     name:'testo_domanda', 
     index:'testo_domanda', 
     editable: true,
     edittype: "text",
     width:200
     },
     {
     name:'gruppodomanda.descrizione', 
     index:'gruppodomanda.descrizione', 
     editable: true,
     edittype: "select",
     editoptions: {
     value: (parametrijs["decodifiche"]!=null?parametrijs["decodifiche"]:"")
     },
     width:100
     },
     { 
     name:'codice', 
     index:'codice', 
     editable: true,
     edittype: "text",
     editoptions: {maxlength:"3"},
     editrules: {required:true},
     width:100 
     },
     { 
     name:'data_inizio', 
     index:'data_inizio', 
     editable: true,
     editrules: {required:true},
     formatter:"date", 
     formatoptions:{srcformat: 'Y-m-d', newformat: 'd/m/Y', reformatAfterEdit : false},
     editrules: {required:true},
     width:100 
     },
     {
     name: 'valutadalbasso', 
     index: 'valutadalbasso', 
     editable: true,
     formatter: 'checkbox',
     edittype: 'checkbox',
     editoptions: {value: 'true:false', defaultvalue: 'true'},
     width: 100 ));
     },
     
     ];*/


    nomicolonne.push("Azioni");

    modellocolonne.push({
        name: 'act',
        index: 'act',
        width: 55,
        align: 'center',
        sortable: false,
        formatter: 'actions',
        formatoptions: {
            keys: false,
            delbutton: (showdel == true) ? true : false,
            editbutton: (showedit == true) ? true : false,
            onEdit: function (rowid) {
            },
            onSuccess: function (jqXHR) {
                $(nomelist).trigger("reloadGrid");
                //reload(nomelist);
                return true;
            },
            onError: function (rowid, jqXHR, textStatus) {
                alert(textStatus);
            },
            afterSave: function (rowid, jqXHR) {
                risposta = JSON.parse(jqXHR["responseText"]);
                if (risposta.codice < 0) {
                    $("#dialog").dialog({
                        title: 'Attenzione',
                        buttons: {
                            "Ok": function () {
                                $(this).dialog("close");
                            }
                        },
                        modal: true
                    });
                    $("#testodialog").html(risposta.messaggio);
                    $("#dialog").show();
                }
            },
            afterRestore: function (rowid) {
            },
            delOptions: {
                onclickSubmit: function (rp_ge, rowid) {
                    // we can use onclickSubmit function as "onclick" on "Delete" button

                    // reset processing which could be modified
                    rp_ge.processing = true;

                    jQuery(nomelist).jqGrid('delGridRow', rowid, {
                        reloadAfterSubmit: true,
                        url: baseUrl + '/' + tabella + '/delete',
                        afterComplete: function (responseText, textStatus, XMLHttpRequest) {
                            jQuery(nomelist).jqGrid('resetSelection');
                            if (responseText.status == 200 && responseText.responseText == "404") {
                                $("#dialog").dialog({
                                    title: 'Attenzione',
                                    buttons: {
                                        "Ok": function () {
                                            $(this).dialog("close");
                                        }
                                    },
                                    modal: true
                                });
                                $("#testodialog").html("Impossibile cancellare la riga perché usata in altre tabelle.");
                                $("#dialog").show();
                            }
                        }
                    });
                    return true;
                },
                processing: false
            }
        }
    });

    jQuery(nomelist).jqGrid({
        url: baseUrl + '/' + tabella + '/' + percorsogriglia + stringapar,
        datatype: "json",
        colNames: nomicolonne,
        colModel: modellocolonne,
        postData: datipost,
        pager: nomepager,
        rowList: [], // disable page size dropdown
        pgbuttons: false, // disable page control like next, back button
        pgtext: null, // disable pager text like 'Page 0 of 10'
        viewrecords: true,
        sortname: sortname,
        sortorder: sortorder,
        caption: titolo,
        multiselect: false,
        editurl: baseUrl + '/' + tabella + '/aggiorna' + stringapar,
        gridview: true,
        rownumbers: viewrownumbers,
        rowNum: 1000000,
        onEditFunc: function () {
            //alert ("edited"); 
        },
        onSelectRow: function (id) {
            lastsel = id;
            if ((id) && (id !== lastsel)) {
                jQuery(nomelist).restoreRow(lastsel);
            }
        }
    });

    jQuery(nomelist).navGrid(nomepager, {
        search: false,
        edit: false,
        add: false,
        del: false,
        refresh: true
    },
    {}, {}, {},
            {
                multipleSearch: false,
                multipleGroup: false,
                closeAfterSearch: true,
                closeAfterReset: true
            });

    if (showadd == true) {
        jQuery(nomelist).jqGrid('navButtonAdd', nomepager, {
            caption: "Aggiungi",
            buttonicon: "ui-icon-plus",
            onClickButton: function () {
                addparameters = {
                    rowID: "new_row",
                    initdata: {},
                    position: "first",
                    useDefValues: false,
                    useFormatter: true,
                    addRowParams: {
                        extraparam: datipost
                    }
                };
                jQuery(nomelist).jqGrid('addRow', addparameters);

                //jQuery(nomelist).jqGrid('editRow', "new_row", true, '', '', '', '', reload);
                editparameters = {
                    "keys": true,
                    "oneditfunc": null,
                    "successfunc": null,
                    "url": null,
                    "extraparam": datipost,
                    "aftersavefunc": function () {
                        reload(nomelist);
                    },
                    "errorfunc": null,
                    "afterrestorefunc": null,
                    "restoreAfterError": true
                };
                jQuery(nomelist).jqGrid('editRow', "new_row", editparameters);
            },
            position: "last",
            title: "",
            cursor: "pointer"
        });
    }

    if (parametrijs["altezzagriglia"] != null)
        altezzagriglia = parametrijs["altezzagriglia"]
    else
        altezzagriglia = 200;
    if (parametrijs["larghezzagriglia"] != null)
        larghezzagriglia = parametrijs["larghezzagriglia"]
    else
        larghezzagriglia = 800;

    if (larghezzagriglia >= 1000) {
        jQuery(nomelist).jqGrid("setGridWidth", "1000");
    } else if (larghezzagriglia <= 600) {
        jQuery(nomelist).jqGrid("setGridWidth", "600");
    } else
        jQuery(nomelist).jqGrid("setGridWidth", larghezzagriglia);

    jQuery(nomelist).jqGrid("setGridHeight", altezzagriglia);
}

function mostraConfigurazione(parametristampa) {

    var tabella = parametristampa["tabella"];
    var div = parametristampa['div'] || "#dettaglioconf";
    var altezza = 300;
    var larghezza = 700;
    var sinistra = -1;
    var alto = -1;

    parametripassa = {};

    jQuery(div).load(baseUrl + '/' + "tabelle/configura/" + tabella, parametripassa, function (responseText, textStatus) {
        jQuery(div).show();

        if (sinistra > 0 || alto > 0) {
            var o = {
                left: sinistra,
                top: alto
            };

            jQuery(div).offset(o);
        }

        if (altezza !== 0)
            jQuery(div).height(altezza);
        if (larghezza !== 0) {
            jQuery(div).width(larghezza);

        }
    });



}

function stampa(parametristampa) {
    var creaformsta = jQuery("<form id='formstampa' name='formstampa'> </form>");
    if (!jQuery("#formstampa").lenght) {
        jQuery("#nascosto").append(creaformsta);
    }

    var tabella = parametristampa["tabella"] || "";
    var nomelist = parametristampa["nomelist"] || "#list1";

    var indirizzo = parametristampa["indirizzo"] || "tabelle/stampatabella/" + tabella;

    var filtro = jQuery(nomelist).getGridParam("postData");

    var formstp = document.formstampa;
    formstp.setAttribute("method", "post");
    formstp.setAttribute("action", baseUrl + '/' + indirizzo);
    formstp.setAttribute("target", "_blank");

    jQuery.each(parametristampa, function (key, value) {
        if (!filtro[key]) {
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", value);
            hiddenField.setAttribute("type", "hidden");
            formstp.appendChild(hiddenField);
        }
    });

    jQuery.each(filtro, function (key, value) {
        var hiddenField = document.createElement("input");
        hiddenField.setAttribute("name", key);
        hiddenField.setAttribute("value", value);
        hiddenField.setAttribute("type", "hidden");
        formstp.appendChild(hiddenField);


    });

    document.body.appendChild(formstp);    // Not entirely sure if this is necessary
    document.formstampa.submit();
    jQuery("#formstampa").remove();

}

function apriDettaglio(parametri) {
    var tabella = parametri['tabella'];
    var div = parametri['div'] || "#dettaglio";
    var divtesta = parametri['divtesta'] || "#testatadettaglio";
    var parametripassa = parametri['parametripassa'] || "";
    var tipo = parametri['tipo'] || "new";
    var idpassato = parametri['id'] || 0;
    var altezza = parametri['altezza'] || 0;
    var larghezza = parametri['larghezza'] || 0;
    var sinistra = parametri['sinistra'] || -1;
    var alto = parametri['alto'] || -1;
    var is_draggable = (parametri["is_draggable"] == 0) ? false : true;
    var overlayopen = parametri['overlayopen'] || 0;
    var imgwaiturl = parametri['imgwaiturl'];
    var list = parametri['list'] || "#list1";
    var nomedialog = parametri['nomedialog'] || "#dialog";
    var nometestodialog = parametri['nometestodialog'] || "#testodialog";
    var overlay = parametri['overlay'] || "#overlay";

    var rowid;
    if (tipo === "new") {
        rowid = 0;
    } else {
        if (idpassato > 0)
            rowid = idpassato;
        else
            rowid = $(list).jqGrid('getGridParam', 'selrow');
    }

    var editUrl = baseUrl + '/' + tabella + "/" + (rowid > 0 ? rowid + "/" : "") + tipo;
    if ((rowid) || (tipo === "new")) {
        if (altezza !== 0)
            jQuery(div).height(altezza);
        if (larghezza !== 0)
            jQuery(div).width(larghezza);

        jQuery(div).append('<img src="' + imgwaiturl + '" style="position: absolute;top: 50%;left: 50%;-webkit-transform: translate(-50%, -50%);transform: translate(-50%, -50%);" />')
        jQuery(div).load(editUrl, parametripassa, function(responseText, textStatus) {
            if (textStatus === 'error') {
                jQuery(div).html('<p>Si è verificato il seguente errore sul server: </p><br/>' + responseText);
            }

            jQuery(div).show();
            if (is_draggable) {
                jQuery(div).draggable({
                    handle: divtesta
                });
            }

            jQuery(div).resizable();

            if (sinistra > 0 || alto > 0) {
                var o = {
                    left: sinistra,
                    top: alto
                };

                jQuery(div).offset(o);
            }

        });
        if (overlayopen) {
            $(overlay).fadeIn('fast');
            $(div).fadeIn('slow');
        }
    } else {
        if (tipo != "new") {
            $(nomedialog).dialog({
                title: 'Attenzione',
                buttons: {
                    "Ok": function() {
                        $(this).dialog("close");
                    }
                },
                modal: true
            });
            $(nometestodialog).html("Nessuna riga selezionata<br/>" + "Selezionarne una");
            $(nomedialog).show();
        }
    }
}


function chiudiDettaglio(parametri) {
    var div = parametri['div'] || "#dettaglio";
    var overlayclose = parametri['overlayclose'] || 0;
    var refreshgrid = parametri['refreshgrid'] || 0;
    var list = parametri['list'] || '#list1';
    jQuery(".fi-default-salva").removeClass("fi-default-salva");
    jQuery(".fi-default-elimina").removeClass("fi-default-elimina");
    jQuery(div).empty();
    jQuery(div).hide();
    if (overlayclose) {
        $('#overlay').fadeOut('fast');
    }
    if (refreshgrid === 1) {
        jQuery(list).trigger("reloadGrid", [{current: true}]);
    }
}

function salvaDettaglio(parametri) {
    var tabella = parametri['tabella'];
    var percorso = parametri['percorso'];
    var rowid = parametri['id'];
    var div = parametri['div'] || "#dettaglio";
    var continua = parametri['continua'] || 0;
    var refreshgrid = parametri['refreshgrid'] || 0;
    var formdati = parametri['formdati'] || "#formdati";
    var list = parametri['list'] || "#list1";
    var parametriaggiuntivi = parametri["parametriaggiuntivi"] || {};
    if (parametri["overlayclose"] === 0)
        parametri["overlayclose"] = 0;
    else
        parametri["overlayclose"] = 1;

    var percorsoricaricare = parametri['percorsoricaricare'];
    var divricaricare = parametri['divricaricare'];

    var parametripassa = jQuery(formdati).serializeArray();
    $.each(parametriaggiuntivi, function(key, value) {
        parametripassa.push({'name' : 'parametriaggiuntivi['+key+']', 'value' : value});
    });
    jQuery(div).load(baseUrl + '/' + tabella + "/" + (rowid > 0 ? rowid + '/update' : 'create'), parametripassa, function(responseText, textStatus, XMLHttpRequest) {
        //jQuery(div).html(dump(parametripassa));
        //jQuery(div).html('<p>Si è verificato il seguente errore sul server: </p><br/>'+responseText);
        var trovatoerrore = responseText.search("Errors");
        var errorivalidazione = responseText.search("error_list");
        var richiesti = responseText.search("Required");
        if (textStatus === 'error' || trovatoerrore !== -1) {
            jQuery(div).html('<p>Si è verificato il seguente errore sul server: </p><br/>' + responseText);
        } else {
            if ((richiesti !== -1) || (errorivalidazione !== -1)) {
                jQuery(div).html(responseText);
                jQuery(".error_list").prepend("Controllare errori:");
            } else {
                jQuery(div).html(responseText);
                if (continua === 0) {
                    chiudiDettaglio(parametri);
                } else {
                    //Questa parte serve per la gestione del 'Salva e inserisci nuovo'
                    //Se vogliamo rimanere nella form (continua = 1) e vogliamo anche refreshare la griglia (refreshgrid = 1)
                    if (refreshgrid === 1) {
                        jQuery(list).trigger("reloadGrid", [{current: true}]);
                    }
                }
            }
        }

        jQuery(list).trigger("reloadGrid", [{
                current: true
            }]);

    });
}

function salvaDettaglio_piu_parametri(parametri, parametriaggiuntivi) {
    var tabella = parametri['tabella'];
    var percorso = parametri['percorso'];
    var rowid = parametri['id'];
    var div = parametri['div'] || "#dettaglio";
    var continua = parametri['continua'] || 0;
    var formdati = parametri['formdati'] || "#formdati";
    var list = parametri['list'] || "#list1";
    var percorsoricaricare = parametri['percorsoricaricare'];
    var divricaricare = parametri['divricaricare'];

    var stringapar = "";
    for (var key in parametriaggiuntivi) {
        stringapar += "&" + key + "=" + parametriaggiuntivi[key];
    }

    var parametripassa = jQuery(formdati).serializeArray();

    jQuery(div).load(baseUrl + '/' + tabella + "/" + (rowid > 0 ? 'update' : 'create') + '?id=' + rowid + stringapar, parametripassa, function(responseText, textStatus, XMLHttpRequest) {
        var trovatoerrore = responseText.search("Errors");
        var errorivalidazione = responseText.search("error_list");
        var richiesti = responseText.search("Required");
        if (textStatus === 'error' || trovatoerrore !== -1) {
            jQuery(div).html('<p>Si è verificato il seguente errore sul server: </p><br/>' + responseText);
        } else {
            if ((richiesti !== -1) || (errorivalidazione !== -1)) {
                jQuery(div).html(responseText);
                jQuery(".error_list").prepend("Controllare errori:");
            } else {
                jQuery(div).html(responseText);
                if (continua === 0)
                    jQuery(div).hide();
            }
        }
        jQuery(list).trigger("reloadGrid", [{
                current: true
            }]);
    });
}

function eliminaDettaglio(parametri) {
    /*var tabella = parametri["tabella"];
     var rowid = parametri['id'];
     //var multisel = parametri['multisel'];
     var div = parametri['div'] || "#dettaglio";
     var continua = parametri['continua'] || 0;
     var list = parametri['list'] || "#list1";
     if (parametri["overlayclose"] === 0)
     parametri["overlayclose"] = 0;
     else
     parametri["overlayclose"] = 1;
     
     jQuery(list).jqGrid('delGridRow', rowid, {
     reloadAfterSubmit: true,
     url: baseUrl + '/' + tabella + '/delete',
     afterComplete: function(responseText, textStatus, XMLHttpRequest) {
     jQuery(list).jqGrid('resetSelection');
     if (responseText.status === 200 && responseText.responseText === "404") {
     jQuery("#dialog").dialog({
     title: 'Attenzione',
     buttons: {
     "Ok": function() {
     jQuery(this).dialog("close");
     }
     },
     modal: true
     });
     jQuery("#testodialog").html("Impossibile cancellare la riga (per esempio potrebbe essere usata in altre tabelle).");
     jQuery("#dialog").show();
     } else {
     jQuery(list).trigger("reloadGrid", [{
     current: true
     }]);
     
     }
     if (continua === 0) {
     chiudiDettaglio(parametri);
     }
     
     
     }
     
     });*/
    var tabella = parametri["tabella"];
    //var rowid = parametri['id'];
    //var multisel = parametri['multisel'];
    var continua = parametri['continua'] || 0;
    var list = parametri['list'] || "#list1";
    var nomedialog = parametri['nomedialog'] || "#dialog";
    var nometestodialog = parametri['nometestodialog'] || "#testodialog";
    var div = parametri['div'] || "#dettaglio";
    var rowid = parametri['id'] || $(list).jqGrid('getGridParam', 'selrow');

    if (parametri["overlayclose"] === 0)
        parametri["overlayclose"] = 0;
    else
        parametri["overlayclose"] = 1;

    if (rowid) {
        jQuery(list).jqGrid('delGridRow', rowid, {
            reloadAfterSubmit: true,
            url: baseUrl + '/' + tabella + '/delete',
            afterComplete: function(responseText, textStatus, XMLHttpRequest) {
                jQuery(list).jqGrid('resetSelection');
                if (responseText.status === 200 && responseText.responseText === "404") {
                    jQuery(nomedialog).dialog({
                        title: 'Attenzione',
                        buttons: {
                            "Ok": function() {
                                jQuery(this).dialog("close");
                            }
                        },
                        modal: true
                    });
                    jQuery(nometestodialog).html("Impossibile cancellare la riga (per esempio potrebbe essere usata in altre tabelle).");
                    jQuery(nomedialog).show();
                } else {
                    jQuery(list).trigger("reloadGrid", [{
                            current: true
                        }]);
                }
                if (continua === 0) {
                    chiudiDettaglio(parametri);
                }
            }
        });
    } else {
        $(nomedialog).dialog({
            title: 'Attenzione',
            buttons: {
                "Ok": function() {
                    $(this).dialog("close");
                }
            },
            modal: true
        });
        $(nometestodialog).html("Nessuna riga selezionata<br/>" + "Selezionarne una");
        $(nomedialog).show();
    }
}

function dopolaricerca(parametripassati) {

    var tabella = parametripassati["tabella"];
    var larghezza = jQuery("#" + tabella).width();
    var filtro = jQuery("#list1").getGridParam("postData");

    jQuery.get(baseUrl + '/' + 'funzioni/traduzionefiltro', filtro, function(data) {

        jQuery("#notecorpo").html(data);
        jQuery("#notecorpo").width(larghezza);
        jQuery("#notecorpo").show();

    });



}

function FindDefaultOption(opzioniedit)
{
    var defaulteditoptions = "";
    for (var y = 0; y < opzioniedit.length; y++) {
        if (opzioniedit[y]["default"] == true)
            defaulteditoptions = opzioniedit[y]["descrizione"];
    }
    return defaulteditoptions;
}

function SetEditOptions(opzioniedit)
{
    var editoptions = "";
    for (var y = 0; y < opzioniedit.length; y++) {

        if (editoptions !== "")
            editoptions = editoptions + ":";
        editoptions = editoptions + opzioniedit[y]["descrizione"];
    }
    return editoptions;
}

function SetSearchOptions(opzioniedit)
{
    var searchoptions = "";
    for (var y = 0; y < opzioniedit.length; y++) {

        if (opzioniedit[y]["valore"] === "") {
            if (searchoptions !== "")
                searchoptions = searchoptions + ";";
            searchoptions = searchoptions + ":" + opzioniedit[y]["descrizione"];
        }
        else {
            if (searchoptions !== "")
                searchoptions = searchoptions + ";";
            if (!jQuery.isNumeric(opzioniedit[y]["valore"]))
                searchoptions = searchoptions + "'" + opzioniedit[y]["valore"] + "':" + opzioniedit[y]["descrizione"];
            else
                searchoptions = searchoptions + opzioniedit[y]["valore"] + ":" + opzioniedit[y]["descrizione"];
        }
    }
    return searchoptions;
}

function messaggio(parametri) {

    var div = parametri["div"] || "#messaggio";
    var testo = parametri["testo"] || "Messaggio";
    var funzioneok = parametri["funzioneok"] || null;
    var nomefunzionenull = parametri["nomefunzionenull"] || null;

    jQuery(div).empty();
    jQuery(div).html("<div id='dialog'><div id='testodialog'></div></div>");


    var bottoni = {
        "Ok": function() {
            var strFun = funzioneok;
            var strParam = parametri;
            var fn = window[strFun];
            fn(strParam);

            jQuery(this).dialog("close");

        }
    };
    if (nomefunzionenull) {
        bottoni[nomefunzionenull] = (function() {
            jQuery(this).dialog("close");
        });
    }

    //jQuery('body').append(div);

    jQuery("#testodialog").html(testo);

    jQuery("#dialog").dialog({
        title: 'Attenzione',
        modal: true,
        buttons: bottoni
    });
    jQuery(div).show();

    return true;

}

function reload(nomelist) {
    $(nomelist).trigger("reloadGrid");
}
