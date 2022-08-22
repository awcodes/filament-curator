const fs = require("fs");
const axios = require("axios").default;

function getStylesArray(css) {
  // const tree = [];

  // css.split("}}").map(function (rule) {
  //   console.log(rule.split(")"));
  //   tree[rule.split(")")[0]] = rule
  //     .split(")")[1]
  //     ?.split("}")
  //     .map(function (rule) {
  //       return rule ? rule.split("{")[0] : "";
  //     })
  //     .filter(function (rule, index, self) {
  //       return self.indexOf(rule) === index;
  //     });
  // });

  // return tree;
  return css
    .split("}")
    .map(function (rule) {
      return rule ? rule.split("{")[0] : "";
    })
    .filter(function (rule, index, self) {
      return self.indexOf(rule) === index;
    });
}

function getStylesSelectors(css) {
  return css
    .split("}")
    .map(function (rule) {
      return rule ? rule.trim() + "}" : "";
    })
    .filter(function (rule, index, self) {
      return self.indexOf(rule) === index;
    });
}

const pluginCss = "./resources/dist/filament-curator.css";

axios
  .get("https://raw.githubusercontent.com/filamentphp/filament/2.x/packages/admin/dist/app.css")
  .then(({ data }) => {
    const pluginStylesRaw = fs.readFileSync(pluginCss, "utf8");
    const filamentStylesArray = getStylesArray(data);

    let pluginStylesArray = getStylesArray(pluginStylesRaw);
    let pluginStyles = getStylesSelectors(pluginStylesRaw);

    diffedStyles = pluginStylesArray.filter((val) => !filamentStylesArray.includes(val));

    newPluginStyles = pluginStyles.filter((val) => {
      return diffedStyles.includes(val.split("{")[0]);
    });

    fs.writeFileSync(pluginCss, newPluginStyles.join(""));
  })
  .catch((e) => console.log(e));
