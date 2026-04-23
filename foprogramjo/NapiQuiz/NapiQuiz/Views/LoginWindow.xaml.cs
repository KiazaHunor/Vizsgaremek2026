using System.Windows;
using NapiQuiz.Services;
using NapiQuiz.Models;
namespace NapiQuiz.Views
{
    public partial class LoginWindow : Window
    {
        public User? LoggedInUser { get; private set; }
        private readonly ApiService _apiService = new ApiService();

        public LoginWindow()
        {
            InitializeComponent();
        }

        private async void Login_Click(object sender, RoutedEventArgs e)
        {
            string username = UsernameTextBox.Text.Trim();
            string password = PasswordBox.Password;

            if (string.IsNullOrWhiteSpace(username) || string.IsNullOrWhiteSpace(password))
            {
                MessageBox.Show("Tölts ki minden mezőt.");
                return;
            }

            try
            {
                var result = await _apiService.LoginAsync(username, password);

                if (result == null)
                {
                    MessageBox.Show("Nem érkezett válasz a szervertől.");
                    return;
                }

                if (!result.success || result.user == null)
                {
                    MessageBox.Show(result.error ?? "Hibás felhasználónév vagy jelszó.");
                    return;
                }

                LoggedInUser = result.user;
                DialogResult = true;
                Close();
            }
            catch (Exception ex)
            {
                MessageBox.Show("Hiba bejelentkezéskor:\n" + ex.Message);
            }
        }
    }
}