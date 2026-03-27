using FizzLigaQuiz.Data;
using FizzLigaQuiz.Dtos;
using FizzLigaQuiz.Models;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;



namespace FizzLigaQuiz.Controllers
{
    [ApiController]
    [Route("api/[controller]")]
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

            if (string.IsNullOrWhiteSpace(request.ValaszA) ||
                string.IsNullOrWhiteSpace(request.ValaszB) ||
                string.IsNullOrWhiteSpace(request.ValaszC) ||
                string.IsNullOrWhiteSpace(request.ValaszD))
            {
                return BadRequest("Mind a 4 válaszlehetőség megadása kötelező.");
            }

            var helyesValasz = request.HelyesValasz.Trim().ToUpper();

            if (helyesValasz != "A" && helyesValasz != "B" && helyesValasz != "C" && helyesValasz != "D")
                return BadRequest("A helyes válasz csak A, B, C vagy D lehet.");

            var kerdes = new Kerdes
            {
                KerdesSzoveg = request.KerdesSzoveg,
                ValaszA = request.ValaszA,
                ValaszB = request.ValaszB,
                ValaszC = request.ValaszC,
                ValaszD = request.ValaszD,
                HelyesValasz = helyesValasz,
                Kategoria = request.Kategoria,
                Nehezseg = request.Nehezseg,
                Aktiv = true,
                LetrehozasDatuma = DateTime.Now
            };

            _context.Kerdesek.Add(kerdes);
            await _context.SaveChangesAsync();

            return Ok(new
            {
                message = "Kérdés sikeresen létrehozva.",
                kerdes
            });
        }

        [HttpGet("today")]
        public async Task<IActionResult> GetTodayQuiz()
        {
            var today = DateTime.Today;

            var napiQuiz = await _context.NapiQuizok
                .Include(n => n.Kerdes)
                .FirstOrDefaultAsync(n => n.Datum.Date == today);

            if (napiQuiz == null || napiQuiz.Kerdes == null)
            {
                return NotFound(new
                {
                    message = "Nincs beállítva mai quiz."
                });
            }

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
        public async Task<IActionResult> CheckAnswer([FromBody] CheckAnswerRequest request)
        {
            if (string.IsNullOrWhiteSpace(request.ValasztottValasz))
                return BadRequest("A választott válasz kötelező.");

            var kerdes = await _context.Kerdesek.FindAsync(request.KerdesId);

            if (kerdes == null)
                return NotFound("Kérdés nem található.");

            var valasztott = request.ValasztottValasz.Trim().ToUpper();
            var helyesValasz = kerdes.HelyesValasz.Trim().ToUpper();

            if (valasztott != "A" && valasztott != "B" && valasztott != "C" && valasztott != "D")
                return BadRequest("A választott válasz csak A, B, C vagy D lehet.");

            bool helyes = valasztott == helyesValasz;

            var quizValasz = new QuizValasz
            {
                KerdesId = kerdes.Id,
                ValasztottValasz = valasztott,
                Helyes = helyes,
                FelhasznaloNev = request.FelhasznaloNev,
                ValaszDatuma = DateTime.Now
            };

            _context.QuizValaszok.Add(quizValasz);
            await _context.SaveChangesAsync();

            return Ok(new
            {
                helyes = helyes,
                helyesValasz = helyesValasz
            });
        }

        [HttpPost("set-daily")]
        public async Task<IActionResult> SetDailyQuiz([FromBody] SetDailyQuizRequest request)
        {
            var kerdes = await _context.Kerdesek.FindAsync(request.KerdesId);

            if (kerdes == null)
                return NotFound("Kérdés nem található.");

            var datum = request.Datum.Date;

            var letezoNapiQuiz = await _context.NapiQuizok
                .FirstOrDefaultAsync(n => n.Datum.Date == datum);

            if (letezoNapiQuiz != null)
            {
                letezoNapiQuiz.KerdesId = request.KerdesId;
            }
            else
            {
                var napiQuiz = new NapiQuiz
                {
                    Datum = datum,
                    KerdesId = request.KerdesId
                };

                _context.NapiQuizok.Add(napiQuiz);
            }

            await _context.SaveChangesAsync();

            return Ok(new
            {
                message = "Napi quiz sikeresen beállítva."
            });
        }

        [HttpGet("stats")]
        public async Task<IActionResult> GetStats()
        {
            var osszesValasz = await _context.QuizValaszok.CountAsync();
            var helyesValaszok = await _context.QuizValaszok.CountAsync(v => v.Helyes);
            var helytelenValaszok = await _context.QuizValaszok.CountAsync(v => !v.Helyes);

            return Ok(new
            {
                osszesValasz,
                helyesValaszok,
                helytelenValaszok
            });
        }
    }
}
