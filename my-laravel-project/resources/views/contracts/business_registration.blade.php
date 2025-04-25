<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zakelijk Contract - De Bazaar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #2b5797;
            margin-bottom: 5px;
        }
        .contract-number {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }
        .content {
            margin-bottom: 30px;
        }
        .content h2 {
            color: #2b5797;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .details {
            margin: 20px 0;
        }
        .details p {
            margin: 5px 0;
        }
        .signature {
            margin-top: 50px;
            page-break-inside: avoid;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 250px;
            margin-top: 70px;
            margin-bottom: 10px;
        }
        .date {
            margin-top: 20px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>De Bazaar</h1>
        <div class="contract-number">Contract #: {{ $contractNumber }}</div>
    </div>

    <div class="content">
        <h2>Zakelijk Gebruikerscontract</h2>
        
        <p>Dit contract is opgemaakt op {{ $date }} tussen De Bazaar (hierna "Platform") en:</p>
        
        <div class="details">
            <p><strong>Bedrijfsnaam:</strong> {{ $user->name }}</p>
            <p><strong>E-mailadres:</strong> {{ $user->email }}</p>
            <p><strong>Registratiedatum:</strong> {{ $user->created_at->format('d-m-Y') }}</p>
            <p><strong>Type gebruiker:</strong> Zakelijke adverteerder</p>
        </div>
        
        <h3>1. Algemene voorwaarden</h3>
        <p>Door dit contract te ondertekenen, gaat de zakelijke gebruiker akkoord met de volgende algemene voorwaarden voor het gebruik van het De Bazaar platform:</p>
        <ul>
            <li>De zakelijke gebruiker verklaart alle gegevens naar waarheid te hebben ingevuld.</li>
            <li>De zakelijke gebruiker zal zich houden aan alle geldende wet- en regelgeving bij het plaatsen van advertenties.</li>
            <li>Het platform behoudt zich het recht voor om advertenties te weigeren die niet voldoen aan de richtlijnen.</li>
            <li>De zakelijke gebruiker is verantwoordelijk voor de juistheid van de informatie in zijn/haar advertenties.</li>
        </ul>
        
        <h3>2. Betalingsvoorwaarden</h3>
        <p>Voor zakelijke gebruikers gelden de volgende betalingsvoorwaarden:</p>
        <ul>
            <li>Maandelijkse bijdrage van €XX,XX (exclusief BTW) voor het gebruik van het platform.</li>
            <li>Kosten per geplaatste advertentie volgens het geldende tarievenschema.</li>
            <li>Betaling dient binnen 14 dagen na factuurdatum te geschieden.</li>
        </ul>
        
        <h3>3. Looptijd en opzegging</h3>
        <p>Dit contract heeft een minimale looptijd van 3 maanden, waarna het maandelijks opzegbaar is met een opzegtermijn van 1 maand.</p>
        
        <h3>4. Privacy en gegevensbescherming</h3>
        <p>Het platform verwerkt de gegevens van de zakelijke gebruiker in overeenstemming met de privacyverklaring en de AVG regelgeving.</p>
    </div>

    <div class="date">
        <p>Datum: {{ $date }}</p>
    </div>
    
    <div class="signature">
        <div style="float: left; width: 45%;">
            <p>Namens De Bazaar:</p>
            <div class="signature-line"></div>
            <p>Naam: _______________________</p>
        </div>
        
        <div style="float: right; width: 45%;">
            <p>Namens {{ $user->name }}:</p>
            <div class="signature-line"></div>
            <p>Naam: _______________________</p>
        </div>
    </div>
</body>
</html>