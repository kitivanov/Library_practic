import config from '../config.js';

import soap from 'soap';

const WSDL_URL = `http://${config.php.host}:${config.php.port}/soap-server.php?wsdl`;

export async function getSoapClient() {
    return soap.createClientAsync(WSDL_URL);
}

export async function searchBooksByAuthor(author) {
    const client = await getSoapClient();
    const [result] = await client.searchBooksByAuthorAsync({ author });
    return Array.isArray(result.book) ? result.book : [result.book];
}

export async function registerLoan(inventory_number, reader_card) {
    const client = await getSoapClient();
    const [res] = await client.registerLoanAsync({ inventory_number, reader_card });
    return res;
}

export async function getBookByInventory(inventory_number) {
    const client = await getSoapClient();
    const [res] = await client.getBookByInventoryAsync({ inventory_number });
    return res;
}
