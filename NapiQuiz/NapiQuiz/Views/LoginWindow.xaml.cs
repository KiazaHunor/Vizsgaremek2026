using System.Linq;
using System.Windows;
using NapiQuiz.Data;
using NapiQuiz.Models;

namespace NapiQuiz.Views
{
    public partial class LoginWindow : Window
    {
        public User? LoggedInUser { get; private set; }

        public LoginWindow()
        {
            InitializeComponent();
        }

        private void Login_Click(object sender, RoutedEventArgs e)
        {
            string username = UsernameTextBox.Text.Trim();
            string password = PasswordBox.Password;

            if (string.IsNullOrWhiteSpace(username) || string.IsNullOrWhiteSpace(password))
            {
                MessageBox.Show("Tölts ki minden mezőt.");
                return;
            }

            using var db = new QuizDbContext();

            var user = db.Users.FirstOrDefault(u => u.Username == username);

            if (user == null)
            {
                MessageBox.Show("Hibás felhasználónév vagy jelszó.");
                return;
            }

            if (!user.EmailVerified)
            {
                MessageBox.Show("Az email címed még nincs megerősítve.");
                return;
            }

            bool validPassword = false;

            try
            {
                validPassword = BCrypt.Net.BCrypt.Verify(password, user.Password);
            }
            catch
            {
                validPassword = false;
            }

            if (!validPassword)
            {
                MessageBox.Show("Hibás felhasználónév vagy jelszó.");
                return;
            }

            LoggedInUser = user;
            DialogResult = true;
            Close();
        }
    }
}