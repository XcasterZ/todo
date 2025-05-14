import mysql from 'mysql2';
import dotenv from 'dotenv';

dotenv.config();

const connection = mysql.createConnection({
    host: process.env.DB_HOST,
    port: process.env.DB_PORT,
    user: process.env.DB_USERNAME,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_DATABASE
});

connection.connect((err) => {
    if (err) {
        console.error('ไม่สามารถเชื่อมต่อฐานข้อมูล:', err);
        return;
    }
    console.log('เชื่อมต่อฐานข้อมูลสำเร็จ!');
    
    connection.query('SHOW TABLES', (err, results) => {
        if (err) {
            console.error('ไม่สามารถดึงข้อมูลตารางได้:', err);
        } else {
            console.log('รายการตารางในฐานข้อมูล:');
            results.forEach(row => {
                console.log(Object.values(row)[0]);
            });
        }

        connection.end();
    });
});