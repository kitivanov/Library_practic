import express from 'express';
import { getBookByInventory, searchBooksByAuthor, registerLoan } from '../services/soapClient.js';

const router = express.Router();

router.get('/books', async (req, res) => {
    try {
        const author = req.query.author || '';
        const books = await searchBooksByAuthor(author);
        res.json(books);
    } catch (err) {
        console.error(err);
        res.status(500).json({ error: 'SOAP request failed' });
    }
});

router.post('/loan', async (req, res) => {
    try {
        const { inventory_number, reader_card } = req.body;
        const result = await registerLoan(inventory_number, reader_card);
        res.json(result);
    } catch (err) {
        console.error(err);
        res.status(500).json({ error: 'SOAP request failed' });
    }
});

export default router;
