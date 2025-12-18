<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    
    <xsl:output method="html" encoding="UTF-8"/>
    
    <xsl:template match="/">
        <html>
        <head>
            <title>Просроченные книги</title>
            <style>
                table { border-collapse: collapse; width: 100%; }
                th, td { border: 1px solid #ccc; padding: 8px; }
                th { background: #007BFF; color: #fff; }
            </style>
        </head>
        <body>
            <h2>Список просроченных книг</h2>
            <table>
                <tr>
                    <th>Инвентарный номер</th>
                    <th>Название</th>
                    <th>Автор</th>
                    <th>Читатель</th>
                    <th>Дата выдачи</th>
                </tr>
                <xsl:for-each select="/*/book">
                    <tr>
                        <td><xsl:value-of select="inventory_number"/></td>
                        <td><xsl:value-of select="title"/></td>
                        <td><xsl:value-of select="author"/></td>
                        <td><xsl:value-of select="reader_card"/></td>
                        <td><xsl:value-of select="date_taken"/></td>
                    </tr>
                </xsl:for-each>
            </table>
        </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
