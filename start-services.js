const { exec } = require('child_process');
const fs = require('fs');
const path = require('path');

const configPath = path.join(__dirname, 'config', 'config.json');
const config = JSON.parse(fs.readFileSync(configPath, 'utf-8'));

function startPHP(label, phpConfig) {
  const { host, port } = phpConfig;
  console.log(`${label} PHP сервер запущен на ${host}:${port}`);
  exec(`start "PHP ${label}" php -S ${host}:${port} -t ./legacy-php`);
}

function startNode(nodeConfig) {
  const { host, port } = nodeConfig;
  console.log(`Node сервер запущен на http://${host}:${port}`);
  exec(`start "Node" node modern/index.js`);
}

startPHP('SOAP', config.php);
startPHP('Admin', config.admin);
startNode(config.node);
