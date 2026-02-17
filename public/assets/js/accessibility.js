(function () {
  var STORAGE_KEY = "ecidade_a11y_pref_v1";

  function getPref() {
    try {
      var raw = localStorage.getItem(STORAGE_KEY);
      if (!raw) {
        return {
          contrast: "normal",
          fontScale: 1,
          colorFilter: "none",
        };
      }
      return JSON.parse(raw);
    } catch (e) {
      return {
        contrast: "normal",
        fontScale: 1,
        colorFilter: "none",
      };
    }
  }

  function setPref(pref) {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(pref));
    } catch (e) {}
  }

  function apply(pref) {
    var html = document.documentElement;
    html.setAttribute("data-contrast", pref.contrast || "normal");
    html.setAttribute("data-color-filter", pref.colorFilter || "none");
    html.style.setProperty("--a11y-font-scale", String(pref.fontScale || 1));
  }

  function bindToolbar() {
    var toolbar = document.querySelector("[data-a11y-toolbar]");
    if (!toolbar) {
      return;
    }

    var pref = getPref();
    apply(pref);

    var contrastBtn = toolbar.querySelector("[data-a11y-contrast]");
    var fontPlus = toolbar.querySelector("[data-a11y-font-plus]");
    var fontMinus = toolbar.querySelector("[data-a11y-font-minus]");
    var resetBtn = toolbar.querySelector("[data-a11y-reset]");
    var filterSelect = toolbar.querySelector("[data-a11y-filter]");
    var scaleLabel = toolbar.querySelector("[data-a11y-scale-label]");

    function refreshLabel() {
      if (scaleLabel) {
        scaleLabel.textContent = Math.round((pref.fontScale || 1) * 100) + "%";
      }
      if (filterSelect) {
        filterSelect.value = pref.colorFilter || "none";
      }
      if (contrastBtn) {
        contrastBtn.textContent = pref.contrast === "high" ? "Contraste: Alto" : "Contraste: Normal";
      }
    }

    refreshLabel();

    if (contrastBtn) {
      contrastBtn.addEventListener("click", function () {
        pref.contrast = pref.contrast === "high" ? "normal" : "high";
        apply(pref);
        setPref(pref);
        refreshLabel();
      });
    }

    if (fontPlus) {
      fontPlus.addEventListener("click", function () {
        pref.fontScale = Math.min(1.8, (pref.fontScale || 1) + 0.1);
        apply(pref);
        setPref(pref);
        refreshLabel();
      });
    }

    if (fontMinus) {
      fontMinus.addEventListener("click", function () {
        pref.fontScale = Math.max(0.8, (pref.fontScale || 1) - 0.1);
        apply(pref);
        setPref(pref);
        refreshLabel();
      });
    }

    if (filterSelect) {
      filterSelect.addEventListener("change", function () {
        pref.colorFilter = filterSelect.value;
        apply(pref);
        setPref(pref);
        refreshLabel();
      });
    }

    if (resetBtn) {
      resetBtn.addEventListener("click", function () {
        pref = {
          contrast: "normal",
          fontScale: 1,
          colorFilter: "none",
        };
        apply(pref);
        setPref(pref);
        refreshLabel();
      });
    }
  }

  document.addEventListener("DOMContentLoaded", function () {
    apply(getPref());
    bindToolbar();
  });
})();
