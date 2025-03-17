# Woocommerce Garanti BBVA Ödeme Modülü Entegrasyonu

Woocommerce, e-ticaret siteleri için popüler bir açık kaynaklı platformdur. Garanti BBVA ödeme modülü ile müşterilerinize güvenli ödeme seçenekleri sunabilirsiniz. Aşağıda, Garanti BBVA modül kurulum sürecini adım adım anlatan bir kılavuz bulunmaktadır.

## EKLENTİ İNDİRME

[Releases](https://github.com/eticsoft/garantibbva-woocommerce-module/releases) sayfasına giderek en son sürümü seçin ardından garantibbva.zip adlı dosyayı indirebilirsiniz.

![Woocommerce eklenti indirme](https://cdn.paythor.com/3/103/installation/3.png)

## EKLENTİ YÜKLEME

1. Woocommerce yönetici panelinize giriş yapın.
2. Sol menüden **Eklentiler > Yeni eklenti ekle** sekmesine tıklayın.
3. Sayfanın üst bölümünde bulunan **Şimdi Kur** butonuna tıklayın.
4. Açılan pencerede, bilgisayarınıza indirdiğiniz Garanti BBVA Modülü ZIP dosyanızı seçin ve yüklemenin tamamlanmasını bekleyin. 
5. Tamamlandıktan sonra **Eklentiyi Etkinleştir** butonuna tıklayın.

![Woocommerce kurulum adım 1](https://cdn.paythor.com/3/103/installation/1.png)

### FTP Üzerinden Garanti BBVA Modülü Yükleme (Alternatif Yöntem)

Eğer yönetici paneli üzerinden yükleme başarısız olursa, modülü manuel olarak yüklemek için aşağıdaki adımları takip edin:

1. FileZilla veya benzeri bir FTP istemcisi kullanarak sunucunuza bağlanın.
2. `plugins` dizinine gidin (`/var/www/html/wp-content/plugins` veya `/public_html/wp-content/plugins`).
3. ZIP dosyanızı bilgisayarınıza çıkarın.
4. Çıkarılan `garantibbva` klasörünü `plugins` dizinine yükleyin.

![FTP kurulum görseli](https://cdn.paythor.com/3/103/installation/2.png)

5. Yönetici paneline giriş yaparak sol menüden **Eklentiler > Kurulu eklentiler** sekmesine tıklayın.
6. Garanti BBVA modülünü listeden bulun ve **Etkinleştir** butonuna tıklayın.

## AYARLARIN YAPILANDIRILMASI

1. Yönetici panelinden **Woocommerce > Ayarlar** sekmesine gidin.
2. Açılan sayfada **Ödemeler** butonuna tıklayın
3. Garanti BBVA modülünün yanındaki **Kurulumu tamamla** veya **Yönet** butonuna tıklayın.
4. **GarantiBBVA ödeme yöntemini etkinleştir/devre dışı bırak** seçeneğinin seçili olduğundan emin olun seçili değilse seçtikten sonra **Değişiklikleri Kaydet** butonuna tıklayın.
5. **GarantiBBVA Paneline Erişmek İçin Tıklayınız** butonuna tıklayarak Yapılandırma ayarları sayfasına yönlenebilirsiniz.
6. **Hesabınız yok mu? Kayıt olun** butonuna tıklayarak, ücretsiz bir şekilde hesap oluşturabilirsiniz. Oluşturduğunuz hesap ile panele erişim sağlayabilirsiniz.
7. Yapılandırma sayfasında **ÖDEME YÖNTEMİ** menüsünden Garanti BBVA tarafından iletilen bilgileri girin.
8. Yapılandırmaları girdikten sonra **Kaydet** butonuna basın.
9. Test siparişi oluşturarak Garanti BBVA ödeme işleminin sorunsuz çalıştığını doğrulayın.

## TEST AŞAMASI

1. **ÖDEME YÖNTEMİ** butonuna tıklayın.
2. **Test Modu** başlığının altında yer alan seçilebilir alanı **Test Modu** olarak seçin ve **Kaydet** butonuna tıklayın.
3. Sepetinize bir ürün ekleyin ve ödeme adımında **Kart ile Öde (GarantiBBVA)** seçeneğini seçin.
4. Açılan Pop-up ödeme sayfası üzerinde test kart bilgilerini giriş yapın ve ödemeyi tamamlayın.

Bu işlemlerden sonra problem yaşanır ise **DESTEK** butonuna tıklayarak destek ekibi ile iletişime geçebilirsiniz.
