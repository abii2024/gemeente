# Gemeente Portal - GitHub Copilot Agent

Een custom GitHub Copilot agent voor het Gemeente Portal project. Deze agent helpt ontwikkelaars met vragen over de codebase, architectuur, en functionaliteiten.

## 🎯 Wat Kan Deze Agent?

De Gemeente Copilot Agent kan je helpen met:
- 📚 Uitleg over de projectstructuur
- 🔍 Code navigatie en locaties
- 💡 Best practices voor het project
- 🐛 Debugging hulp
- 📖 Documentatie vragen
- 🚀 Feature implementatie tips

## 🛠️ Installatie

### 1. Zorg dat GitHub Copilot CLI geïnstalleerd is

```bash
# Installeer via npm (als nog niet gedaan)
npm install -g @githubnext/github-copilot-cli
```

### 2. Configureer de Agent

De agent configuratie staat in `.github/copilot-instructions.md`

## 💬 Gebruik

Open de GitHub Copilot Chat in VS Code en gebruik de agent:

```
@gemeente Waar staat de klacht indienen functionaliteit?
@gemeente Hoe werk ik met de database?
@gemeente Leg de authentication flow uit
@gemeente Waar vind ik de admin dashboard code?
```

## 📋 Voorbeeld Vragen

### Projectstructuur
- "Waar staan alle controllers?"
- "Welke routes zijn er beschikbaar?"
- "Hoe is de database gestructureerd?"

### Features
- "Hoe werk de klachten tracking?"
- "Waar staat de chatbot code?"
- "Hoe upload ik foto's bij een klacht?"

### Development
- "Hoe start ik de development server?"
- "Waar staan de tests?"
- "Hoe run ik de migrations?"

## 🔧 Agent Configuratie

De agent heeft toegang tot:
- Volledige codebase in `/Users/abdisamadabdulle/Herd/Gemeente`
- Alle documentatie in `docs/`
- README en setup guides
- Database schema (ERD)
- API endpoints

## 📚 Resources

- [Main Documentation](./docs/)
- [WAAR_IS_ALLES.md](./WAAR_IS_ALLES.md) - Complete navigatie gids
- [ONTWERPEN.md](./docs/ONTWERPEN.md) - ERD, Klassendiagram, Use Cases
- [API_ENDPOINTS_COMPLETE.md](./API_ENDPOINTS_COMPLETE.md)

## 🎨 Features van de Agent

1. **Context-Aware**: Begrijpt de Laravel structuur
2. **Project-Specific**: Kent alle custom implementations
3. **Up-to-date**: Gebruikt actuele project documentatie
4. **Multi-lingual**: Werkt in Nederlands en Engels
