import fs from 'fs';
import path from 'path';
import semver from 'semver';
import { fileURLToPath } from 'url';
import { dirname } from 'path';

// 현재 파일의 디렉토리 경로를 얻기 위해 __dirname을 구현
const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

const themesDir = path.join(__dirname, 'amuz-themes');
const packagesDir = path.join(__dirname, 'amuz-packages');
const rootPackageJsonPath = path.join(__dirname, 'package.json');
let rootPackageJson = JSON.parse(fs.readFileSync(rootPackageJsonPath, 'utf8'));

let mergedDependencies = {};
let mergedDevDependencies = {};

// 주어진 디렉토리 내의 모든 package.json 파일 처리
async function processDirectory(dir, callback, depth) {
    const entries = fs.readdirSync(dir, { withFileTypes: true });
    for (const entry of entries) {
        const entryPath = path.join(dir, entry.name);

        if (entry.isDirectory() && depth === 0) {
            console.log("디렉터리 체크 중 " + entryPath);
            await processDirectory(entryPath, callback, 1); // 재귀 뎁스를 테마 폴더까지만 체크
        } else if (entry.isFile() && entry.name === 'package.json') {
            const pkgJson = JSON.parse(fs.readFileSync(entryPath, 'utf8'));
            callback(pkgJson);
        }
    }
}

// dependencies 및 devDependencies 병합
function mergeDependencies(pkgJson) {
    const { dependencies, devDependencies } = pkgJson;
    mergeDependencySet(dependencies, mergedDependencies);
    mergeDependencySet(devDependencies, mergedDevDependencies);
}

// 이게 쉽지않네... 너무어렵네... 젠장...
function mergeDependencySet(dependencies, target) {
    if (!dependencies) return;
    Object.keys(dependencies).forEach(pkg => {
        let version = dependencies[pkg];
        let coercedVersion = semver.coerce(version);
        if(!coercedVersion) version = "0.0.0";
        else version = coercedVersion.version;

        let targetVersion = target[pkg];
        let coercedTargetVersion = semver.coerce(targetVersion);
        if(!coercedTargetVersion) targetVersion = "0.0.0";
        else targetVersion = coercedTargetVersion.version;

        if (!target[pkg] || semver.gt(version, targetVersion)) {
            target[pkg] = dependencies[pkg];
        }
    });
}

// 패키지는, 노바에서만 보통 작동하므로 굳이 필요없을거같다.
await processDirectory(themesDir, mergeDependencies, 0);
// await processDirectory(packagesDir, mergeDependencies);

// 루트 package.json 업데이트
rootPackageJson.dependencies = { ...rootPackageJson.dependencies, ...mergedDependencies };
rootPackageJson.devDependencies = { ...rootPackageJson.devDependencies, ...mergedDevDependencies };

// 루트 package.json 파일 저장
fs.writeFileSync(rootPackageJsonPath, JSON.stringify(rootPackageJson, null, 2));

console.log('Package.json files have been merged successfully.');
