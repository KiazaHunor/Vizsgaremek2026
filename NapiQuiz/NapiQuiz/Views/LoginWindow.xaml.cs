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
            string password = PasswordBox.Password.Trim();

            using var db = new QuizDbContext();

            var user = db.Users.FirstOrDefault(u =>
                u.Username == username &&
                u.Password == password);

            if (user != null)
            {
                LoggedInUser = user;
                DialogResult = true;
                Close();
            }
            else
            {
                MessageBox.Show("Hibás felhasználónév vagy jelszó.");
            }
        }
    }
}