import { defineConfig, loadEnv } from "vite";
import fileSystem from "fs";
import path from "path";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";

const themeDirectory = "./amuz-themes/";
let amuzThemes = {
    "@": "amuz-themes/amuz/default/resources/js",
};
let inputs = ["amuz-themes/amuz/default/resources/js/app.js"];

async function setInputs() {
    console.log("start build alias");
    let vendors = [];
    const vendorDirectories = fileSystem.readdirSync(themeDirectory);
    for (let vendorIdx in vendorDirectories) {
        let vendor = vendorDirectories[vendorIdx];
        if (!vendor.startsWith(".")) {
            vendors.push(vendor);
        }
    }
    console.log("vendors", vendors);

    for (let idx in vendors) {
        let vendor = vendors[idx];
        let vendorPath = themeDirectory + vendor;
        let themes = fileSystem.readdirSync(vendorPath);
        console.log(
            "vendor : ",
            vendor,
            " / included themes : ",
            themes,
            " / vendorPath : ",
            vendorPath,
        );

        for (let themeIdx in themes) {
            let theme = themes[themeIdx];
            if (
                !theme.startsWith(".") &&
                theme !== "default" &&
                vendor !== "amuz"
            ) {
                let themeAppJsFile =
                    vendorPath + "/" + theme + "/resources/js/app.js";
                if (fileSystem.existsSync(themeAppJsFile)) {
                    amuzThemes["@" + theme] =
                        "amuz-themes/" +
                        vendor +
                        "/" +
                        theme +
                        "/resources/js/";
                    inputs.push(themeAppJsFile);
                    console.info(
                        theme +
                            "테마의 app.js 가 등록되었습니다. css 파일은 app.js 내에서 Import 해야합니다.",
                    );
                }
            }
        }
    }
}

await setInputs();
console.log("Target Aliases : ", amuzThemes);

export default ({ mode }) => {
    process.env = { ...process.env, ...loadEnv(mode, process.cwd()) };

    let config = {
        resolve: {
            alias: amuzThemes,
        },
        plugins: [
            laravel({
                input: inputs,
                refresh: true,
            }),
            vue({
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false,
                    },
                },
            }),
        ],

        build: {
            target: "esnext",
            chunkSizeWarningLimit: 1600,
            rollupOptions: {
                output: {
                    manualChunks(id) {
                        if (id.includes("node_modules")) {
                            return id
                                .toString()
                                .split("node_modules/")[1]
                                .split("/")[0]
                                .toString();
                        }
                    },
                },
            },
        },
    };

    if (process.env.VITE_APP_ENV === "local") {
        console.log("this is local mode");
        config["server"] = {
            secure: true,
            host: "localhost",
            https: false,
            hmr: {
                host: "localhost",
            },
        };
    } else {
        console.info(process.env.APP_ENV);
    }

    return defineConfig(config);
};
