import mysql.connector
from fpdf import FPDF
import qrcode
import os

# Database connection
conn = mysql.connector.connect(
    host="your_db_host",
    user="your_db_user",
    password="your_db_password",
    database="your_db_name"
)
cursor = conn.cursor()

# Member email
member_email = 'maganaadmin@agl.or.ke'

# Query to fetch event and member data
query = """
    SELECT er.event_name, er.event_date, er.event_location, er.member_name, er.member_email,
           pm.passport_image, om.logo_image
    FROM event_registrations er
    LEFT JOIN personalmembership pm ON er.member_email = pm.email
    LEFT JOIN organizationmembership om ON er.member_email = om.organization_email
    WHERE er.member_email = %s
"""
cursor.execute(query, (member_email,))
data = cursor.fetchone()

if data:
    event_name, event_date, event_location, member_name, member_email, passport_image, logo_image = data

    # Determine whether to display member photo or organization logo
    image = passport_image if passport_image else logo_image

    # Create PDF
    pdf = FPDF('P', 'mm', [127, 178])  # Set custom page size
    pdf.add_page()

    header_image = '../assets/img/logo.png'  # Path to your logo
    page_width = 127  # Custom width

    # Set background color
    pdf.set_fill_color(195, 198, 214)
    pdf.rect(0, 0, 127, 178, 'F')  # Fill the entire page

    # Add header image and center it
    if os.path.exists(header_image):
        header_image_width = 50
        x_position = (page_width - header_image_width) / 2
        pdf.image(header_image, x_position, 5, header_image_width)

    pdf.ln(12)  # Add space after header

    # Add event name
    pdf.set_font('Arial', 'B', 16)
    pdf.cell(0, 10, event_name, 0, 1, 'C')

    pdf.ln(3)  # Add space

    # Add member name
    pdf.set_font('Arial', '', 12)
    pdf.cell(0, 10, f'Name: {member_name}', 0, 1, 'C')

    pdf.ln(12)  # Add space before the image

    # Add member photo or organization logo and center it
    if image and os.path.exists(image):
        image_width = 40
        x_position = (page_width - image_width) / 2
        pdf.image(image, x_position, 70, image_width)
        pdf.ln(60)  # Space below the image based on height
    else:
        pdf.cell(0, 10, 'Image not found.', 0, 1, 'C')

    # Add event date and location
    pdf.cell(0, 10, f'Event Date: {event_date}', 0, 1, 'C')
    pdf.ln(3)
    pdf.cell(0, 10, f'Location: {event_location}', 0, 1, 'C')

    # Add a new page for the QR code (back side)
    pdf.add_page()

    # Generate QR code for user information
    qr_content = f"Member Name: {member_name}\nEvent: {event_name}\nDate: {event_date}\nLocation: {event_location}\nEmail: {member_email}"
    qr_file = '../assets/qr/temp_qrcode.png'

    # Generate and save QR code
    qr_img = qrcode.make(qr_content)
    qr_img.save(qr_file)

    # Add QR code image to the new page
    if os.path.exists(qr_file):
        qr_image_width = 60
        x_position = (page_width - qr_image_width) / 2
        pdf.image(qr_file, x_position, 60, qr_image_width)

    # Output the PDF in the browser or save it
    pdf.output('Invitation_Card.pdf')

    # Remove the generated QR code image
    if os.path.exists(qr_file):
        os.remove(qr_file)

else:
    print("No data found for the member email:", member_email)

# Close the database connection
conn.close()
