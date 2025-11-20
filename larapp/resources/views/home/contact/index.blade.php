  <x-home.layout :title="'Simas MTTG - Detail Masjid'">
	<x-home._navbar />
  <style>
    .contact-wrapper {
      max-width: 1000px;
      border-radius: 24px;
      background: #ffffff;
      box-shadow: 0 24px 60px rgba(0, 0, 0, 0.15);
      overflow: hidden;
    }

    .office-photo {
      background-image: url('{{ asset("images/nabawi.png") }}');
      background-size: cover;
      background-position: center;
      border-radius: 24px 0 0 24px;
      height: 100%;
      min-height: 320px;
    }

    @media (max-width: 767.98px) {
      .office-photo {
        border-radius: 24px 24px 0 0;
        min-height: 220px;
      }
    }

    .info-card {
      border-radius: 20px;
    }

    .jet-badge {
      border-radius: 18px;
      background: #2f6bff;
      color: #ffffff;
      padding: 0.75rem 1.25rem;
      box-shadow: 0 18px 40px rgba(0, 0, 0, 0.2);
      font-weight: 600;
      font-size: 0.95rem;
    }

    .jet-badge span {
      display: block;
      font-size: 0.75rem;
      font-weight: 400;
    }
    
    .label-muted {
      font-size: 0.85rem;
      font-weight: 500;
      color: #606b78;
      text-transform: uppercase;
      letter-spacing: 0.03em;
    }

    .form-control {
      background: #f4f6f9;
      border: none;
      border-radius: 12px;
      padding-block: 0.7rem;
    }

    .form-control:focus {
      box-shadow: 0 0 0 2px rgba(47, 107, 255, 0.25);
      background: #fdfefe;
    }
  </style>
  @include('home.contact._form')
	<x-home._footer />
</x-home.layout>