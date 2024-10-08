function classDataActive() {
  var rules = [];

  class Rule {
    constructor(element, options) {
      const {
        targets = Rule.findTargets(element),
        action = "toggle",
        deactivateOutsideArea = false,
        classWhenAdd = "active",
        classWhenRemove = "leave",
        removeClassDuration = 600,
        autoClose = 0,
        active = false,
      } = options ? options : {};
      this.element = element;
      this.targets = targets;
      this.action = action;
      this.deactivateOutsideArea = deactivateOutsideArea;
      this.classWhenAdd = classWhenAdd;
      this.classWhenRemove = classWhenRemove;
      this.classWhenRemoveDuration = removeClassDuration;
      this.active = active;
      this.autoClose = autoClose;
    }
    static findTargets(element) {
      const params = element.dataset.active;
      let targets = [element];
      if (params && params.length > 0) {
        targets = Array.from(document.querySelectorAll(params));
      }
      return targets;
    }
    defaultAction() {
      switch (this.action) {
        case "toggle":
          this.toggleClass();
          break;
        case "add":
          this.addClass();
          break;
        case "remove":
          this.removeClass();
          break;
        default:
          break;
      }
    }
    addClass() {
      const classadd = this.classWhenAdd;
      console.log(classadd);
      this.targets.forEach((t) => {
        t.classList.add(classadd);
      });
      this.active = true;
      if (this.autoClose > 0) {
        setTimeout(() => {
          this.removeClass();
          this.active = false;
        }, this.autoClose);
      }
    }
    removeClass() {
      const classadd = this.classWhenAdd,
        classremove = this.classWhenRemove,
        duration = this.classWhenRemoveDuration;
      this.targets.forEach((t) => {
        if (t.classList.contains(classadd)) {
          t.classList.remove(classadd);
          if (classremove.length > 0) {
            t.classList.add(classremove);
            setTimeout(() => t.classList.remove(classremove), duration);
          }
        }
      });
      this.active = false;
    }
    toggleClass() {
      if (this.active) {
        this.removeClass();
      } else {
        this.addClass();
      }
    }
    targetContains(element) {
      for (let i = 0; i < this.targets.length; i++) {
        if (this.targets[i].contains(element)) {
          return true;
        }
      }
      return false;
    }
  }
  document.addEventListener("readystatechange", () => {
    if (
      document.readyState === "interactive" ||
      document.readyState === "complete"
    ) {
      findRules();
    }
  });
  document.addEventListener("pjax:success", findRules);
  document.addEventListener("mousedown", clicked);
  document.addEventListener("keydown", clicked);

  function findRules() {
    rules = [];
    let activeElements = document.querySelectorAll("[data-active]");
    activeElements.forEach((e) => {
      rules.push(new Rule(e));
    });
    customRules();
  }

  function clicked(event) {
    if (event.key && event.key != "Enter") {
      return;
    }
    let stack = event.path || (event.composedPath && event.composedPath());
    let clickInTarget = false;
    let firstActive = null;
    for (var i = 0; i < stack.length - 2; i++) {
      const element = stack[i];
      if (clickInTarget === false) {
        for (let i = 0; i < rules.length; i++) {
          if (rules[i].targetContains(element)) {
            clickInTarget = true;
            break;
          }
        }
      }
      if (element.dataset.active) {
        firstActive = rules.find((r) => r.element === element);
        break;
      }
    }
    if (clickInTarget === false && firstActive === null) {
      rules.forEach((r) => {
        if (r.deactivateOutsideArea) {
          r.removeClass();
        }
      });
      return;
    }
    if (clickInTarget === true && firstActive === null) {
      return;
    }
    firstActive.defaultAction();
  }
  function customRules() {
    rules.forEach((r) => {
      if (r.element == document.querySelector(".modal-share .copy")) {
        r.autoClose = 3000;
      }
    });
  }
}
classDataActive();
