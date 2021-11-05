<?php

class Rokudoku
{
    protected $imeIgraca, $brojPokusaja, $gameOver;
    protected $errorMsg;
    protected $stanjeIgre, $bojanje;
    
    function __construct()
	{
        $this->dobar = 0;
		$this->imeIgraca = false;
		$this->brojPokusaja = 0;
		$this->gameOver = false;
        $this->errorMsg = false;
        // Generiramo praznu matricu koju treba ispuniti 
        // i matricu bojanja brojeva
        $this->stanjeIgre = [];
        $this->bojanje = [];
        for ($i = 0; $i < 6; ++$i)
        {
            $this->stanjeIgre[$i] = [];
            $this->bojanje[$i] = [];
            for ($j = 0; $j < 6; ++$j)
            {
                if ($i === 0 && $j === 2)
                {
                    $this->stanjeIgre[$i][$j] = 4;
                    $this->bojanje[$i][$j] = 1;
                }
                elseif($i === 1 && $j === 3)
                {
                    $this->stanjeIgre[$i][$j] = 2;
                    $this->bojanje[$i][$j] = 1;
                }
                elseif($i === 1 && $j === 4)
                {
                    $this->stanjeIgre[$i][$j] = 3;
                    $this->bojanje[$i][$j] = 1;
                }
                elseif($i === 2 && $j === 0)
                {
                    $this->stanjeIgre[$i][$j] = 3;
                    $this->bojanje[$i][$j] = 1;
                }
                elseif($i === 2 && $j === 4)
                {
                    $this->stanjeIgre[$i][$j] = 6;
                    $this->bojanje[$i][$j] = 1;
                }
                elseif($i === 3 && $j === 1)
                {
                    $this->stanjeIgre[$i][$j] = 6;
                    $this->bojanje[$i][$j] = 1;
                }
                elseif($i === 3 && $j === 5)
                {
                    $this->stanjeIgre[$i][$j] = 2;
                    $this->bojanje[$i][$j] = 1;
                }
                elseif($i === 4 && $j === 1)
                {
                    $this->stanjeIgre[$i][$j] = 2;
                    $this->bojanje[$i][$j] = 1;
                }
                elseif($i === 4 && $j === 2)
                {
                    $this->stanjeIgre[$i][$j] = 1;
                    $this->bojanje[$i][$j] = 1;
                }
                elseif($i === 5 && $j === 3)
                {
                    $this->stanjeIgre[$i][$j] = 5;
                    $this->bojanje[$i][$j] = 1;
                }                    
                else
                {
                    $this->stanjeIgre[$i][$j] = 0;
                    $this->bojanje[$i][$j] = 0;
                }                    
            }                
        }                    
    }
    
    function ispisiFormuZaIme() //gotovo
	{
		// Ispisi formu koja ucitava imeIgraca, sprema u _POST
		?>

		<!DOCTYPE html>
		<html>
		<head>
			<meta charset="utf-8">
			<title>ROKUDOKU!</title>
		</head>
		<body>
            <h1> ROKUDOKU! </h1>
			<form method="post" action="<?php echo htmlentities( $_SERVER['PHP_SELF']); ?>">
				Unesite svoje ime: <input type="text" name="imeIgraca" />
				<button type="submit">Započni igru!</button>
			</form>

			<?php if( $this->errorMsg !== false ) echo '<p>Greška: ' . htmlentities( $this->errorMsg ) . '</p>'; ?>
		</body>
		</html>

		<?php
    }

    function get_imeIgraca() //gotovo
	{
		// Je li već definirano ime igrača?
		if( $this->imeIgraca !== false )
			return $this->imeIgraca;

		// Možda nam se upravo sad šalje ime igrača?
		if( isset( $_POST['imeIgraca'] ) )
		{
			// Šalje nam se ime igrača. Provjeri da li se sastoji samo od slova.
			if( !preg_match( '/^[a-zA-Z]{1,20}$/', $_POST['imeIgraca'] ) )
			{
				// Nije dobro ime. Dakle nemamo ime igrača.
				$this->errorMsg = 'Ime igrača treba imati između 1 i 20 slova.';
				return false;
			}
			else
			{
				// Dobro je ime. Spremi ga u objekt.
				$this->imeIgraca = $_POST['imeIgraca'];
				return $this->imeIgraca;
			}
		}

		// Ne šalje nam se sad ime. Dakle nemamo ga uopće.
		return false;
	}

    function ispisiFormuZaRokudoku( )
    {
        // Ispisuje formu za igru + poruku o prethodnom pokušaju.

		// Povećaj brojač pokušaja -- brojim sad i neuspješne pokušaje.
		++$this->brojPokusaja;

		?>
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset="utf-8">
            <title>ROKUDOKU</title>
            <style>
                table, td
                {
                    border-collapse: collapse;                        
                }
                table
                {
                    border: solid 3px;
                }
                td
                {
                    border: solid 1px;
                    padding: 10px 15px;
                    text-align: center;
                    font-size: large;
                }
                td:first-child
                {
                    border-left:solid 3px;
                }
                td:nth-child(3n)
                {
                    border-right:solid 3px;
                }
                tr:first-child
                {
                    border-top:solid 3px;
                }
                tr:nth-child(2n) td
                {
                    border-bottom:solid 3px;
                }
                
            </style>
		</head>
		<body>
            <h1> Rokudoku! </h1>
			<p>
				Igrač: <?php echo htmlentities( $this->imeIgraca ); ?>
                <br />
                Broj pokušaja: <?php echo $this->brojPokusaja; ?>
			</p>

        <p>        
        <table>
            <?php
            echo '<table style="width:30%">';
			for ($r = 0; $r < 6; ++$r)
			{
				echo "<tr>";
                for ($c = 0; $c < 6; ++$c)
                {
                    if ($this->bojanje[$r][$c] === 0)
                        $boja = 'white';
                    if ($this->bojanje[$r][$c] === 1)
                        $boja = 'black';
                    if ($this->bojanje[$r][$c] === 2)
                        $boja = 'blue';
                    if ($this->bojanje[$r][$c] === 3)
                        $boja = 'red';
                    echo '<td>';
                    echo '<label style="color: ' . $boja . '; ">';
                    if ($this->bojanje[$r][$c] === 1)
                        echo '<b>';
                    echo $this->stanjeIgre[$r][$c];
                    if ($this->bojanje[$r][$c] === 1)
                        echo '</b>';
                    echo '</label>';
                    echo '</td>';
                }	
                echo "</tr>";
            }
            ?>
         </table>			 			
        </p>

			<form method="post" action="<?php echo htmlentities( $_SERVER['PHP_SELF']); ?>">
            <label for="unosBroja"><input type="radio" name="izborAkcije" id="unosBroja" value="unos" checked />Unesi broj 
        <input type="text" name="pokusaj">
        u redak 
        <select name="redakUnesi" id="redakUnesi">
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
			<option value="5">5</option>
			<option value="6">6</option>
		</select>
        i stupac
        <select name="stupacUnesi" id="stupacUnesi">
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
			<option value="5">5</option>
			<option value="6">6</option>
		</select>
        </label>
        <br>
        <label for="brisanjeBroja"><input type="radio" name="izborAkcije" id="brisanjeBroja" value="brisanje" />Obrisi broj iz retka
        <select name="redakObrisi" id="redakObrisi">
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
			<option value="5">5</option>
			<option value="6">6</option>
		</select>
        i stupca
        <select name="stupacObrisi" id="stupacObrisi">
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
			<option value="5">5</option>
			<option value="6">6</option>
		</select>
        </label>
        <br>
        <label for="ispocetka"><input type="radio" name="izborAkcije" id="ispocetka" value="ispocetka" />Želim sve ispočetka!</label>
        <br>
        <input type="submit" value="Izvrši akciju!">
			</form>

			<?php if( $this->errorMsg !== false ) echo '<p>Greška: ' . htmlentities( $this->errorMsg ) . '</p>'; ?>
        
           
        </body>
		</html>

		<?php
    }

    function isGameOver() { return $this->gameOver; } //gotovo

    function obradiPokusaj()
	{
		// Vraca false ako nije bio pokusaj pogadanja, ili je bio neispravan pokusaj pogadanja.
		// Inace, vraća 1 ako je unesen broj, 2 ako je broj obrisan, 3 ako se vraca na pocetak.

		// Da li je igrač uopće pokusao pogađati broj?
		if( isset( $_POST['izborAkcije'] ) )
		{
            if( $_POST['izborAkcije'] === "ispocetka")
            {
                return 3;
            }

            elseif( $_POST['izborAkcije'] === "brisanje")
            {
                $red = (int) $_POST['redakObrisi'];
                $stup = (int) $_POST['stupacObrisi'];

                if( !$this->dopustenBroj($red, $stup) )
                {
                    $this->errorMsg = 'Ne smijete mijenjati zadani broj.';
                    return false;
                }

                $this->stanjeIgre[$red-1][$stup-1] = 0;
                $this->bojanje[$red-1][$stup-1] = 0;
                $this->promjenaTablice();
                                            
                return 2;
            }

            elseif ($_POST['izborAkcije'] === "unos")
            {
                // Je. Da li je pokušaj broj između 1 i 6
                $options = array( 'options' => array( 'min_range' => 1, 'max_range' => 6 ) );
                if( filter_var( $_POST['pokusaj'], FILTER_VALIDATE_INT, $options ) === false )
			    {
				    // Nije unesen broj između 1 i 6.
				    $this->errorMsg = 'Trebate unijeti broj između 1 i 6.';
				    return false;
                }
                 
                $pokusaj = (int) $_POST['pokusaj'];
                $red = (int) $_POST['redakUnesi'];
                $stup = (int) $_POST['stupacUnesi'];

                // Da li je unesen broj na nedopusteno mjesto?
                if($this->dopustenBroj($red, $stup) === false)
                {
                    $this->errorMsg = 'Ne smijete mijenjati zadani broj.';
                    return false;
                }

                // Ako je unesen ispravan broj, unosimo ga u tablicu i bojamo.
                else
                {
                    $this->stanjeIgre[$red-1][$stup-1] = $pokusaj;
                    
                    // dobar broj bojamo u 2 = plavo
                    if ($this->dobarBroj($red, $stup, $pokusaj) === 1)
                    {
                        $this->bojanje[$red-1][$stup-1] = 2;
                        
                    }
                        
                    // los broj bojamo u 3 = crveno
                    elseif ($this->dobarBroj($red, $stup, $pokusaj) === -1)
                    {
                        $this->bojanje[$red-1][$stup-1] = 3;
                        
                    }
                        
                    $this->promjenaTablice();

                    return 1;
                }
                    
            }
		}

		// Igrač nije pokušao pogoditi broj.
		return false;
    }
    
    // Provjera da li je na željenoj poziciji zadana početna vrijednost tablice, takve ne smijemo mijenjati.
    function dopustenBroj($red, $stup)
    {
        if ($this->bojanje[$red-1][$stup-1] === 1)
            return false;
        return true;
    }
    // Provjera da li željeni broj zadovoljava pravila igre.
    function dobarBroj($red, $stup, $pokusaj)
    {
        for ($i = 0; $i < 6; ++$i)
        {
            if ($this->stanjeIgre[$red-1][$i] === $pokusaj && $i !== $stup-1)
                return -1;
            if ($this->stanjeIgre[$i][$stup-1] === $pokusaj && $i !== $red-1)
                return -1;
            if ($red === 1 || $red === 3 || $red === 5)
            {
                if ($stup === 1 || $stup === 2 || $stup === 3 )
                    for ($j = 0; $j < 3; ++$j) 
                        if ($this->stanjeIgre[$red][$j] === $pokusaj && $j !== $stup-1)
                            return -1;
                if ($stup === 4 || $stup === 5 || $stup === 6 )
                    for ($j = 3; $j < 6; ++$j) 
                        if ($this->stanjeIgre[$red][$j] === $pokusaj && $j !== $stup-1)
                            return -1;
                    
            }
            if ($red === 2 || $red === 4 || $red === 6)
            {
                if ($stup === 1 || $stup === 2 || $stup === 3 )
                    for ($j = 0; $j < 3; ++$j) 
                        if ($this->stanjeIgre[$red-2][$j] === $pokusaj && $j !== $stup-1)
                            return -1;
                if ($stup === 4 || $stup === 5 || $stup === 6 )
                    for ($j = 3; $j < 6; ++$j) 
                        if ($this->stanjeIgre[$red-2][$j] === $pokusaj && $j !== $stup-1)
                            return -1;
            }
        }
        return 1; 
    }
    // Nakon svakog unosa/brisanja brojeva provjeravamo da li neki vec uneseni vise ne krsi pravila
    // i u tom slucaju mijenjamo boju iz crvene u plavu.
    function promjenaTablice()
    {
        for ($i = 0; $i < 6; ++$i)
            for ($j = 0; $j < 6; ++$j)
                if ($this->bojanje[$i][$j] === 3)         
                    if ( $this->dobarBroj($i + 1, $j + 1, $this->stanjeIgre[$i][$j]) === 1 )            
                        $this->bojanje[$i][$j] = 2;
    }

    // Provjera da li je igrač točno ispunio cijelu tablicu.
    function rijeseno()
    {
        for ($i = 0; $i < 6; ++$i)
            for ($j = 0; $j < 6; ++$j)
                if ($this->bojanje[$i][$j] === 0 || $this->bojanje[$i][$j] === 3)
                    return false;
        return true;

    }

    // Ispisuje igraču prikladnu čestituku za uspješno rješavanje igre.
    function ispisiCestitku()
	{
		?>
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset="utf-8">
			<title>Rokudoku</title>
		</head>
		<body>
			<p>
				Bravo, <?php echo htmlentities( $this->imeIgraca ); ?>!
				<br />
				Riješili ste ROKUDOKU u samo <?php echo $this->brojPokusaja; ?> pokušaja!
			</p>
		</body>
		</html>

		<?php
	}

    function run() //a lot of work
    {
		// Funkcija obavlja "jedan potez" u igri.
		// Prvo, resetiraj poruke o greški.
		$this->errorMsg = false;

		// Prvo provjeri jel imamo uopće ime igraca
		if( $this->get_imeIgraca() === false )
		{
			// Ako nemamo ime igrača, ispiši formu za unos imena i to je kraj.
			$this->ispisiFormuZaIme();
			return;
        }         
           
        // Dakle imamo ime igrača.
		// Ako je igrač odigrao, provjerimo što se dogodilo s tim pokušajem.
        $rez = $this->obradiPokusaj();
        
        if( $rez === 1 && $this->rijeseno() === true )
		{
			// Ako je igrač pogodio, ispiši mu čestitku.
            $this->gameOver = true;
            $this->ispisiCestitku();
			
		}

        elseif ($rez === 3)
        {
            // Ako igrac zeli ponovno igrati, vrati ga na pocetnu stranu.
            $this->gameOver = true;
            $this->ispisiFormuZaIme();
        }

        else
            $this->ispisiFormuZaRokudoku( );	
    }

};

session_start(); //mislim da je gotovo

if( !isset( $_SESSION['igra'] ) )
{
    // Ako igra još nije započela, stvori novi objekt tipa Rokudoku i spremi ga u $_SESSION
    
	$igra = new Rokudoku(); 
	$_SESSION['igra'] = $igra;
}
else
{
	// Ako je igra već ranije započela, dohvati ju iz $_SESSION-a	
	$igra = $_SESSION['igra'];
}

// Izvedi jedan korak u igri, u kojoj god fazi ona bila.
$igra->run();

if( $igra->isGameOver() )
{
	// Kraj igre -> prekini session.
	session_unset();
	session_destroy();
}
else
{
	// Igra još nije gotova -> spremi trenutno stanje u SESSION
	$_SESSION['igra'] = $igra;	
}