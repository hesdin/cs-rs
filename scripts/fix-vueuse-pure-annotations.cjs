const fs = require('node:fs');
const path = require('node:path');

const candidates = [
    path.resolve('node_modules/reka-ui/node_modules/@vueuse/core/dist/index.js'),
    path.resolve('node_modules/@vueuse/core/dist/index.js'),
];

for (const file of candidates) {
    if (!fs.existsSync(file)) {
        continue;
    }

    let contents = fs.readFileSync(file, 'utf8');

    contents = contents.replace('//#endregion\n//#region useEventBus/internal.ts\n/* #__PURE__ */\nconst events = /* @__PURE__ */ new Map();', '//#endregion\n//#region useEventBus/internal.ts\nconst events = /* @__PURE__ */ new Map();');
    contents = contents.replace('const defaultState = (/* #__PURE__ */ {\n', 'const defaultState = {\n');
    contents = contents.replace('\n});\nconst keys = /* @__PURE__ */ Object.keys(defaultState);', '\n};\nconst keys = /* @__PURE__ */ Object.keys(defaultState);');

    fs.writeFileSync(file, contents);
}
