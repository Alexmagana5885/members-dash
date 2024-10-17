import mysql.connector
from mysql.connector import Error
import random
from datetime import datetime, timedelta
import string

def random_string(length=8):
    """Generate a random string of fixed length."""
    letters = string.ascii_letters
    return ''.join(random.choice(letters) for i in range(length))

def random_date(start, end):
    """Generate a random datetime between `start` and `end`."""
    return start + timedelta(days=random.randint(0, (end - start).days))

def generate_random_entries(n):
    """Generate n random entries for the `personalmembership` table."""
    entries = []
    
    for _ in range(n):
        entry = {
            'name': random_string(10),
            'email': f"{random_string(5).lower()}@example.com",
            'phone': f"+254{random.randint(700000000, 799999999)}",
            'dob': random_date(datetime(1970, 1, 1), datetime(2000, 12, 31)).strftime('%Y-%m-%d'),
            'home_address': random_string(15),
            'passport_image': f"{random_string(8)}.jpg",
            'highest_degree': random.choice(['Bachelors', 'Masters', 'PhD']),
            'institution': random_string(12),
            'start_date': random_date(datetime(2010, 1, 1), datetime(2018, 12, 31)).strftime('%Y-%m-%d'),
            'graduation_year': random.randint(2015, 2023),
            'completion_letter': f"{random_string(8)}.pdf",
            'profession': random.choice(['Engineer', 'Doctor', 'Teacher', 'Lawyer']),
            'experience': random.randint(1, 30),
            'current_company': random_string(10),
            'position': random.choice(['Manager', 'Developer', 'Analyst']),
            'work_address': random_string(20),
            'payment_method': random.choice(['Mpesa', 'Card', 'PayPal']),
            'payment_code': random_string(10),
            'password': random_string(12),
            'registration_date': datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        }
        entries.append(entry)
    
    return entries

def insert_entries(entries):
    """Insert entries into the `personalmembership` table."""
    try:
        connection = mysql.connector.connect(
            host='localhost',  # replace with your host
            database='agldatabase',  # replace with your database name
            user='root',  # replace with your username
            password=''  # replace with your password
           
        )

        if connection.is_connected():
            cursor = connection.cursor()
            insert_query = """
            INSERT INTO personalmembership 
            (name, email, phone, dob, home_address, passport_image, highest_degree, 
            institution, start_date, graduation_year, completion_letter, profession, 
            experience, current_company, position, work_address, payment_method, 
            payment_code, password, registration_date)
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
            """

            for entry in entries:
                cursor.execute(insert_query, tuple(entry.values()))

            connection.commit()
            print(f"{cursor.rowcount} records inserted successfully into personalmembership table")

    except Error as e:
        print(f"Error while connecting to MySQL: {e}")

    finally:
        if connection.is_connected():
            cursor.close()
            connection.close()
            print("MySQL connection is closed")

if __name__ == "__main__":
    # Generate 100 random entries
    entries = generate_random_entries(100)

    # Insert generated entries into the database
    insert_entries(entries)
