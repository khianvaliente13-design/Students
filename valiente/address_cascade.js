(function () {
  function fillBarangays(munSelect, barSelect, data, selectedBarangay) {
    var m = munSelect.value;
    if (!m || !data[m]) {
      barSelect.innerHTML =
        '<option value="" disabled selected>Choose municipality first, then barangay</option>';
      return;
    }
    barSelect.innerHTML =
      '<option value="" disabled selected>Select barangay</option>';
    var list = data[m];
    var found = false;
    for (var i = 0; i < list.length; i++) {
      var b = list[i];
      var o = document.createElement("option");
      o.value = b;
      o.textContent = b;
      if (selectedBarangay && selectedBarangay === b) {
        o.selected = true;
        found = true;
        barSelect.options[0].removeAttribute("selected");
      }
      barSelect.appendChild(o);
    }
    if (!found && selectedBarangay) {
      var extra = document.createElement("option");
      extra.value = selectedBarangay;
      extra.textContent = selectedBarangay + " (saved)";
      extra.selected = true;
      barSelect.options[0].removeAttribute("selected");
      barSelect.appendChild(extra);
    }
  }

  window.ValienteAddressInit = function (cfg) {
    var mun = document.getElementById(cfg.municipalityId);
    var bar = document.getElementById(cfg.barangayId);
    var data = cfg.data || {};
    if (!mun || !bar) return;

    if (cfg.initialMunicipality) {
      mun.value = cfg.initialMunicipality;
    }
    fillBarangays(mun, bar, data, cfg.initialBarangay || "");

    mun.addEventListener("change", function () {
      fillBarangays(mun, bar, data, "");
    });
  };
})();
