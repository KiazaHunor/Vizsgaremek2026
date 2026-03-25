using FizzLigaQuiz.Data;
using FizzLigaQuiz.Dtos;
using FizzLigaQuiz.Migrations;
using FizzLigaQuiz.Models;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using static Microsoft.EntityFrameworkCore.DbLoggerCategory;


namespace FizzLigaQuiz.Controllers
{
    public class QuizController : ControllerBase
    {
        private readonly QuizDbContext _context;

        public QuizController(QuizDbContext context)
        {
            _context = context;
        }

        [HttpGet]
        public async Task<IActionResult> GetAll()
        {
            var kerdesek = await _context.Kerdesek
                .OrderByDescending(k => k.Id)
                .ToListAsync();

            return Ok(kerdesek);
        }

        [HttpPost]
        public async Task<IActionResult> Create([FromBody] CreateKerdesRequest request)
        {
            if (string.IsNullOrWhiteSpace(request.KerdesSzoveg))
                return BadRequest("A kérdés szövege kötelező.");

            var helyes = request.HelyesValasz.ToUpper();

            if (helyes != "A" && helyes != "B" && helyes != "C" && helyes != "D")
                return BadRequest("A helyes válasz csak A, B, C vagy D lehet.");

            var kerdes = new Kerdes
            {
                KerdesSzoveg = request.KerdesSzoveg,
                ValaszA = request.ValaszA,
                ValaszB = request.ValaszB,
                ValaszC = request.ValaszC,
                ValaszD = request.ValaszD,
                HelyesValasz = helyes,
                Kategoria = request.Kategoria,
                Nehezseg = request.Nehezseg,
                Aktiv = true,
                LetrehozasDatuma = DateTime.Now
            };

            _context.Kerdesek.Add(kerdes);
            await _context.SaveChangesAsync();

            return Ok(kerdes);
        }

        [HttpGet("today")]
        public async Task<IActionResult> GetTodayQuiz()
        {
            var today = DateTime.Today;

            var napiQuiz = await _context.NapiQuizok
                .Include(n => n.Kerdes)
                .FirstOrDefaultAsync(n => n.Datum.Date == today);

            if (napiQuiz == null || napiQuiz.Kerdes == null)
                return NotFound(new { message = "Nincs beállítva mai quiz." });

            return Ok(new
            {
                id = napiQuiz.Kerdes.Id,
                kerdesSzoveg = napiQuiz.Kerdes.KerdesSzoveg,
                valaszA = napiQuiz.Kerdes.ValaszA,
                valaszB = napiQuiz.Kerdes.ValaszB,
                valaszC = napiQuiz.Kerdes.ValaszC,
                valaszD = napiQuiz.Kerdes.ValaszD,
                kategoria = napiQuiz.Kerdes.Kategoria,
                nehezseg = napiQuiz.Kerdes.Nehezseg
            });
        }
        [HttpPost("check-answer")]
        public async Task<IActionResult> CheckAnswer([FromBody] CreateKerdesRequest request)
        {
            var kerdes = await _context.Kerdesek.FindAsync(request.KerdesId);

            if (kerdes == null)
                return NotFound("Kérdés nem található");

            var helyes = kerdes.HelyesValasz.ToUpper();
            var valasz = request.ValasztottValasz.ToUpper();

            bool helyesE = helyes == valasz;

            // opcionális mentés adatbázisba
            var valaszEntity = new QuizValasz
            {
                KerdesId = kerdes.Id,
                ValasztottValasz = valasz,
                Helyes = helyesE,
                ValaszDatuma = DateTime.Now
            };

            _context.QuizValaszok.Add(valaszEntity);
            await _context.SaveChangesAsync();

            return Ok(new
            {
                helyes = helyesE
            });
        }
    }
}
