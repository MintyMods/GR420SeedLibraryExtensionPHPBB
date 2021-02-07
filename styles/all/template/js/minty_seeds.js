
$(document).ready(function () {
  if ($('#minty_seed_grid').length > 0) {
    console.info("Loading Minty Seed Lib");
    buildSeedGrid();
  }
});

const label_width = 120;
const expire = 3000;

const GRID_SELECT_URL = "GRID_SELECT_RECORDS";
const SEED_POST_URL = "SEED_POST";
const BREEDER_POST_URL = "BREEDER_POST";
const BREEDER_SELECT_URL = "BREEDER_SELECT_RECORD";
const BREEDER_OPTIONS_URL = "BREEDER_OPTIONS";
const GENETICS_OPTIONS_URL = "GENETICS_OPTIONS";
const SMELLS_OPTIONS_URL = "SMELLS_OPTIONS";
const EFFECTS_OPTIONS_URL = "EFFECTS_OPTIONS";
const TASTES_OPTIONS_URL = "TASTES_OPTIONS";
const META_TAGS_OPTIONS_URL = "META_TAGS_OPTIONS";
const AWARDS_OPTIONS_URL = "AWARDS_OPTIONS";

const flowering_type_options = {
  cols: [
    { type: "radioButton", text: "Auto", value: "A", },
    { type: "radioButton", text: "Photo", value: "P" },
    { type: "radioButton", text: "Regular", value: "R" },
  ]
};

const sex_options = {
  cols: [
    { type: "radioButton", text: "Male", value: "M", },
    { type: "radioButton", text: "Female", value: "F" }
  ]
};

const indoor_outdoor_options = {
  cols: [
    { id: "indoor_yn", type: "checkbox", text: "Indoor", },
    { id: "outdoor_yn", type: "checkbox", text: "Outdoor", },
  ]
};

const month_options = [
  { value: "", content: "Select Month" },
  { value: "jan", content: "January" },
  { value: "feb", content: "February" },
  { value: "mar", content: "March" },
  { value: "apr", content: "April" },
  { value: "may", content: "May" },
  { value: "jun", content: "June" },
  { value: "jul", content: "July" },
  { value: "aug", content: "Augest" },
  { value: "sep", content: "September" },
  { value: "oct", content: "October" },
  { value: "nov", content: "November" },
  { value: "dec", content: "December" }
];

var seedGrid, seedForm, seedWindow, breederForm, breederWindow;

function buildSeedGrid() {
  seedGrid = new dhx.Grid("minty_seed_grid", {
    columns: [
      { width: 50, id: "id", header: [{ text: "ID" }] },
      { width: 150, id: "breeder_name", header: [{ text: "Breeder" }, { content: "comboFilter" }], type: "string", editorType: "combobox" },
      { width: 150, id: "seed_name", type: "string", header: [{ text: "Name" }, { content: "inputFilter" }], type: "string", editorType: "input" },
      { width: 100, id: "flowering_type", header: [{ text: "Type" }, { content: "comboFilter" }], type: "string", editorType: "combobox", options: [' ', 'Regular', 'Feminised', 'Auto'] },
      { width: 80, id: "sex", header: [{ text: "Sex" }, { content: "comboFilter" }], type: "string", editorType: "combobox", options: [' ', 'Male', 'Female'] },
      { width: 50, id: "indoor_yn", type: "boolean", editorType: "checkbox", header: [{ text: "Indoor" }] },
      { width: 50, id: "outdoor_yn", type: "boolean", editorType: "checkbox", header: [{ text: "Outdoor" }] },
      { width: 110, id: "flowering_time", header: [{ text: "Flowering Time" }, { content: "comboFilter" }], type: "string", editorType: "input" },
      { width: 110, id: "harvest_month", type: "date", dateFormat: "%M", header: [{ text: "Harvest Month" }, { content: "comboFilter" }], type: "string", editorType: "input" },
      { width: 110, id: "thc", type: "string", header: [{ text: "THC" }, { content: "comboFilter" }], type: "string", editorType: "input" },
      { width: 110, id: "cbd", type: "string", header: [{ text: "CBD" }, { content: "comboFilter" }], type: "string", editorType: "input" },
      { width: 110, id: "indica", type: "string", header: [{ text: "Indica" }, { content: "comboFilter" }], type: "string", editorType: "input" },
      { width: 110, id: "sativa", type: "string", header: [{ text: "Sativa" }, { content: "comboFilter" }], type: "string", editorType: "input" },
      { width: 110, id: "ruderalis", type: "nstring", header: [{ text: "Ruderalis" }, { content: "comboFilter" }], type: "string", editorType: "input" },
      { width: 110, id: "yeild_indoors", type: "string", header: [{ text: "Indoor Yeild" }, { content: "comboFilter" }], type: "string", editorType: "input" },
      { width: 110, id: "yeild_outdoors", type: "string", header: [{ text: "Outdoor Yeild" }, { content: "comboFilter" }], type: "string", editorType: "input" },
      { width: 110, id: "height_indoors", type: "string", header: [{ text: "Indoor Height" }, { content: "comboFilter" }], type: "string", editorType: "input" },
      { width: 110, id: "height_outdoors", type: "string", header: [{ text: "Outdoor Height" }, { content: "comboFilter" }], type: "string", editorType: "input" },
      { width: 200, id: "minty_sl_genetics", header: [{ text: "Genetics" }, { content: "comboFilter" }], type: "string", editorType: "combobox" },
      { width: 200, id: "minty_sl_smells", header: [{ text: "Smells" }, { content: "comboFilter" }], type: "string", editorType: "combobox" },
      { width: 200, id: "minty_sl_tastes", header: [{ text: "Tastes" }, { content: "comboFilter" }], type: "string", editorType: "combobox" },
      { width: 200, id: "minty_sl_effects", header: [{ text: "Effects" }, { content: "comboFilter" }], type: "string", editorType: "combobox" },
      { width: 200, id: "minty_sl_meta_tag", header: [{ text: "Effects" }, { content: "comboFilter" }], type: "string", editorType: "combobox" },
      { width: 200, id: "seed_desc", header: [{ text: "Desc" }, { content: "inputFilter" }] },
      { width: 200, id: "forum_url", header: [{ text: "Forum Link" }] },
    ],
    editable: false,
    autoEmptyRow: false,
    height: 620,
    multiselection: false,
    selection: "row",
    resizable: true,
  });
  seedGrid.data.load(new dhx.LazyDataProxy(GRID_SELECT_URL, { limit: 15, prepare: 0, delay: 10, from: 0 }));
  buildAddSeedButton();
}

function buildSeedWindow() {
  if (!seedForm) {
    buildSeedForm();
  }
  seedWindow = new dhx.Window({ title: "Add New Seed Entry", modal: true, resizable: true, movable: true, closable: true, header: true, footer: true, height: 600, width: 600, minHeight: 400, minWidth: 400, });
  seedWindow.footer.data.add([
    { type: "spacer" },
    { id: "save_button", type: "button", icon: "dxi dxi-checkbox-marked-circle", view: "flat", size: "medium", color: "primary", value: "Save", submit: true, },
    { id: "save_new_button", type: "button", value: "Save & New", size: "medium", view: "flat", color: "primary", icon: "dxi dxi-plus-circle", },
    { id: "cancel_button", type: "button", icon: "dxi dxi-close-circle", size: "medium", color: "secondary", value: "Cancel", },
  ], 0);
  seedWindow.footer.events.on("click", function (id) {
    if (id === "cancel_button") {
      seedWindow.hide()
    } else if (id === "save_new_button") {
      //@todo
    } else if (id === "save_button") {
      if (seedForm.validate()) {
        seedForm.send(SEED_POST_URL, "POST", true).then(function () {
          debugger;
          seedWindow.hide();
        });
      } else {
        err("Failed to validate form");
      }
    }
  });
  seedWindow.attach(seedForm);
}

function buildSeedForm() {
  seedForm = new dhx.Form('seedForm', {
    css: "dhx_widget--bordered",
    rows: [
      { name: "seed_id", type: "text", label: "ID", hidden: true, },
      {
        cols: [
          { name: "breeder_id", type: "combo", width: 340, label: "Breeder", labelPosition: "left", required: true, labelWidth: label_width, errorMessage: "You must select a valid breeder from the list" },
          { name: "add_breader_button", type: "button", text: "Add New Breader", size: "medium", width: 160, view: "flat", icon: "dxi dxi-plus", color: "secondary", view: "link", },]
      },
      { name: "seed_name", type: "input", label: "Name", labelPosition: "left", labelWidth: label_width, required: true, placeholder: "Name of the Plant?", errorMessage: "Plant name is mandatory to save a new record" },
      { name: "flowering_type", type: "radioGroup", required: true, label: "Type", labelWidth: label_width, labelPosition: "left", errorMessage: "Flowering type is a required field", options: flowering_type_options, },
      { name: "sex", type: "radioGroup", required: true, label: "Sex", errorMessage: "Sex is a required field", labelWidth: label_width, labelPosition: "left", options: sex_options, },
      { name: "indoor_outdoor", type: "checkboxGroup", label: "Environment", labelWidth: label_width, labelPosition: "left", labelInline: true, options: indoor_outdoor_options, },
      { name: "thc", type: "input", label: "THC", labelPosition: "left", labelWidth: label_width, },
      { name: "cbd", type: "input", label: "CBD", labelPosition: "left", labelWidth: label_width, },
      { name: "indica", type: "input", label: "Indica", labelPosition: "left", labelWidth: label_width, },
      { name: "sativa", type: "input", label: "Sativa", labelPosition: "left", labelWidth: label_width, },
      { name: "ruderalis", type: "input", label: "Ruderalis", labelPosition: "left", labelWidth: label_width, },
      { name: "yeild_indoors", type: "input", label: "Indoor Yeild", labelPosition: "left", labelWidth: label_width, },
      { name: "yeild_outdoors", type: "input", label: "Outdoor Yeild", labelPosition: "left", labelWidth: label_width, },
      { name: "height_indoors", type: "input", label: "Indoor Height", labelPosition: "left", labelWidth: label_width, },
      { name: "height_outdoors", type: "input", label: "Outdoor Height", labelPosition: "left", labelWidth: label_width, },
      { name: "flowering_time", type: "input", label: "Flowering Time", labelPosition: "left", labelWidth: label_width, },
      { name: "harvest_month", type: "select", label: "Harvest Month", labelPosition: "left", labelWidth: label_width, options: month_options },
      { name: "seed_desc", type: "textarea", label: "Description", labelPosition: "left", labelWidth: label_width, },
      { name: "minty_sl_genetics", type: "combo", label: "Genetics", labelPosition: "left", labelWidth: label_width, multiselection: true, },
      { name: "minty_sl_awards", type: "combo", label: "Awards", labelPosition: "left", labelWidth: label_width, multiselection: true, },
      { name: "minty_sl_smells", type: "combo", label: "Smell", labelPosition: "left", labelWidth: label_width, multiselection: true, },
      { name: "minty_sl_tastes", type: "combo", label: "Taste", labelPosition: "left", labelWidth: label_width, multiselection: true, },
      { name: "minty_sl_effects", type: "combo", label: "Effect", labelPosition: "left", labelWidth: label_width, multiselection: true, },
      { name: "minty_sl_meta_tags", validation: validateTag, type: "combo", label: "Tags", labelPosition: "left", labelWidth: label_width, multiselection: true, },
    ]
  });
  seedForm.getItem("add_breader_button").events.on("Click", showAddBreederWindow);
  seedForm.getItem("breeder_id").getWidget().data.load(BREEDER_OPTIONS_URL);
  seedForm.getItem("minty_sl_smells").getWidget().data.load(SMELLS_OPTIONS_URL);
  seedForm.getItem("minty_sl_effects").getWidget().data.load(EFFECTS_OPTIONS_URL);
  seedForm.getItem("minty_sl_tastes").getWidget().data.load(TASTES_OPTIONS_URL);
  seedForm.getItem("minty_sl_awards").getWidget().data.load(AWARDS_OPTIONS_URL);
  seedForm.getItem("minty_sl_meta_tags").getWidget().data.load(META_TAGS_OPTIONS_URL);
  seedForm.getItem("minty_sl_genetics").combobox.data.load(GENETICS_OPTIONS_URL);

  var EVENT = "keypress";
  var control = seedForm.getItem("minty_sl_meta_tags");
  var widget = control.getWidget();
  widget.events.on(EVENT, function (val, event) {
    console.log("WIDGET(" + EVENT + "):", val);
  })

  control.events.on(EVENT, function (val, event) {
    console.log("CONTROL(" + EVENT + "):", val);
  })

}

function validateTag(value, event) {
  console.log("Validate Tag", value);
}

function showAddBreederWindow() {
  if (!breederWindow) {
    buildBreederWindow();
  }
  breederWindow.show();
  breederForm.setFocus("breeder_name");
}

function buildBreederWindow() {
  if (!breederForm) {
    buildBreederForm();
  }
  breederWindow = new dhx.Window({ title: "Add New Breeder", modal: true, resizable: true, movable: true, closable: true, header: true, footer: true, minWidth: 520, minHeight: 380, height: 400, });
  breederWindow.footer.data.add([
    { type: "spacer" },
    { id: "breeder_save", type: "button", value: "Save", view: "flat", color: "primary", icon: "dxi dxi-checkbox-marked-circle", },
    { id: "breeder_cancel", type: "button", value: "Cancel", view: "flat", color: "secondary", icon: "dxi dxi-close-circle", },
    // { id: "breeder_delete", type: "button", disabled: true, value: "Delete", view: "flat", color: "danger", icon: "dxi dxi-alert-circle", },
  ], 0);

  breederWindow.footer.events.on("click", function (id) {
    data = breederForm.getValue();
    switch (id) {
      case 'breeder_cancel': {
        breederWindow.hide();
        break;
      }
      case 'breeder_save': {
        if (breederForm.validate()) {
          breederForm.send(BREEDER_POST_URL, "POST", true).then(breederFormSaved).catch(function (e) {
            err(e.statusText);
          });
        } else {
          breederForm.setFocus("breeder_name");
          err("Validation Failed");
        }
        break;
      }
    }
  });
  breederWindow.attach(breederForm);
}

function buildBreederForm() {
  breederForm = new dhx.Form(null, {
    rows: [
      { type: "input", hidden: true, id: "breeder_id" },
      { type: "input", label: "Name", required: true, id: "breeder_name", labelPosition: "left", labelWidth: label_width, errorMessage: "Breeder name is a required field", validation: validateBreeder },
      { type: "textarea", label: "Description", id: "breeder_desc", labelPosition: "left", labelWidth: label_width, },
      { type: "input", label: "URL", id: "breeder_url", labelPosition: "left", labelWidth: label_width, },
      { type: "checkbox", label: "Sponsor", id: "sponsor_yn", labelInline: true, labelPosition: "left", labelWidth: label_width, }
    ]
  })
}

function breederFormSaved(response) {
  var json = JSON.parse(response);
  if (json.saved) {
    debugger;
    seedForm.setValue(json.data);
    msg("Breeder Details Saved");
    breederForm.clear();
    breederWindow.hide();
  } else {
    err("Failed to save Breeder Details!");
  }
}

function validateBreeder(value) {
  var data = seedForm.getItem("breeder_id").combobox.data;
  var control = breederForm.getItem("breeder_name");
  var valid = true;
  if (value == "") {
    control.config.errorMessage = "Breeder Name is a required field!";
    valid = false;
  } else {
    data.forEach(function (option) {
      if (clean(option.value) == clean(value)) {
        control.config.errorMessage = "Breeder '" + option.value + "' already exists!";
        valid = false;
      }
    });
  }
  return valid;
}

function buildAddSeedButton() {
  var buttons = new dhx.Form("minty_add_button", {
    css: "dhx_widget--bordered",
    rows: [{
      align: "end",
      cols: [{ name: "add_button", type: "button", text: "New", size: "medium", view: "flat", color: "primary", icon: "dxi dxi-plus-circle", }]
    }]
  });
  buttons.getItem("add_button").events.on("Click", function (events) {
    if (!seedWindow) {
      buildSeedWindow();
    }
    seedWindow.show();
  });
}

function clean(text) {
  return text ? text.trim().toLowerCase() : "";
}

function msg(text) {
  console.log(text);
  dhx.message({ text, css: "dhx_message--success", icon: "dxi-checkbox-marked-circle", expire });
}

function err(text) {
  console.error(text);
  dhx.message({ text, css: "dhx_message--error", icon: "dxi-close", expire });
}